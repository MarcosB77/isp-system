<?php
namespace App\Filament\Widgets;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Client\Models\Client;
use App\Domains\Support\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalClientes    = Client::where("status", "active")->count();
        $suspensos        = Client::where("status", "suspended")->count();
        $faturasPendentes = Invoice::where("status", "pending")->count();
        $valorPendente    = Invoice::where("status", "pending")->sum("amount");
        $faturasVencidas  = Invoice::where("status", "overdue")->count();
        $valorVencido     = Invoice::where("status", "overdue")->sum("amount");
        $chamadosAbertos  = Ticket::where("status", "open")->count();

        return [
            Stat::make("Clientes Ativos", $totalClientes)
                ->description("Total de clientes ativos")
                ->descriptionIcon("heroicon-o-users")
                ->color("success"),

            Stat::make("Suspensos", $suspensos)
                ->description("Clientes suspensos")
                ->descriptionIcon("heroicon-o-no-symbol")
                ->color("warning"),

            Stat::make("Faturas Pendentes", $faturasPendentes)
                ->description("R$ " . number_format($valorPendente, 2, ",", "."))
                ->descriptionIcon("heroicon-o-clock")
                ->color("info"),

            Stat::make("Inadimplentes", $faturasVencidas)
                ->description("R$ " . number_format($valorVencido, 2, ",", ".") . " em aberto")
                ->descriptionIcon("heroicon-o-exclamation-triangle")
                ->color("danger"),

            Stat::make("Chamados Abertos", $chamadosAbertos)
                ->description("Aguardando atendimento")
                ->descriptionIcon("heroicon-o-ticket")
                ->color("warning"),
        ];
    }
}
