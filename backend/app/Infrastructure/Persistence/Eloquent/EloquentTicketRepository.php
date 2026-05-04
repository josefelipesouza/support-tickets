<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Ticket\Entities\Ticket as TicketEntity;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Models\Ticket as TicketModel;

class EloquentTicketRepository implements TicketRepositoryInterface
{
    public function save(TicketEntity $ticket): TicketEntity
    {
        $model = TicketModel::create([
            'id'              => $ticket->id,
            'title'           => $ticket->title,
            'description'     => $ticket->description,
            'priority'        => $ticket->priority,
            'status'          => $ticket->status,
            'requester_email' => $ticket->requesterEmail,
        ]);

        return $this->toEntity($model);
    }

    public function findById(string $id): ?TicketEntity
    {
        $model = TicketModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(): array
    {
        return TicketModel::orderBy('created_at', 'desc')
            ->get()
            ->map(fn($m) => $this->toEntity($m))
            ->toArray();
    }

    public function update(TicketEntity $ticket): TicketEntity
    {
        $model = TicketModel::findOrFail($ticket->id);
        $model->update(['status' => $ticket->status]);
        return $this->toEntity($model->fresh());
    }

    private function toEntity(TicketModel $model): TicketEntity
    {
        return new TicketEntity(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            priority: $model->priority,
            status: $model->status,
            requesterEmail: $model->requester_email,
            createdAt: $model->created_at
                ? \DateTimeImmutable::createFromMutable($model->created_at->toDateTime())
                : null,
            updatedAt: $model->updated_at
                ? \DateTimeImmutable::createFromMutable($model->updated_at->toDateTime())
                : null,
        );
    }
}