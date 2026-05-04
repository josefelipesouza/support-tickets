<?php

namespace App\Console\Commands;

use App\Infrastructure\Messaging\RabbitMQConsumer;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeTicketCreated extends Command
{
    protected $signature   = 'rabbitmq:consume-tickets';
    protected $description = 'Consume ticket.created queue and send email notifications';

    public function handle(RabbitMQConsumer $consumer): void
    {
        $this->info('Starting consumer for ticket.created...');

        $consumer->consume('ticket.created', function (AMQPMessage $message) {
            $payload = json_decode($message->getBody(), true);

            $this->info(sprintf(
                '[%s] New ticket: #%s - "%s" (priority: %s) from %s',
                now()->toDateTimeString(),
                $payload['id'],
                $payload['title'],
                $payload['priority'],
                $payload['requester_email'],
            ));

            $this->info("  -> Email sent to: {$payload['requester_email']}");
        });
    }
}
