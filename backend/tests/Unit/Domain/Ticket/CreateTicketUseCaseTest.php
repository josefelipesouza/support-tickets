<?php

namespace Tests\Unit\Domain\Ticket;

use App\Domain\Ticket\Entities\Ticket;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Domain\Ticket\UseCases\CreateTicketUseCase;
use App\Infrastructure\Messaging\RabbitMQPublisher;
use PHPUnit\Framework\TestCase;

class CreateTicketUseCaseTest extends TestCase
{
    public function test_creates_ticket_with_open_status(): void
    {
        $ticket = Ticket::create('Servidor fora', 'Descricao', 'high', 'user@test.com');

        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->willReturn($ticket);

        $publisher = $this->createMock(RabbitMQPublisher::class);
        $publisher->expects($this->once())
            ->method('publish')
            ->with('ticket.created', $this->arrayHasKey('id'));

        $useCase = new CreateTicketUseCase($repository, $publisher);
        $result  = $useCase->execute('Servidor fora', 'Descricao', 'high', 'user@test.com');

        $this->assertEquals('open', $result->status);
        $this->assertEquals('Servidor fora', $result->title);
        $this->assertEquals('high', $result->priority);
    }

    public function test_publishes_message_to_rabbitmq(): void
    {
        $ticket = Ticket::create('Test', 'Desc', 'low', 'a@b.com');

        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->method('save')->willReturn($ticket);

        $publisher = $this->createMock(RabbitMQPublisher::class);
        $publisher->expects($this->once())
            ->method('publish')
            ->with('ticket.created');

        $useCase = new CreateTicketUseCase($repository, $publisher);
        $useCase->execute('Test', 'Desc', 'low', 'a@b.com');
    }
}
