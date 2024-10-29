<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class like extends Model
{
    use HasFactory;

    protected $fillable = ['dibuat_oleh', 'game_id', 'namae_game', 'isLike'];
}
