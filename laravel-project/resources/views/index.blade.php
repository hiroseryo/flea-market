@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<nav class="sub-nav">
    <a href="/" class="sub-nav-link">おすすめ</a>
    <a href="/?page=mylist" class="sub-nav-link active">マイリスト</a>
</nav>

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