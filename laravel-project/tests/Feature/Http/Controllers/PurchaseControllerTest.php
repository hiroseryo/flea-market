<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Auth;

class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_bought_item_shows_in_buy_list()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name'    => 'Test Item',
            'img_url' => 'example/test.png',
        ]);

        SoldItem::factory()->create([
            'user_id'       => $user->id,
            'item_id'       => $item->id,
            'payment_status' => 'paid',
        ]);

        $response = $this->get('/mypage?page=buy');

        $response->assertStatus(200);

        $soldItems = $response->viewData('soldItems') ?? collect();

        $this->assertTrue($soldItems->contains(function ($soldItem) use ($item) {
            return $soldItem->item_id === $item->id;
        }), '購入済み商品の item_id が含まれる');

        $response->assertSee('Test Item');
        $response->assertSee('example/test.png');
    }
}
