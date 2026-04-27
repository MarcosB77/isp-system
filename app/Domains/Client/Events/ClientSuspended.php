<?php
namespace App\Domains\Client\Events;

use App\Domains\Client\Models\Client;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientSuspended
{
    use Dispatchable, SerializesModels;
    public function __construct(public readonly Client $client) {}
}
