<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ServerStatusWidget extends Widget
{
    protected string $view = "filament.widgets.server-status";
    protected static ?int $sort = 99;
    protected int | string | array $columnSpan = "full";

    public function getViewData(): array
    {
        $uptime = trim(shell_exec("uptime -p") ?? "N/A");

        $memInfo = file_get_contents("/proc/meminfo");
        preg_match("/MemTotal:\s+(\d+)/", $memInfo, $total);
        preg_match("/MemAvailable:\s+(\d+)/", $memInfo, $avail);
        $used = round(($total[1] - $avail[1]) / 1024);
        $totalMb = round($total[1] / 1024);
        $mem = $used . "/" . $totalMb . " MB";

        $load = sys_getloadavg();
        $cpu = round($load[0] * 100 / max(1, (int)shell_exec("nproc")), 1);

        return [
            "uptime" => $uptime,
            "mem"    => $mem,
            "cpu"    => $cpu,
        ];
    }
}
