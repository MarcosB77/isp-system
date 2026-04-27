<?php
namespace App\Domains\Billing\Jobs;

use App\Domains\Billing\Services\SuspensionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SuspendDefaultersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries   = 3;
    public int $timeout = 120;

    public function handle(SuspensionService $service): void
    {
        $service->suspendDefaulters();
    }
}
