<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\SoldItem;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page');

        $user = Auth::user();
        $profile = $user->profile;

        if ($page === 'buy') {
            $soldItems = SoldItem::where('user_id', $user->id)->where('payment_status', 'paid')->get();

            return view('my_buy', compact('soldItems', 'user', 'profile'));
        } elseif ($page === 'sell') {
            $items = Item::where('user_id', $user->id)->get();
            return view('my_sell', compact('items', 'user', 'profile'));
        } else {
            return redirect()->route('mypage.index', ['page' => 'buy']);
        }
    }
}
