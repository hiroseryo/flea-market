@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_create.css') }}">
@endsection

@section('content')
<div class="container">

    @if ($errors->any())
    <div class="alert">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h1>商品の出品</h1>

    <form action="{{ route('items.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="image-title">商品画像</div>
        <div class="image-upload">
            <label for="img" class="image-select-button">画像を選択する</label>
            <input type="file" id="img" name="img" accept=".png" style="display: none;">
            <div class="image-preview">
                <img id="preview">
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">商品の詳細</h2>

            <div class="form-group">
                <label>カテゴリー</label>
                @foreach($categories as $category)
                <input class="category-checkbox" type="checkbox" name="categories[]" id="category-{{ $category->id }}" value="{{ $category->id }}" @if(is_array(old('categories')) && in_array($category->id, old('categories'))) checked @endif>
                <label class="category-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                @endforeach
            </div>

            <div class="form-group">
                <label>商品の状態</label>
                <select name="condition_id">
                    <option disabled selected hidden>選択してください</option>
                    @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}" @if(old('condition_id')==$condition->id) selected @endif>
                        {{ $condition->condition }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">商品名と説明</h2>

            <div class="form-group">
                <label>商品名</label>
                <input type="text" name="name" placeholder="商品名" value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label>商品の説明</label>
                <textarea name="description" placeholder="商品の説明を入力してください">{{ old('description')}}</textarea>
            </div>
        </div>

        <div class="section">
            <div class="form-group">
                <label>販売価格</label>
                <div class="price-input">
                    <span class="price-symbol">¥</span>
                    <input type="number" name="price" placeholder="0" value="{{ old('price') }}">
                </div>
            </div>
        </div>

        <button type="submit" class="submit-btn">出品する</button>
    </form>
</div>

<script>
    document.getElementById('img').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('preview');

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            }

            reader.readAsDataURL(file);
        }
    });
</script>
@endsection