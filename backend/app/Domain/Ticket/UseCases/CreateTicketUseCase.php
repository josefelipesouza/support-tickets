<?php

namespace App\Domain\Ticket\UseCases;

use App\Domain\Ticket\Entities\Ticket;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Infrastructure\Messaging\RabbitMQPublisher;

class CreateTicketUseCase
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository,
        private readonly RabbitMQPublisher $publisher,
    ) {}

    public function execute(
        string $title,
        string $description,
        string $priority,
        string $requesterEmail,
    ): Ticket {
        $ticket = Ticket::create(
            title: $title,
            description: $description,
            priority: $priority,
            requesterEmail: $requesterEmail,
        );

        $saved = $this->repository->save($ticket);

        $this->publisher->publish('ticket.created', [
            'id'              => $saved->id,
            'title'           => $saved->title,
            'priority'        => $saved->priority,
            'requester_email' => $saved->requesterEmail,
            'created_at'      => $saved->createdAt?->format('Y-m-d H:i:s'),
        ]);

        return $saved;
    }
}