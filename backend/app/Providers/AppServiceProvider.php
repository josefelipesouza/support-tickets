<?php

namespace App\Providers;

use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentTicketRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TicketRepositoryInterface::class,
            EloquentTicketRepository::class,
        );
    }

    public function boot(): void {}
}