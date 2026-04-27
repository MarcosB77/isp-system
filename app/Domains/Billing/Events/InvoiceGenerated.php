<?php
namespace App\Domains\Billing\Events;

use App\Domains\Billing\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceGenerated
{
    use Dispatchable, SerializesModels;
    public function __construct(public readonly Invoice $invoice) {}
}
