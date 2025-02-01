<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    // メールアドレスは必須
    public function test_email_is_required(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);

        $this->assertGuest();
    }

    // パスワードは必須
    public function test_password_is_required(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'text@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);

        $this->assertGuest();
    }

    // データが不明
    public function test_data_is_unknown(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'unknown@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);

        $this->assertGuest();
    }

    // ログイン成功
    public function test_login_success(): void
    {
        $user = User::factory()->create([
            'email' => 'text@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->get('/login');

        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'text@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/?page=mylist');

        $this->assertAuthenticatedAs($user);
    }

    // ログアウト処理
    public function test_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'text@example.com',
            'password' => bcrypt('password123'),
        ]);

        $loginPageResponse = $this->get('/login');
        $loginPageResponse->assertStatus(200);

        $loginResponse = $this->post('/login', [
            'email' => 'text@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect('/?page=mylist');

        $this->assertAuthenticatedAs($user);

        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }
}
