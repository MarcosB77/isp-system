<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Domains\Client\Models\Client;

class ClientesChart extends ChartWidget
{
    protected ?string $heading = "Crescimento de clientes";
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $dados = collect(range(5, 0))->map(function ($i) {
            $mes = now()->subMonths($i);
            $total = Client::where("created_at", "<=", $mes->endOfMonth())->count();
            return ["mes" => $mes->format("M/y"), "total" => $total];
        });
        return [
            "datasets" => [[
                "label" => "Clientes",
                "data" => $dados->pluck("total")->toArray(),
                "borderColor" => "#378ADD",
                "backgroundColor" => "rgba(55,138,221,0.08)",
                "fill" => true,
                "tension" => 0.4,
            ]],
            "labels" => $dados->pluck("mes")->toArray(),
        ];
    }

    protected function getType(): string { return "line"; }
}
