@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<main class="verify-email-page">
    <div class="verify-email-intro">
        <h2 class="verify-email-title">メールアドレスの確認</h2>
        <p class="verify-email-instructions">
            ご登録のメールアドレスに確認用のリンクを送信しました。<br>
            メールが届いていない場合は、以下のボタンをクリックして再送信してください。
        </p>
    </div>

    @if (session('status'))
    <div class="verify-email-alert">
        新しい確認用リンクが送信されました。
    </div>
    @endif

    <div class="verify-email-action">
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-primary verify-email-button">確認メールを再送信する</button>
        </form>
    </div>
</main>
@endsection