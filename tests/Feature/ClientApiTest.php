<?php
namespace Tests\Feature;

use App\Domains\Client\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_clients(): void
    {
        Client::factory()->count(3)->create();

        $this->getJson('/api/v1/clients')
             ->assertOk()
             ->assertJsonStructure(['data', 'total']);
    }

    public function test_can_create_client(): void
    {
        $this->postJson('/api/v1/clients', [
            'name'  => 'Maria Souza',
            'email' => 'maria@teste.com',
            'cpf'   => '98765432100',
        ])->assertCreated()
          ->assertJsonFragment(['name' => 'Maria Souza']);
    }

    public function test_can_suspend_client(): void
    {
        $client = Client::factory()->create(['status' => 'active']);

        $this->postJson("/api/v1/clients/{$client->id}/suspend")
             ->assertOk();

        $this->assertEquals('suspended', $client->fresh()->status);
    }
}
