<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $pageParam = $request->input('page');

        if ($request->has('keyword')) {
            session(['keyword' => $request->input('keyword')]);
        }
        $keyword = session('keyword') ?? '';

        if ($pageParam === 'mylist') {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'マイページを表示するにはログインが必要です');
            }

            $user = Auth::user();
            $likeItemIds = DB::table('likes')->where('user_id', $user->id)->pluck('item_id');

            $query = Item::whereIn('id', $likeItemIds)->where('user_id', '!=', $user->id);

            if (!empty($keyword)) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $items = $query->get();

            return view('my_list', compact('items', 'keyword'));
        } else {
            $query = Item::query();

            if (Auth::check()) {
                $userId = Auth::id();
                $query->where('user_id', '!=', $userId);
            }

            if (!empty($keyword)) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $items = $query->get();

            return view('index', compact('items', 'keyword'));
        }
    }

    public function show($item_id)
    {
        $item = Item::with(['condition', 'categories', 'comments.user.profile'])->findOrFail($item_id);

        $isLiked = false;
        $likeCount = $item->likes()->count();

        if (Auth::check()) {
            $isLiked = $item->likes()->where('user_id', Auth::id())->exists();
        }

        return view('detail', compact('item', 'isLiked', 'likeCount'));
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('item_create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $extension = $image->getClientOriginalExtension();
            $filename = Str::random(20) . '.' . $extension;
            $path = $image->storeAs('images', $filename, 'public');
            $img_url = Storage::url($path);
        }

        $item = Item::create([
            'user_id' => Auth::id(),
            'condition_id' => $request->input('condition_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'img_url' => $img_url,
        ]);

        $item->categories()->attach($request->input('categories'));

        return redirect()->route('mypage.index', ['page' => 'sell'])->with('status', '商品を出品しました');
    }

    public function checkoutForm($item_id)
    {
        $item = Item::findOrFail($item_id);

        $user = Auth::user();
        $profile = $user->profile;

        return view('checkout', compact('item', 'profile'));
    }
}
