<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Domains\Billing\Models\Invoice;

class StatusPagamentosChart extends ChartWidget
{
    protected ?string $heading = "Status de pagamentos";
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        return [
            "datasets" => [[
                "data" => [
                    Invoice::where("status", "paid")->count(),
                    Invoice::where("status", "pending")->count(),
                    Invoice::where("status", "overdue")->count(),
                ],
                "backgroundColor" => ["#1D9E75", "#EF9F27", "#E24B4A"],
                "borderWidth" => 0,
                "hoverOffset" => 6,
            ]],
            "labels" => ["Pago", "Pendente", "Vencido"],
        ];
    }

    protected function getType(): string { return "doughnut"; }
}
