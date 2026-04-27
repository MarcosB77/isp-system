<?php
namespace App\Http\Controllers;

use App\Application\UseCases\Client\ActivateClientUseCase;
use App\Application\UseCases\Client\CreateClientUseCase;
use App\Application\UseCases\Client\SuspendClientUseCase;
use App\Domains\Client\Models\Client;
use App\Http\Requests\StoreClientRequest;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function __construct(
        private CreateClientUseCase  $createUseCase,
        private SuspendClientUseCase $suspendUseCase,
        private ActivateClientUseCase $activateUseCase,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            Client::with('activeContract.plan', 'connection')->paginate(20)
        );
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->createUseCase->execute($request->validated());
        return response()->json($client, 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json(
            $client->load('contracts.plan', 'invoices', 'connection', 'tickets')
        );
    }

    public function suspend(Client $client): JsonResponse
    {
        $this->suspendUseCase->execute($client);
        return response()->json(['message' => 'Cliente suspenso com sucesso.']);
    }

    public function activate(Client $client): JsonResponse
    {
        $this->activateUseCase->execute($client);
        return response()->json(['message' => 'Cliente reativado com sucesso.']);
    }
}
