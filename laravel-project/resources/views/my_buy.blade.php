@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/my_buy.css') }}">
@endsection

@section('content')
@if (session('status'))
<div class="success">
    {{ session('status') }}
</div>
@endif

@if (session('error'))
<div class="error">
    {{ session('error') }}
</div>
@endif

<div class="profile">
    <div class="profile-image">
        @if (!empty($profile->img_url))
        <img src="{{ $profile->img_url }}" class="profile-image-placeholder">
        @endif
    </div>
    <div class="profile-name">{{ $user->name }}</div>
    <a href="/profile" class="edit-profile">プロフィールを編集</a>
</div>

<nav class="sub-nav">
    <a href="/mypage/?page=sell" class="sub-nav-link">出品した商品</a>
    <a href="/mypage/?page=buy" class="sub-nav-link active">購入した商品</a>
</nav>

<div class="product-grid">
    @foreach ($soldItems as $soldItem)
    @php
    $item = $soldItem->item;
    @endphp
    <div class="product-container">
        <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="product-card">
            <img src="{{ $item->img_url }}" class="product-image">
            @if($item->soldItem && $item->soldItem->payment_status === 'paid')
            <div class="sold-overlay">
                SOLD
            </div>
            @endif
        </a>
        <div class="product-name">{{ $item->name }}</div>
    </div>
    @endforeach
</div>
@endsection