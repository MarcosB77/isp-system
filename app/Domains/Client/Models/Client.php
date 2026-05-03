<?php

namespace App\Domains\Client\Models;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Network\Models\Connection;
use App\Domains\Support\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use SoftDeletes;
    use HasFactory; 
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone',
        'address',
        'city',
        'state',
        'status',
        'plan_id',
        'due_day',
    ];

    // O método novo entra aqui embaixo:
    protected static function newFactory()
    {
        return \Database\Factories\ClientFactory::new();
    }

    /**
     * Relacionamento com todos os contratos do cliente.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Relacionamento com o contrato ativo mais recente.
     * Esta versão é compatível com Eager Loading (with('activeContract')).
     */
    public function activeContract(): HasOne
    {
        return $this->hasOne(Contract::class)
            ->where('active', true)
            ->latestOfMany();
    }

    /**
     * Relacionamento com as faturas do cliente.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Relacionamento com a conexão de rede (Starlink/PPPoE).
     */
    public function connection(): HasOne
    {
        return $this->hasOne(Connection::class);
    }

    /**
     * Relacionamento com os chamados de suporte.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // --- Helpers de Estado ---

    public function plan()
    {
        return $this->belongsTo(\App\Domains\Billing\Models\Plan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Verifica se o cliente possui faturas atrasadas.
     */
    public function hasOverdueInvoices(): bool
    {
        return $this->invoices()->where('status', 'overdue')->exists();
    }
}
