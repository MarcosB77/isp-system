<?php
namespace App\Http\Controllers;

use App\Domains\Client\Models\Client;
use App\Domains\Support\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request, Client $client): JsonResponse
    {
        $data = $request->validate([
            'subject'     => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority'    => ['nullable', 'in:low,medium,high,critical'],
        ]);

        $ticket = $client->tickets()->create($data);
        return response()->json($ticket, 201);
    }

    public function resolve(Ticket $ticket): JsonResponse
    {
        $ticket->resolve();
        return response()->json(['message' => 'Chamado resolvido.']);
    }
}
