<?php
namespace Tests\Unit\Domain\Ticket;

use App\Domain\Ticket\Entities\Ticket;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Domain\Ticket\UseCases\UpdateTicketStatusUseCase;
use App\Infrastructure\Messaging\RabbitMQPublisher;
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

        $publisher = $this->createMock(RabbitMQPublisher::class);
        $publisher->expects($this->once())->method('publish');

        $useCase = new UpdateTicketStatusUseCase($repository, $publisher);
        $result  = $useCase->execute($ticket->id, 'resolved');

        $this->assertEquals('resolved', $result->status);
    }

    public function test_throws_exception_when_ticket_not_found(): void
    {
        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->method('findById')->willReturn(null);

        $publisher = $this->createMock(RabbitMQPublisher::class);

        $useCase = new UpdateTicketStatusUseCase($repository, $publisher);

        $this->expectException(\DomainException::class);
        $useCase->execute('invalid-id', 'resolved');
    }
}