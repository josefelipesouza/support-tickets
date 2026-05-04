<?php

namespace App\Http\Schemas;

/**
 * @OA\Schema(
 *     schema="Ticket",
 *     @OA\Property(property="id", type="string", example="uuid-aqui"),
 *     @OA\Property(property="title", type="string", example="Servidor fora"),
 *     @OA\Property(property="description", type="string", example="O servidor caiu"),
 *     @OA\Property(property="priority", type="string", enum={"low","medium","high"}),
 *     @OA\Property(property="status", type="string", enum={"open","in_progress","resolved","closed"}),
 *     @OA\Property(property="requesterEmail", type="string", example="user@test.com"),
 *     @OA\Property(property="createdAt", type="string", example="2026-05-04 19:40:39")
 * )
 */
class TicketSchema {}
