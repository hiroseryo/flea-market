@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/my_list.css') }}">
@endsection

@section('content')

@if (session('status'))
<div class="success-message">
    {{ session('status') }}
</div>
@endif

<nav class="sub-nav">
    <a href="/" class="sub-nav-link">おすすめ</a>
    <a href="/?page=mylist" class="sub-nav-link active">マイリスト</a>
</nav>

@if ($items->isEmpty())
<div class="empty-message">お気に入り商品はありません</div>
@endif

<div class="product-grid">
    @foreach ($items as $item)
    <div class="product-container">
        <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="product-card">
            <img src="{{ $item->img_url }}" alt="{{ $item->name }}" class="product-image">
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