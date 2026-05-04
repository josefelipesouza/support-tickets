<?php

namespace App\Infrastructure\Messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConsumer
{
    public function consume(string $queue, callable $callback): void
    {
        $connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost'),
        );

        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $channel->basic_qos(null, 1, null);

        $channel->basic_consume(
            $queue, '', false, false, false, false,
            function ($message) use ($channel, $callback) {
                $callback($message);
                $channel->basic_ack($message->delivery_info['delivery_tag']);
            }
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
