<?php
namespace App\Domains\Billing\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name','speed_download','speed_upload','price','technology','active'];
    protected $casts = ['active' => 'boolean'];
}
