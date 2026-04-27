<?php
namespace Tests\Unit;

use App\Domains\Client\DTOs\CreateClientDTO;
use App\Domains\Client\Events\ClientCreated;
use App\Domains\Client\Models\Client;
use App\Domains\Client\Services\ClientService;
use App\Infrastructure\External\MikrotikService;
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

    public function test_creates_client_and_fires_event(): void
    {
        Event::fake();

        $dto = new CreateClientDTO(
            name:  'João Silva',
            email: 'joao@teste.com',
            cpf:   '12345678901',
        );

        $client = $this->service->create($dto);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('João Silva', $client->name);
        $this->assertEquals('active', $client->status);
        Event::assertDispatched(ClientCreated::class);
    }

    public function test_suspend_changes_status(): void
    {
        Event::fake();

        $client = Client::factory()->create(['status' => 'active']);
        $this->service->suspend($client);

        $this->assertEquals('suspended', $client->fresh()->status);
    }
}
