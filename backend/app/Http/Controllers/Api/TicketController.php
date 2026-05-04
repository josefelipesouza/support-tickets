<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticket\UseCases\CreateTicketUseCase;
use App\Domain\Ticket\UseCases\ListTicketsUseCase;
use App\Domain\Ticket\UseCases\UpdateTicketStatusUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Support Tickets API",
 *     version="1.0.0",
 *     description="API para gerenciamento de chamados de suporte"
 * )
 * @OA\Server(url="/api")
 */
class TicketController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tickets",
     *     summary="Listar todos os chamados",
     *     tags={"Tickets"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de chamados",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Ticket"))
     *     )
     * )
     */
    public function index(ListTicketsUseCase $useCase): JsonResponse
    {
        return response()->json($useCase->execute());
    }

    /**
     * @OA\Post(
     *     path="/tickets",
     *     summary="Criar novo chamado",
     *     tags={"Tickets"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","description","priority","requester_email"},
     *             @OA\Property(property="title", type="string", example="Servidor fora"),
     *             @OA\Property(property="description", type="string", example="O servidor caiu"),
     *             @OA\Property(property="priority", type="string", enum={"low","medium","high"}, example="high"),
     *             @OA\Property(property="requester_email", type="string", example="user@test.com")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Chamado criado", @OA\JsonContent(ref="#/components/schemas/Ticket")),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request, CreateTicketUseCase $useCase): JsonResponse
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'priority'        => 'required|in:low,medium,high',
            'requester_email' => 'required|email',
        ]);

        $ticket = $useCase->execute(
            title: $data['title'],
            description: $data['description'],
            priority: $data['priority'],
            requesterEmail: $data['requester_email'],
        );

        return response()->json($ticket, 201);
    }

    /**
     * @OA\Get(
     *     path="/tickets/{id}",
     *     summary="Buscar chamado por ID",
     *     tags={"Tickets"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Chamado encontrado", @OA\JsonContent(ref="#/components/schemas/Ticket")),
     *     @OA\Response(response=404, description="Chamado não encontrado")
     * )
     */
    public function show(string $id, \App\Domain\Ticket\Repositories\TicketRepositoryInterface $repository): JsonResponse
    {
        $ticket = $repository->findById($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        return response()->json($ticket);
    }

    /**
     * @OA\Patch(
     *     path="/tickets/{id}/status",
     *     summary="Atualizar status do chamado",
     *     tags={"Tickets"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"open","in_progress","resolved","closed"}, example="resolved")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status atualizado", @OA\JsonContent(ref="#/components/schemas/Ticket")),
     *     @OA\Response(response=404, description="Chamado não encontrado")
     * )
     */
    public function updateStatus(Request $request, string $id, UpdateTicketStatusUseCase $useCase): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        try {
            $ticket = $useCase->execute($id, $data['status']);
            return response()->json($ticket);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
