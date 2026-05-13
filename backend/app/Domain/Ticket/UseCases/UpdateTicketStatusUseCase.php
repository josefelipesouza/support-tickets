<?php
namespace App\Domain\Ticket\UseCases;

use App\Domain\Ticket\Entities\Ticket;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Infrastructure\Messaging\RabbitMQPublisher;

class UpdateTicketStatusUseCase
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository,
        private readonly RabbitMQPublisher $publisher,
    ) {}

    public function execute(string $id, string $status): Ticket
    {
        $ticket = $this->repository->findById($id);

        if (!$ticket) {
            throw new \DomainException("Ticket not found: {$id}");
        }

        $updated = $ticket->withStatus($status);
        $saved = $this->repository->update($updated);

        $this->publisher->publish('ticket.status_updated', [
            'id'         => $saved->id,
            'title'      => $saved->title,
            'status'     => $saved->status,
            'priority'   => $saved->priority,
            'requester_email' => $saved->requesterEmail,
            'updated_at' => $saved->updatedAt?->format('Y-m-d H:i:s'),
        ]);

        return $saved;
    }
}

