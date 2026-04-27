<?php
namespace App\Application\UseCases\Billing;

use App\Domains\Billing\Services\BillingService;

class GenerateInvoicesUseCase
{
    public function __construct(private BillingService $billingService) {}

    public function execute(): int
    {
        return $this->billingService->generateInvoices();
    }
}
