<?php

namespace Tests\Unit\Domain\Ticket;

use App\Domain\Ticket\Entities\Ticket;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Domain\Ticket\UseCases\ListTicketsUseCase;
use PHPUnit\Framework\TestCase;

class ListTicketsUseCaseTest extends TestCase
{
    public function test_returns_all_tickets(): void
    {
        $tickets = [
            Ticket::create('T1', 'D1', 'low', 'a@a.com'),
            Ticket::create('T2', 'D2', 'high', 'b@b.com'),
        ];

        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->method('findAll')->willReturn($tickets);

        $useCase = new ListTicketsUseCase($repository);
        $result  = $useCase->execute();

        $this->assertCount(2, $result);
        $this->assertEquals('T1', $result[0]->title);
        $this->assertEquals('T2', $result[1]->title);
    }

    public function test_returns_empty_array_when_no_tickets(): void
    {
        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->method('findAll')->willReturn([]);

        $useCase = new ListTicketsUseCase($repository);
        $result  = $useCase->execute();

        $this->assertEmpty($result);
    }
}
