<?php
namespace App\Domains\Client\Services;

use App\Domains\Client\DTOs\CreateClientDTO;
use App\Domains\Client\Events\ClientCreated;
use App\Domains\Client\Events\ClientSuspended;
use App\Domains\Client\Models\Client;
use App\Infrastructure\External\MikrotikService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientService
{
    public function __construct(
        private MikrotikService $mikrotik
    ) {}

    public function create(CreateClientDTO $dto): Client
    {
        return DB::transaction(function () use ($dto) {
            $client = Client::create((array) $dto);
            event(new ClientCreated($client));
            Log::info("Cliente criado: {$client->id} — {$client->name}");
            return $client;
        });
    }

    public function suspend(Client $client): void
    {
        if ($client->isSuspended()) return;

        DB::transaction(function () use ($client) {
            $client->update(['status' => 'suspended']);

            if ($connection = $client->connection) {
                $this->mikrotik->suspendUser($connection->pppoe_username);
            }

            event(new ClientSuspended($client));
            Log::info("Cliente suspenso: {$client->id}");
        });
    }

    public function activate(Client $client): void
    {
        DB::transaction(function () use ($client) {
            $client->update(['status' => 'active']);

            if ($connection = $client->connection) {
                $this->mikrotik->activateUser($connection->pppoe_username);
            }

            Log::info("Cliente reativado: {$client->id}");
        });
    }
}
