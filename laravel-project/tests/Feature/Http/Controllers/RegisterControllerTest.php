<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    // ユーザー名は必須
    public function test_name_is_required(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '',
            'email' => 'example@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);

        $this->assertGuest();
    }

    // メールアドレスは必須
    public function test_email_is_required(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);

        $this->assertGuest();
    }

    // パスワードは必須
    public function test_password_is_required(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'example@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);

        $this->assertGuest();
    }

    // パスワードは最低8文字
    public function test_password_is_minimum_8_characters(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'example@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);

        $this->assertGuest();
    }

    // パスワードは確認用と一致
    public function test_password_confirmation_is_required(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'example@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password321',
        ]);

        $response->assertSessionHasErrors([
            'password_confirmation' => 'パスワードと一致しません',
        ]);

        $this->assertGuest();
    }

    // ユーザー登録成功
    public function test_user_can_register(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'text@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/email/verify');

        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'text@example.com',
        ]);

        $this->assertAuthenticated();
    }
}
