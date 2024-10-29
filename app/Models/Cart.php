<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['dibuat_oleh', 'game_id', 'namae_game', 'isCart'];

    public function game()
    {
        return $this->belongsTo(Games::class, 'game_id', 'id');
    }
}
