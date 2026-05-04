<?php

namespace Tests\Unit\Domain\Ticket;

use App\Domain\Ticket\Entities\Ticket;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Domain\Ticket\UseCases\UpdateTicketStatusUseCase;
use PHPUnit\Framework\TestCase;

class UpdateTicketStatusUseCaseTest extends TestCase
{
    public function test_updates_ticket_status(): void
    {
        $ticket  = Ticket::create('Titulo', 'Desc', 'medium', 'x@x.com');
        $updated = $ticket->withStatus('resolved');

        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->method('findById')->willReturn($ticket);
        $repository->method('update')->willReturn($updated);

        $useCase = new UpdateTicketStatusUseCase($repository);
        $result  = $useCase->execute($ticket->id, 'resolved');

        $this->assertEquals('resolved', $result->status);
    }

    public function test_throws_exception_when_ticket_not_found(): void
    {
        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->method('findById')->willReturn(null);

        $useCase = new UpdateTicketStatusUseCase($repository);

        $this->expectException(\DomainException::class);
        $useCase->execute('invalid-id', 'resolved');
    }
}
