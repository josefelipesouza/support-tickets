<?php

namespace App\Infrastructure\Messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher
{
    public function publish(string $queue, array $payload): void
    {
        $connection = new AMQPStreamConnection(
            host: config('rabbitmq.host'),
            port: config('rabbitmq.port'),
            user: config('rabbitmq.user'),
            password: config('rabbitmq.password'),
            vhost: config('rabbitmq.vhost'),
        );

        $channel = $connection->channel();

        $channel->queue_declare(
            queue: $queue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false,
        );

        $message = new AMQPMessage(
            body: json_encode($payload),
            properties: ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT],
        );

        $channel->basic_publish(
            msg: $message,
            exchange: '',
            routing_key: $queue,
        );

        $channel->close();
        $connection->close();
    }
}