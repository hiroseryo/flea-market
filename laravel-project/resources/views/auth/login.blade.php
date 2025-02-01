@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>ログイン</h1>
    <form action="{{ route('login.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="login">ユーザー名 / メールアドレス</label>
            @if ($errors->has('email'))
            <div style="color: red;">{{ $errors->first('email') }}</div>
            @endif
            <input type="text" id="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            @if ($errors->has('password'))
            <div style="color: red;">{{ $errors->first('password') }}</div>
            @endif
            <input type="password" id="password" name="password">
        </div>

        <button type="submit" class="submit-btn">ログインする</button>
    </form>

    <a href="/register" class="register-link">会員登録はこちら</a>
</div>
@endsection