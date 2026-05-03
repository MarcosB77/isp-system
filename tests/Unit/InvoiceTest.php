<?php
namespace Tests\Unit;

use App\Domains\Billing\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fatura_pendente_nao_e_paga(): void
    {
        $invoice = Invoice::factory()->create(["status" => "pending"]);
        $this->assertFalse($invoice->isPaid());
    }

    public function test_marca_fatura_como_paga(): void
    {
        $invoice = Invoice::factory()->create(["status" => "pending"]);
        $invoice->markAsPaid("pix");
        $this->assertTrue($invoice->fresh()->isPaid());
        $this->assertEquals("pix", $invoice->fresh()->payment_method);
        $this->assertNotNull($invoice->fresh()->paid_at);
    }

    public function test_fatura_vencida_e_overdue(): void
    {
        $invoice = Invoice::factory()->create(["status" => "overdue"]);
        $this->assertTrue($invoice->isOverdue());
    }
}
