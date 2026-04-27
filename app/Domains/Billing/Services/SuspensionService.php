<?php
namespace App\Domains\Billing\Services;

use App\Domains\Client\Models\Client;
use App\Domains\Client\Services\ClientService;
use Illuminate\Support\Facades\Log;

class SuspensionService
{
    public function __construct(
        private ClientService $clientService
    ) {}

    /**
     * Suspende clientes com faturas vencidas.
     * Chamado pelo Scheduler a cada hora.
     */
    public function suspendDefaulters(): int
    {
        $suspended = 0;

        Client::where('status', 'active')
            ->whereHas('invoices', fn($q) => $q->where('status', 'overdue'))
            ->chunk(50, function ($clients) use (&$suspended) {
                foreach ($clients as $client) {
                    $this->clientService->suspend($client);
                    $suspended++;
                }
            });

        Log::info("Clientes suspensos por inadimplência: {$suspended}");
        return $suspended;
    }
}
