<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    //未ログインユーザーはコメント投稿できず、/loginにリダイレクト
    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'ゲストがコメント',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'comment' => 'ゲストがコメント',
        ]);
    }

    //コメントが空の場合はエラー
    public function test_comment_cannot_be_empty()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        Auth::login($user);

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors('comment');

        $this->assertDatabaseMissing('comments', [
            'comment' => '',
        ]);
    }

    //コメントが255文字を超える場合はエラー
    public function test_comment_cannot_exceed_255_characters()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        Auth::login($user);

        $longComment = str_repeat('a', 256);

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => $longComment,
        ]);

        $response->assertSessionHasErrors('comment');

        $this->assertDatabaseMissing('comments', [
            'comment' => $longComment,
        ]);
    }

    //コメントが正常な場合は投稿される
    public function test_user_can_post_comment_if_valid()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        Auth::login($user);

        $validComment = str_repeat('a', 255);

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => $validComment,
        ]);


        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'comment' => $validComment,
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
