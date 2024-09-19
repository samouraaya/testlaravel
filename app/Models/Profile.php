<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'administrator_id', 'first_name', 'image', 'status',
    ];

    public function administrator()
    {
        return $this->belongsTo(Admin::class);
    }
}
