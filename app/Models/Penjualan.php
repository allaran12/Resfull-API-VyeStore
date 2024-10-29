<?php

namespace App\Models;

use App\Models\Games;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = "penjualans";
    protected $guarded = ['id'];

    public function game()
{
    return $this->belongsTo(Games::class, 'game_id');
}
    
}
