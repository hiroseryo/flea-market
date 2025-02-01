@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>会員登録</h1>
    <form action="{{ route('register.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">ユーザー名</label>
            @if ($errors->has('name'))
            <div style="color: red;">{{ $errors->first('name') }}</div>
            @endif
            <input type="text" id="name" name="name" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            @if ($errors->has('email'))
            <div style="color: red;">{{ $errors->first('email') }}</div>
            @endif
            <input type="email" id="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            @if ($errors->has('password'))
            <div style="color: red;">{{ $errors->first('password') }}</div>
            @endif
            <input type="password" id="password" name="password">
        </div>

        <div class="form-group">
            <label for="password_confirmation">確認用パスワード</label>
            @if ($errors->has('password_confirmation'))
            <div style="color: red;">{{ $errors->first('password_confirmation') }}</div>
            @endif
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>

        <button type="submit" class="submit-btn">登録する</button>
    </form>

    <a href="/login" class="login-link">ログインはこちら</a>
</div>
@endsection