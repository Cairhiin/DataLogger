<?php

namespace App\Services;

use App\Models\LogEntry;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Illuminate\Support\Str;

class RabbitMQService
{
    public function publish($message, $key)
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->exchange_declare(env('MQ_EXCHANGE'), 'direct', false, false, false);
        $channel->queue_declare(env('MQ_QUEUE'), false, true, false, false);
        $channel->queue_bind(env('MQ_QUEUE'), env('MQ_EXCHANGE'), $key);

        if ($key == 'url') {
            $message = unserialize($message);
            $message += array('id' => Str::orderedUuid()->toString());
            $message = serialize($message);
        }

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, env('MQ_EXCHANGE'), $key);
        echo " [x] Sent $message\n";
        $channel->close();
        $connection->close();
    }

    public function consume()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->queue_declare(env('MQ_QUEUE'), false, true, false, false);

        $callback = function ($msg) {
            $condition = unserialize($msg->body);

            if (!$condition) {
                $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            } else {
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

                $data = unserialize($msg->body);
                $routingKey = $msg->getRoutingKey();
                if ($routingKey == "log") {
                    LogEntry::create($data);
                } else if ($routingKey == "url") {
                    Log::build([
                        'driver' => 'daily',
                        'path' => storage_path('logs/user-data.log'),
                    ])->info("URL event for route: {$data['route']} ###", $data);
                }
            }
        };

        $channel->basic_consume(env('MQ_QUEUE'), '', false, false, false, false, $callback);
        echo 'Waiting for new message on ' . env('MQ_QUEUE'), " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
