<?php

namespace App\Http\Controllers;

use App\Models\Games;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function getUserGames(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $transaksi = Penjualan::where('user_id', $userId)->get();

            return response()->json([
                'games' => $transaksi,
                'message' => 'Daftar game yang sudah dibeli'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil user game ', 'error' => $e->getMessage()], 500);
        }
    }

    public function data_user_login(Request $request)
    {
        $user = $request->attributes->get('auth_user');

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $userData = DB::table('users')->where('id', $user->id)->first();

        if (!$userData) {
            return response()->json(['error' => 'User data not found'], 404);
        }

        return response()->json(['user' => $userData]);
    }

    public function EditUsername(Request $request)
    {

        $user = $request->attributes->get('auth_user');

        $newName = $request->input('newName');

        $validator = Validator::make($request->all(), [
            'newName' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = User::where('id', $user->id)->update([
            'name' => $newName
        ]);

        if (!$query) {
            return response()->json(['massage' => 'gagal mengupdate', 'username' => $newName, 'status' => false]);
        }

        return response()->json(['massage' => 'berhasil update', 'newName' => $newName, 'status' => true]);
    }

    public function EditAvatar(Request $request)
    {

        $user = $request->attributes->get('auth_user');

        $validator = Validator::make($request->all(), [
            // 'image' => 'image|mimes:jpeg,png,jpg,svg',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'        
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $image = $request->file('image');

        $imageName = time() . '.' . $image->extension();
        $imgname = "profile/" . $imageName;

        Storage::disk('public')->put($imgname, file_get_contents($image));
        // $path = storage_path("profile");
        // dd($path);

        $query = User::where('id', $user->id)->update([
            'profile_image' => $imgname
        ]);

        if (!$query) {
            return response()->json(['massage' => 'gagal mengupdate', 'status' => false]);
        }

        return response()->json(['massage' => 'berhasil update', 'avatar' => $imgname, 'status' => true]);
    }
}
