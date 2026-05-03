<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Domains\Billing\Models\Invoice;

class ReceitaMensalChart extends ChartWidget
{
    protected ?string $heading = "Receita mensal (R$)";
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $dados = collect(range(5, 0))->map(function ($i) {
            $mes = now()->subMonths($i);
            $total = Invoice::where("status", "paid")
                ->whereYear("paid_at", $mes->year)
                ->whereMonth("paid_at", $mes->month)
                ->sum("amount");
            return ["mes" => $mes->format("M/y"), "total" => $total];
        });
        return [
            "datasets" => [[
                "label" => "Receita",
                "data" => $dados->pluck("total")->toArray(),
                "backgroundColor" => "#1D9E75",
                "borderRadius" => 4,
            ]],
            "labels" => $dados->pluck("mes")->toArray(),
        ];
    }

    protected function getType(): string { return "bar"; }
}
