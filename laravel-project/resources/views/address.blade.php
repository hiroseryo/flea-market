@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>住所の変更</h1>

    <form action="{{ route('items.addressUpdate', ['item_id' => $item->id]) }}" method="post">
        @csrf
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" hidden>

        <div class="form-group">
            <label for="postal-code">郵便番号</label>
            <input type="text" id="postal-code" name="postcode" value="{{ old('postcode', $profile->postcode ?? '') }}">
        </div>
        @if ($errors->has('postcode'))
        <div style="color: red;">{{ $errors->first('postcode') }}</div>
        @endif

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', $profile->address ?? '') }}">
        </div>
        @if ($errors->has('address'))
        <div style="color: red;">{{ $errors->first('address') }}</div>
        @endif

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building', $profile->building ?? '') }}">
        </div>
        @if ($errors->has('building'))
        <div style="color: red;">{{ $errors->first('building') }}</div>
        @endif

        <button type="submit" class="submit-btn">更新する</button>
    </form>
</div>
@endsection