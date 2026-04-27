<?php
namespace App\Application\UseCases\Client;

use App\Domains\Client\DTOs\CreateClientDTO;
use App\Domains\Client\Models\Client;
use App\Domains\Client\Services\ClientService;

class CreateClientUseCase
{
    public function __construct(
        private ClientService $clientService
    ) {}

    public function execute(array $data): Client
    {
        $dto = CreateClientDTO::fromArray($data);
        return $this->clientService->create($dto);
    }
}
