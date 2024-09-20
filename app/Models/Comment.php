<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['content','administrator_id','profile_id'];

    public function administrator()
    {
        return $this->belongsTo(Admin::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
