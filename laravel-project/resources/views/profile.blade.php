@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>プロフィール設定</h1>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="profile-image-section">
            <div class="profile-image">
                <img src="{{ !empty($profile->img_url) ? $profile->img_url : asset('images/default-profile.png') }}" class="profile-image-placeholder" id="profileImage">
            </div>
            <label for="image" class="image-select-button">画像を選択する</label>
            @if ($errors->has('image'))
            <div style="color: red;">{{ $errors->first('image') }}</div>
            @endif
            <input type="file" name="image" id="image" accept=".png" style="display: none;">
        </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            @if ($errors->has('name'))
            <div style="color: red;">{{ $errors->first('name') }}</div>
            @endif
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
        </div>

        <div class="form-group">
            <label for="postcode">郵便番号</label>
            @if ($errors->has('postcode'))
            <div style="color: red;">{{ $errors->first('postcode') }}</div>
            @endif
            <input type="text" id="postcode" name="postcode" value="{{ old('postcode', $profile->postcode ?? '') }}">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            @if ($errors->has('address'))
            <div style="color: red;">{{ $errors->first('address') }}</div>
            @endif
            <input type="text" id="address" name="address" value="{{ old('address', $profile->address ?? '') }}">
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            @if ($errors->has('building'))
            <div style="color: red;">{{ $errors->first('building') }}</div>
            @endif
            <input type="text" id="building" name="building" value="{{ old('building', $profile->building ?? '') }}">
        </div>

        <button type="submit" class="submit-btn">更新する</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const profileImage = document.getElementById('profileImage');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                if (file.type === 'image/png') {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        profileImage.src = e.target.result;
                    }

                    reader.readAsDataURL(file);
                } else {
                    alert('PNG形式の画像を選択してください。');
                    imageInput.value = '';
                }
            }
        });
    });
</script>
@endsection