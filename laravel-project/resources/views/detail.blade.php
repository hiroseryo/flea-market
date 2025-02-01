@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

@if ($errors->any())
<div class="error">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session('status'))
<div class="success">
    {{ session('status') }}
</div>
@endif

<div class="container">
    <div class="product-image">
        <img src="{{ $item->img_url }}" alt="{{ $item->name }}">
    </div>

    <div class="product-info">
        <div class="product-title">{{ $item->name }}</div>

        <p class="price">¥{{ number_format($item->price) }} (税込)</p>

        <div class="interaction-stats">
            @auth
            <span id="like-star" class="star-icon {{ $isLiked ? 'star-liked' : '' }}" data-item-id="{{ $item->id }}" data-liked="{{ $isLiked ? 'true' : 'false' }}">★</span>
            <span id="like-count">{{ $likeCount }}</span>
            @endauth
            @guest
            <span class="star-icon" id="guest-star">★</span>
            <span>{{ $likeCount }}</span>
            @endguest
            <span class="comment-count">💬 {{ $item->comments->count() }}</span>
        </div>
        <div id="guestMessage" style="display: none;">ログインしてからいいねして下さい</div>
        @if($item->user->id === Auth::id())
        <div class="sold-out">自分の出品した商品は購入できません</div>
        @else
        @if($item->soldItem && $item->soldItem->payment_status === 'paid')
        <div class="sold-out">購入できません（SOLD OUT）</div>
        @else
        <a href="{{ route('items.checkoutForm', ['item_id' => $item->id]) }}" class="purchase-button">購入手続きへ</a>
        @endif
        @endif

        <div class="product-description">
            <div class="description-title">商品の説明</div>
            <p>{{ $item->description }}</p>
        </div>

        <div class="product-meta">
            <div class="meta-title">商品の情報</div>
            <div class="category-tags">
                @foreach ($item->categories as $category)
                <span class="category-tag">{{ $category->name }}</span>
                @endforeach
            </div>
            <div class="condition-row">
                <span class="condition-label">商品の状態</span>
                <span>{{ $item->condition->condition}}</span>
            </div>
        </div>

        <div class="comments-header">コメント({{$item->comments->count()}})</div>
        @foreach ($item->comments as $comment)
        <div class="comment">
            <div class="comment-user">
                <div class="user-img">
                    <img src="{{ optional($comment->user->profile)->img_url }}" onerror="this.style.display='none';">
                </div>
                <div class="comment-name">{{ $comment->user->name }}</div>
            </div>
            <p class="comment-message">{{ $comment->comment }}</p>
        </div>
        @endforeach

        @auth
        <div class="comment-form">
            <form action="{{ route('comments.store', ['item_id' => $item->id]) }}" method="post">
                @csrf
                <textarea class="comment-input" placeholder="商品へのコメント" name="comment">{{ old('comment') }}</textarea>
                <button class="comment-submit" type="submit">コメントを送信する</button>
            </form>
        </div>
        @endauth
        @guest
        <div class="comment-form">
            <textarea class="comment-input" placeholder="ログインしてからコメントして下さい"></textarea>
            <a href="/login" class="login-submit">ログイン</a>
        </div>
        @endguest
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const starElement = document.getElementById('like-star');
        const likeCountElement = document.getElementById('like-count');

        if (!starElement) return;

        starElement.addEventListener('click', function() {
            const itemId = starElement.dataset.itemId;

            fetch(`/item/${itemId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({}),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.liked) {
                        starElement.classList.add('star-liked');
                        starElement.dataset.liked = 'true';
                    } else {
                        starElement.classList.remove('star-liked');
                        starElement.dataset.liked = 'false';
                    }
                    likeCountElement.textContent = data.likeCount;
                })
                .catch(err => {
                    console.error(err);
                });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const guest = document.getElementById('guest-star');
        const guestMessage = document.getElementById('guestMessage');

        guest.addEventListener('mouseenter', () => {
            guestMessage.style.display = 'block';
        });

        guest.addEventListener('mouseleave', () => {
            guestMessage.style.display = 'none';
        });

    });
</script>
@endsection