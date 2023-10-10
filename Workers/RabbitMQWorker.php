<?php

$upOne = realpath(__DIR__ . '/..');
require_once $upOne . '/vendor/autoload.php';

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$exchange = 'router';
$queue = 'msgs';
$consumerTag = 'consumer';
$connection = new AMQPStreamConnection('localhost', 5672, 'worthitIT', '2a55f70a841f1gh822baa97c3gga7dkkob9839b7adc9e34a0f1b', 'datalogger');
$channel = $connection->channel();

$channel->queue_declare($queue, true, true, false, false);
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
$channel->queue_bind($queue, $exchange);

/**
 * @param \PhpAmqpLib\Message\AMQPMessage $message
 */
function process_message($message)
{
    echo "\n--------\n";
    echo $message->body;
    echo "\n--------\n";

    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

    // Send a message with the string "quit" to cancel the consumer.
    if ($message->body === 'quit') {
        $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
    }
}

$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

/**
 * @param \PhpAmqpLib\Channel\AMQPChannel $channel
 * @param \PhpAmqpLib\Connection\AbstractConnection $connection
 */
function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown', $channel, $connection);

while ($channel->is_consuming()) {
    $channel->wait();
}
