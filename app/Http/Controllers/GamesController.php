<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Comment;
use App\Models\Games;
use App\Models\like;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class GamesController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkToken');
    }

    // show untuk game
    public function show_all_Games(Request $request)
    {
        try {
            $user = $request->attributes->get('auth_user');
            $genre = $request->input('genre');

            $query = Games::query();

            $query->leftJoin('carts', function ($join) use ($user) {
                $join->on('games.id', '=', 'carts.game_id')
                    ->where('carts.dibuat_oleh', '=', $user->id);
            });

            $query->leftJoin('penjualans', function ($join) use ($user) {
                $join->on('games.id', '=', 'penjualans.game_id')
                    ->where('penjualans.user_id', '=', $user->id);
            });

            if ($request->has('keyword') && !empty($request->get('keyword'))) {
                $keyword = '%' . strtolower($request->get('keyword')) . '%';

                $query->where(function ($query) use ($keyword) {
                    $query->whereRaw('LOWER(name) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(genre) LIKE ?', [$keyword]);
                });
            }

            $transaksi = Penjualan::where('user_id', $user->id)->get();

            // dd($transaksi);

            // dd($purchasedGameIds);

            $games = $query->select('games.*', 'carts.isCart', 'penjualans.diBeli')->get();




            if (!empty($genre)) {
                $games = $games->filter(function ($game) use ($genre) {
                    return stripos($game->genre, $genre) !== false;
                });
            }

            if ($games->isEmpty()) {
                return response()->json(['message' => 'Games Tidak Ditemukan'], 404);
            }

            return response()->json(['games' => $games]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
        }
    }

    public function showBySlug(Request $request, $surname)
    {
        try {
            $user = $request->attributes->get('auth_user');

            $game = Games::leftJoin('carts', function ($join) use ($user) {
                $join->on('games.id', '=', 'carts.game_id')
                    ->where('carts.dibuat_oleh', '=', $user->id);
            })
                ->leftJoin('penjualans', function ($join) use ($user) {
                    $join->on('games.id', '=', 'penjualans.game_id')
                        ->where('penjualans.user_id', '=', $user->id);
                })
                ->where('games.surname', $surname)
                ->select('games.*', 'carts.isCart', 'penjualans.diBeli')
                ->first();
            if (!$game) {
                return response()->json(['message' => 'Game tidak ditemukan'], 404);
            }

            return response()->json(['game' => $game], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
        }
    }


    //function untuk cart
    public function addCart(Request $request)
    {
        try {
            $user = $request->attributes->get('auth_user');
            $game_id = $request->input('gameid');

            $findGame = Games::select('name')->where('id', $game_id)->first();

            if (!$findGame) {
                return response()->json(['message' => 'Game tidak ditemukan'], 404);
            }

            $cart = Cart::create([
                'dibuat_oleh' => $user->id,
                'game_id' => $game_id,
                'namae_game' => $findGame->name,
            ]);

            return response()->json(['message' => 'Berhasil menambahkan ke cart']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan ke cart', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteCart(Request $request)
    {
        try {
            $user = $request->attributes->get('auth_user');
            $cart_id = $request->input('cart_id');

            $cart = Cart::where('id', $cart_id)->where('dibuat_oleh', $user->id)->first();

            if (!$cart) {
                return response()->json(['message' => 'Item cart tidak ditemukan'], 404);
            }

            $cart->delete();

            return response()->json(['message' => 'Item cart berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus item cart', 'error' => $e->getMessage()], 500);
        }
    }

    public function showcart(Request $request)
    {
        try {
            $user = $request->attributes->get('auth_user');

            $query = Cart::select(
                'carts.id',
                'dibuat_oleh',
                'game_id',
                'namae_game',
            )->join('users', 'users.id', '=', 'carts.dibuat_oleh')
                ->where('dibuat_oleh', $user->id)
                ->with('game')
                ->get();

            return response()->json([
                'cart' => $query,
                'massage' => 'Data cart ditemukan'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
        }
    }

    // function like
    public function isLike(Request $request)
    {
        try {
            $user = $request->attributes->get('auth_user');
            $game_id = $request->input('gameid');

            $findGame = Games::select('name')->where('id', $game_id)->first();

            if (!$findGame) {
                return response()->json(['message' => 'Game tidak ditemukan'], 404);
            }

            $cart = like::create([
                'dibuat_oleh' => $user->id,
                'game_id' => $game_id,
                'namae_game' => $findGame->name,
            ]);

            return response()->json(['message' => 'Berhasil menambahkan ke favorite']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan ke favorite', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteLike(Request $request)
    {
        try {
            $user = $request->attributes->get('auth_user');
            $like_id = $request->input('like_id');

            $like = Like::where('id', $like_id)->where('dibuat_oleh', $user->id)->first();

            if (!$like) {
                return response()->json(['message' => 'Item favorite tidak ditemukan'], 404);
            }

            $like->delete();

            return response()->json(['message' => 'Item favorite berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus item favorite', 'error' => $e->getMessage()], 500);
        }
    }

    //function untuk transaksi
    public function addtransaksi(Request $request)
    {
        $user = $request->attributes->get('auth_user');

        // Validasi input
        $validator = Validator::make($request->all(), [
            'game_id' => 'required|array',
            'total' => 'required|array',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $gameIds = $request->input('game_id');
        $total = $request->input('total');
        $data = $request->input('data');

        foreach ($gameIds as $index => $gameId) {
            $transaksi = Penjualan::create([
                'user_id' => $user->id,
                'username' => $user->name,
                'game_id' => $gameId,
                'total' => $total[$index],
                'data' => json_encode($data[$index]),
            ]);
        }

        // Response success
        return response()->json([
            'message' => 'Transaksi berhasil',
            'transaksi' => 'Transaksi telah ditambahkan untuk semua game.',
        ]);
    }


    //comment fanction 
    public function addComment(Request $request)
    {
        try {
            $user = $request->attributes->get('auth_user');
            $game_id = $request->input('game_id');
            $comment = $request->input('comment');

            $query = Comment::create([
                'dibuat_oleh' => $user->name,
                'dibuat_oleh_id' => $user->id,
                'game_id' => $game_id,
                'comment' => $comment,
            ]);
            return response()->json(['message' => 'Berhasil menambahkan comment', 'comment' => $comment]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan comment', 'error' => $e->getMessage()], 500);
        }
    }

    public function showcomment(Request $request)
    {
        try {
            $user = $request->attributes->get('auth_user');
            $game_id = $request->input('game_id');

            $query = Comment::where('game_id', $game_id)->get();

            if ($query) {
                return response()->json([
                    'comment' => $query,
                    'massage' => 'Data comment kosong'
                ]);
            }

            return response()->json([
                'comment' => $query,
                'massage' => 'Data comment berhasil'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
        }
    }
}
