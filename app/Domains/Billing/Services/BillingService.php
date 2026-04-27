<?php
namespace App\Domains\Billing\Services;

use App\Domains\Billing\Events\InvoiceGenerated;
use App\Domains\Billing\Models\Invoice;
use App\Domains\Client\Models\Contract;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    /**
     * Gera faturas mensais para todos os contratos ativos.
     * Chamado pelo Scheduler diariamente.
     */
    public function generateInvoices(): int
    {
        $today     = Carbon::today();
        $generated = 0;

        Contract::with('plan', 'client')
            ->where('active', true)
            ->whereNull('ends_at')
            ->chunk(100, function ($contracts) use ($today, &$generated) {
                foreach ($contracts as $contract) {
                    // Só gera se hoje é o dia de vencimento e ainda não existe fatura do mês
                    if ($today->day !== $contract->due_day) continue;

                    $alreadyExists = Invoice::where('contract_id', $contract->id)
                        ->whereYear('due_date', $today->year)
                        ->whereMonth('due_date', $today->month)
                        ->exists();

                    if ($alreadyExists) continue;

                    DB::transaction(function () use ($contract, $today, &$generated) {
                        $invoice = Invoice::create([
                            'client_id'   => $contract->client_id,
                            'contract_id' => $contract->id,
                            'amount'      => $contract->plan->price,
                            'due_date'    => $today,
                            'status'      => 'pending',
                        ]);

                        event(new InvoiceGenerated($invoice));
                        $generated++;
                    });
                }
            });

        Log::info("Faturas geradas: {$generated}");
        return $generated;
    }

    /**
     * Marca faturas vencidas como overdue.
     */
    public function markOverdue(): int
    {
        $count = Invoice::where('status', 'pending')
            ->where('due_date', '<', now()->startOfDay())
            ->update(['status' => 'overdue']);

        Log::info("Faturas marcadas como overdue: {$count}");
        return $count;
    }
}
