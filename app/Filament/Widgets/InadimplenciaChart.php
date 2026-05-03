<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Domains\Billing\Models\Invoice;

class InadimplenciaChart extends ChartWidget
{
    protected ?string $heading = "Inadimplencia por mes (R$)";
    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $dados = collect(range(5, 0))->map(function ($i) {
            $mes = now()->subMonths($i);
            $total = Invoice::where("status", "overdue")
                ->whereYear("due_date", $mes->year)
                ->whereMonth("due_date", $mes->month)
                ->sum("amount");
            return ["mes" => $mes->format("M/y"), "total" => $total];
        });
        return [
            "datasets" => [[
                "label" => "Inadimplencia",
                "data" => $dados->pluck("total")->toArray(),
                "backgroundColor" => "#E24B4A",
                "borderRadius" => 4,
            ]],
            "labels" => $dados->pluck("mes")->toArray(),
        ];
    }

    protected function getType(): string { return "bar"; }
}
