<?php

namespace App\Domain\Ticket\UseCases;

use App\Domain\Ticket\Entities\Ticket;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;

class UpdateTicketStatusUseCase
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository,
    ) {}

    public function execute(string $id, string $status): Ticket
    {
        $ticket = $this->repository->findById($id);

        if (!$ticket) {
            throw new \DomainException("Ticket not found: {$id}");
        }

        $updated = $ticket->withStatus($status);

        return $this->repository->update($updated);
    }
}