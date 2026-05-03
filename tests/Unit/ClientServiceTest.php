<?php
namespace Tests\Unit;

use App\Domains\Client\Models\Client;
use App\Domains\Client\Services\ClientService;
use App\Infrastructure\External\MikrotikService;
use App\Domains\Client\DTOs\CreateClientDTO;
use App\Domains\Client\Events\ClientCreated;
use App\Domains\Client\Events\ClientSuspended;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ClientServiceTest extends TestCase
{
    use RefreshDatabase;

    private ClientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientService(new MikrotikService());
    }

    public function test_cria_cliente_e_dispara_evento(): void
    {
        Event::fake();
        $dto = new CreateClientDTO(name: "João Silva", email: "joao@test.com", cpf: "12345678901");
        $client = $this->service->create($dto);
        $this->assertEquals("João Silva", $client->name);
        $this->assertEquals("active", $client->status);
        Event::assertDispatched(ClientCreated::class);
    }

    public function test_suspende_cliente_ativo(): void
    {
        Event::fake();
        $client = Client::factory()->create(["status" => "active"]);
        $this->service->suspend($client);
        $this->assertEquals("suspended", $client->fresh()->status);
        Event::assertDispatched(ClientSuspended::class);
    }

    public function test_nao_suspende_cliente_ja_suspenso(): void
    {
        Event::fake();
        $client = Client::factory()->create(["status" => "suspended"]);
        $this->service->suspend($client);
        Event::assertNotDispatched(ClientSuspended::class);
    }

    public function test_reativa_cliente_suspenso(): void
    {
        $client = Client::factory()->create(["status" => "suspended"]);
        $this->service->activate($client);
        $this->assertEquals("active", $client->fresh()->status);
    }
}
