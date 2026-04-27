<?php
namespace App\Infrastructure\External;

use Illuminate\Support\Facades\Log;

/**
 * MikrotikService — Mock/Simulação
 * Substitua os métodos por chamadas reais à API RouterOS quando tiver hardware.
 * Biblioteca sugerida: evilfreelancer/routeros-api-php
 */
class MikrotikService
{
    public function suspendUser(string $pppoeUsername): bool
    {
        Log::info("[Mikrotik MOCK] Suspendendo usuário: {$pppoeUsername}");
        // REAL: $api->write('/ppp/secret/disable', ['.id' => $id]);
        return true;
    }

    public function activateUser(string $pppoeUsername): bool
    {
        Log::info("[Mikrotik MOCK] Ativando usuário: {$pppoeUsername}");
        // REAL: $api->write('/ppp/secret/enable', ['.id' => $id]);
        return true;
    }

    public function createPppoeUser(string $username, string $password, string $profile): bool
    {
        Log::info("[Mikrotik MOCK] Criando PPPoE: {$username} | Perfil: {$profile}");
        // REAL: $api->write('/ppp/secret/add', ['name' => $username, ...]);
        return true;
    }

    public function setSpeedProfile(string $username, int $downloadMbps, int $uploadMbps): bool
    {
        Log::info("[Mikrotik MOCK] Velocidade {$username}: {$downloadMbps}M/{$uploadMbps}M");
        return true;
    }

    public function isOnline(string $pppoeUsername): bool
    {
        // REAL: checar /ppp/active
        return (bool) rand(0, 1);
    }
}
