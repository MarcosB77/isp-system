<?php
use App\Domains\Billing\Services\BillingService;
use App\Domains\Billing\Services\SuspensionService;
use Illuminate\Support\Facades\Schedule;

// Laravel 11+: schedule direto no console.php
Schedule::call(function () {
    app(BillingService::class)->markOverdue();
    app(BillingService::class)->generateInvoices();
})->daily()->name('billing:generate')->withoutOverlapping();

Schedule::call(function () {
    app(SuspensionService::class)->suspendDefaulters();
})->hourly()->name('billing:suspend')->withoutOverlapping();
