<?php

namespace App\Domain\Ticket\UseCases;

use App\Domain\Ticket\Repositories\TicketRepositoryInterface;

class ListTicketsUseCase
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository,
    ) {}

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}