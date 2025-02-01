<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Like;

class LikeController extends Controller
{
    public function like($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $existingLike = Like::where('user_id', $user->id)->where('item_id', $item->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
            $liked = true;
        }

        $likeCount = $item->likes()->count();

        return response()->json([
            'liked' => $liked,
            'likeCount' => $likeCount,
        ]);
    }
}
