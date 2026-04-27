<?php
namespace App\Domains\Billing\Models;

use App\Domains\Client\Models\Client;
use App\Domains\Client\Models\Contract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'client_id','contract_id','amount','due_date','paid_at','status','payment_method',
    ];
    protected $casts = ['due_date' => 'date', 'paid_at' => 'date'];

    public function client(): BelongsTo   { return $this->belongsTo(Client::class); }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }

    public function isPaid(): bool    { return $this->status === 'paid'; }
    public function isOverdue(): bool { return $this->status === 'overdue'; }

    public function markAsPaid(string $method = 'pix'): void
    {
        $this->update(['status' => 'paid', 'paid_at' => now(), 'payment_method' => $method]);
    }
}
