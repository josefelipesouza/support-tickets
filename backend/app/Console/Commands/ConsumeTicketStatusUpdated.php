<?php

namespace App\Console\Commands;

use App\Infrastructure\Messaging\RabbitMQConsumer;
use App\Mail\TicketStatusUpdatedMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeTicketStatusUpdated extends Command
{
    protected $signature   = 'rabbitmq:consume-ticket-status';
    protected $description = 'Consume ticket.status_updated queue and send email notifications';

    public function handle(RabbitMQConsumer $consumer): void
    {
        $this->info('Starting consumer for ticket.status_updated...');

        $consumer->consume('ticket.status_updated', function (AMQPMessage $message) {
            $payload = json_decode($message->getBody(), true);

            $this->info(sprintf(
                '[%s] Ticket #%s updated to status: %s',
                now()->toDateTimeString(),
                $payload['id'],
                $payload['status'],
            ));

            try {
                Mail::to($payload['requester_email'])
                    ->send(new TicketStatusUpdatedMail($payload));
                $this->info("  → Email sent to: {$payload['requester_email']}");
            } catch (\Throwable $e) {
                $this->error("  → Failed to send email: {$e->getMessage()}");
            }
        });
    }
}