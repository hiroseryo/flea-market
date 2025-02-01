@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endsection

@section('content')
@if (session('status'))
<div class="success-message">
    {{ session('status') }}
</div>
@endif

<form action="{{ route('purchase.checkout', ['item_id' => $item->id]) }}" method="post">
    @csrf
    <main class="main-content">
        <div class="checkout-left">
            <div class="product-summary">
                <div class="product-image">
                    <img src="{{ $item->img_url }}">
                </div>
                <div class="product-details">
                    <h2 class="product-name">{{ $item->name}}</h2>
                    <p class="product-price">¥{{ $item->price}}</p>
                </div>
            </div>
            <div class="delivery-section">
                <div class="delivery-header">
                    <h2 class="section-title">配送先</h2>
                    <a href="{{ route('items.address', ['item_id' => $item->id]) }}" class="change-link">変更する</a>
                </div>
                <div class="address">
                    {{ $profile->postcode ?? '' }}<br>
                    <span class="address-section">{{ $profile->address ?? ''}}</span>
                    <span class="address-section">{{ $profile->building ?? '' }}</span>
                    @if ($errors->has('address'))
                    <div class="error">{{ $errors->first('address') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="order-summary">
            <div class="payment-container">
                <label for="payment_method" class="payment-method">支払い方法</label>
                <select name="payment_method" class="payment-select" id="payment-select">
                    <option disabled selected hidden>選択してください</option>
                    <option value="konbini">コンビニ払い</option>
                    <option value="card">クレジットカード払い</option>
                </select>
                @if ($errors->has('payment_method'))
                <div class="error">{{ $errors->first('payment_method') }}</div>
                @endif
            </div>

            <div class="summary-container">
                <div class="summary-row">
                    <span>商品代金</span>
                    <span>¥{{ $item->price }}</span>
                </div>
                <div class="summary-method">
                    <span>支払い方法</span>
                    <span id="payment-method">{{ old('payment_method') }}</span>
                </div>
            </div>
            @if($item->user->id === Auth::id())
            <div class="sold-out">自分の出品商品は購入不可</div>
            @elseif($item->soldItem && $item->soldItem->payment_status === 'paid')
            <div class="sold-out">購入できません（SOLD OUT）</div>
            @else
            <button class="purchase-button" type="submit">購入する</button>
            @endif
        </div>
    </main>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment-select');
        const paymentMethod = document.getElementById('payment-method');

        paymentSelect.addEventListener('change', function() {
            paymentMethod.textContent = this.options[this.selectedIndex].text;
        });
    });
</script>
@endsection