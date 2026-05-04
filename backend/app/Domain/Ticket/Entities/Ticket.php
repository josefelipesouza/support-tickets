<?php

namespace App\Domain\Ticket\Entities;

use Ramsey\Uuid\Uuid;

class Ticket
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $priority,
        public readonly string $status,
        public readonly string $requesterEmail,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {}

    public static function create(
        string $title,
        string $description,
        string $priority,
        string $requesterEmail,
    ): self {
        return new self(
            id: Uuid::uuid4()->toString(),
            title: $title,
            description: $description,
            priority: $priority,
            status: 'open',
            requesterEmail: $requesterEmail,
        );
    }

    public function withStatus(string $status): self
    {
        return new self(
            id: $this->id,
            title: $this->title,
            description: $this->description,
            priority: $this->priority,
            status: $status,
            requesterEmail: $this->requesterEmail,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }
}