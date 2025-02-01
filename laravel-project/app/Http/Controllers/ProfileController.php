<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Profile;
use App\Http\Requests\AddressRequest;
use App\Models\Item;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile', compact('user', 'profile'));
    }

    public function update(AddressRequest $request)
    {
        $user = User::findOrFail(Auth::id());

        $user->name = $request->input('name');
        $user->save();

        $profile = $user->profile ?? new Profile();
        $profile->user_id = $user->id;

        $profile->postcode = $request->input('postcode') ?? '';
        $profile->building = $request->input('building') ?? '';
        $profile->address  = $request->input('address') ?? '';


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = 'png';
            $filename = Str::random(20) . '.' . $extension;
            $path = $image->storeAs('images', $filename, 'public');
            $img_url = Storage::url($path);
            if ($profile->img_url) {
                Storage::disk('public')->delete(str_replace('/storage', '', $profile->img_url));
            }
            $profile->img_url = $img_url;
        }
        $profile->save();

        return redirect('/?page=mylist')->with('status', 'プロフィールを更新しました');
    }

    public function addressForm($item_id)
    {
        $user = User::findOrFail(Auth::id());
        $item = Item::findOrFail($item_id);
        $profile = $user->profile ?? new Profile();

        return view('address', compact('profile', 'item', 'user'));
    }

    public function addressUpdate(AddressRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = User::findOrFail(Auth::id());

        $profile = $user->profile ?? new Profile();
        $profile->user_id = $user->id;

        $profile->postcode = $request->input('postcode') ?? '';
        $profile->building = $request->input('building') ?? '';
        $profile->address  = $request->input('address') ?? '';

        $profile->save();

        return redirect()->route('items.checkoutForm', ['item_id' => $item->id])->with('status', '住所が更新されました');
    }
}
