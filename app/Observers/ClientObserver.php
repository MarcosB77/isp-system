<?php
namespace App\Observers;

use App\Domains\Client\Models\Client;
use App\Domains\Client\Models\Contract;

class ClientObserver
{
    public function created(Client $client): void
    {
        if (empty($client->plan_id)) return;

        Contract::create([
            "client_id" => $client->id,
            "plan_id"   => $client->plan_id,
            "starts_at" => now()->toDateString(),
            "due_day"   => $client->due_day ?? 10,
            "active"    => true,
        ]);
    }
}
