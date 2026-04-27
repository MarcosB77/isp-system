<?php
namespace App\Domains\Client\Models;

use App\Domains\Billing\Models\Plan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'client_id', 'plan_id', 'starts_at', 'ends_at', 'due_day', 'active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
        'active'    => 'boolean',
    ];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function plan(): BelongsTo   { return $this->belongsTo(Plan::class); }
}
