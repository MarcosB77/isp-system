<?php
namespace App\Application\UseCases\Client;

use App\Domains\Client\Models\Client;
use App\Domains\Client\Services\ClientService;

class SuspendClientUseCase
{
    public function __construct(private ClientService $clientService) {}

    public function execute(Client $client): void
    {
        $this->clientService->suspend($client);
    }
}
