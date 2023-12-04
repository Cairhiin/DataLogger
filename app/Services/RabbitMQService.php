<?php

namespace App\Services;

use App\Models\LogEntry;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Auth;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQService
{
    public function publish($message, $key)
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->exchange_declare(env('MQ_EXCHANGE'), 'direct', false, false, false);
        $channel->queue_declare(env('MQ_QUEUE'), false, true, false, false);
        $channel->queue_bind(env('MQ_QUEUE'), env('MQ_EXCHANGE'), $key);
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

        $callback = function ($msg) {
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
        };

        $channel->queue_declare(env('MQ_QUEUE'), false, true, false, false);
        $channel->basic_consume(env('MQ_QUEUE'), '', false, true, false, false, $callback);
        echo 'Waiting for new message on ' . env('MQ_QUEUE'), " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
