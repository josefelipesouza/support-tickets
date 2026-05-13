<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use App\Domain\Ticket\Repositories\TicketRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentTicketRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TicketRepositoryInterface::class, EloquentTicketRepository::class);
    }

    public function boot(): void
    {
        Mail::extend('smtp', function (array $config) {
            $transport = new EsmtpTransport(
                $config['host'],
                $config['port'],
                true
            );
            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);

            /** @var SocketStream $stream */
            $stream = $transport->getStream();
            $stream->setStreamOptions([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ]);

            return $transport;
        });
    }
}