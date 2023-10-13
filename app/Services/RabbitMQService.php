<?php

namespace App\Services;

use App\Models\LogEntry;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQService
{
    public function publish($message)
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->exchange_declare(env('MQ_EXCHANGE'), 'direct', false, false, false);
        $channel->queue_declare(env('MQ_QUEUE'), false, false, false, false);
        $channel->queue_bind(env('MQ_QUEUE'), env('MQ_EXCHANGE'), 'test_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, env('MQ_EXCHANGE'), 'test_key');
        echo " [x] Sent $message\n";
        $channel->close();
        $connection->close();
    }
    public function consume()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();

        $callback = function ($msg) {
            $data = unserialize($msg->body);
            LogEntry::create($data);
        };

        $channel->queue_declare(env('MQ_QUEUE'), false, false, false, false);
        $channel->basic_consume(env('MQ_QUEUE'), '', false, true, false, false, $callback);
        echo 'Waiting for new message on test_queue', " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
