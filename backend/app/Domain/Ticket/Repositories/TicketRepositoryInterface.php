<?php

namespace App\Domain\Ticket\Repositories;

use App\Domain\Ticket\Entities\Ticket;

interface TicketRepositoryInterface
{
    public function save(Ticket $ticket): Ticket;
    public function findById(string $id): ?Ticket;
    public function findAll(): array;
    public function update(Ticket $ticket): Ticket;
}