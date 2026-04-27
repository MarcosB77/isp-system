<?php
namespace App\Domains\Support\Models;

use App\Domains\Client\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = ['client_id','subject','description','status','priority','resolved_at'];
    protected $casts    = ['resolved_at' => 'datetime'];
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function resolve(): void
    {
        $this->update(['status' => 'resolved', 'resolved_at' => now()]);
    }
}
