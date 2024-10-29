<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Games extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'surname',
        'price',
        'desc',
        'link',
        'release',
        'platforms',
        'genre',
        'developers',
        'publishers',
        'rating',
        'cover',
        'footage'
    ];

    protected $casts = [
        'footage' => 'array',
        'genre' => 'array',
    ];

    /**
     * 
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($game) {
            if ($game->cover) {
                Storage::disk('public')->delete($game->cover);
            }

            if (is_array($game->footage)) {
                foreach ($game->footage as $footage) {
                    if ($footage) {
                        Storage::disk('public')->delete($footage);
                    }
                }
            }
        });
    }

   

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }    

    public function carts()
    {
        return $this->hasMany(Cart::class, 'game_id', 'id');
    }

    public function comment()
    {
         return $this->hasMany(Comment::class, 'game_id', 'id');
    }


}
