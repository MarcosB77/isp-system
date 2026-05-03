<?php
namespace Database\Factories;

use App\Domains\Billing\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            "client_id"   => \App\Domains\Client\Models\Client::factory(),
            "contract_id" => null,
            "amount"      => $this->faker->randomFloat(2, 50, 500),
            "due_date"    => now()->addDays(10),
            "status"      => "pending",
            "payment_method" => null,
            "paid_at"     => null,
        ];
    }
}
