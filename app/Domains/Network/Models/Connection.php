<?php
namespace App\Domains\Network\Models;

use App\Domains\Client\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Connection extends Model
{
    protected $fillable = [
        'client_id','ip_address','pppoe_username','pppoe_password',
        'mac_address','onu_serial','online',
    ];
    protected $casts = ['online' => 'boolean'];
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
}
