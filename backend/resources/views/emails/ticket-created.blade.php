<x-mail::message>
# Chamado criado com sucesso ✅

Olá! Seu chamado foi registrado em nosso sistema.

<x-mail::panel>
**#{{ $ticket['id'] }}** — {{ $ticket['title'] }}
</x-mail::panel>

| Campo      | Valor |
|------------|-------|
| Prioridade | {{ ucfirst($ticket['priority']) }} |
| Status     | Aberto |
| Criado em  | {{ $ticket['created_at'] }} |

Acompanhe o andamento pelo nosso portal.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>