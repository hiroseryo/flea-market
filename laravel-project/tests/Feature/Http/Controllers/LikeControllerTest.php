<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_toggle_like()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        Auth::login($user);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->postJson("/item/{$item->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'liked'     => true,
                'likeCount' => 1,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response2 = $this->postJson("/item/{$item->id}/like");

        $response2->assertStatus(200)
            ->assertJson([
                'liked'     => false,
                'likeCount' => 0,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
