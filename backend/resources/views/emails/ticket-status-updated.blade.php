<x-mail::message>
# Status do chamado atualizado 🔄

Olá! O status do seu chamado foi atualizado.

<x-mail::panel>
**#{{ $ticket['id'] }}** — {{ $ticket['title'] }}
</x-mail::panel>

| Campo         | Valor |
|---------------|-------|
| Novo Status   | {{ ucfirst(str_replace('_', ' ', $ticket['status'])) }} |
| Prioridade    | {{ ucfirst($ticket['priority']) }} |
| Atualizado em | {{ $ticket['updated_at'] }} |

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>