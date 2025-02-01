<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Auth;


class MypageControllerTest extends TestCase
{
    use RefreshDatabase;

    // 購入者情報(プロフィール画像・名前)+購入済み商品一覧が取得できる
    public function test_mypage_buy_displays_user_info_and_purchased_items()
    {
        $buyer = User::factory()->create([
            'name' => 'Test Buyer',
        ]);

        Profile::factory()->create([
            'user_id' => $buyer->id,
            'postcode' => '123-4567',
            'address' => 'Test Address',
            'building' => 'Test Building',
            'img_url' => 'avatars/test_buyer.png',
        ]);

        Auth::login($buyer);

        $userB = User::factory()->create();
        $item1 = Item::factory()->create([
            'user_id' => $userB->id,
            'name'    => 'Purchased Item 1',
        ]);
        $item2 = Item::factory()->create([
            'user_id' => $userB->id,
            'name'    => 'Purchased Item 2',
        ]);

        SoldItem::factory()->create([
            'user_id'       => $buyer->id,
            'item_id'       => $item1->id,
            'payment_status' => 'paid',
        ]);
        SoldItem::factory()->create([
            'user_id'       => $buyer->id,
            'item_id'       => $item2->id,
            'payment_status' => 'paid',
        ]);

        $response = $this->get('/mypage/?page=buy');

        $response->assertStatus(200);

        $soldItems = $response->viewData('soldItems') ?? collect();
        $userInView = $response->viewData('user');
        $profileInView = $response->viewData('profile');

        $response->assertSee('Test Buyer');
        $response->assertSee('avatars/test_buyer.png');

        $this->assertEquals($buyer->id, $userInView->id, 'Viewに渡されたuserがログインユーザー');
        $this->assertEquals('avatars/test_buyer.png', $profileInView->img_url);

        $this->assertCount(2, $soldItems);
        $response->assertSee('Purchased Item 1');
        $response->assertSee('Purchased Item 2');
    }

    // 出品者情報(プロフィール画像・名前)+出品商品一覧が取得できる
    public function test_mypage_sell_displays_user_info_and_selling_items()
    {
        $seller = User::factory()->create([
            'name' => 'Test Seller',
        ]);

        Profile::factory()->create([
            'user_id' => $seller->id,
            'postcode' => '123-4567',
            'address' => 'Test Address',
            'building' => 'Test Building',
            'img_url' => 'avatars/test_seller.png',
        ]);

        Auth::login($seller);

        $itemA = Item::factory()->create([
            'user_id' => $seller->id,
            'name'    => 'My Item A',
        ]);
        $itemB = Item::factory()->create([
            'user_id' => $seller->id,
            'name'    => 'My Item B',
        ]);

        $response = $this->get('/mypage/?page=sell');

        $response->assertStatus(200);

        $items        = $response->viewData('items')   ?? collect();
        $userInView   = $response->viewData('user');
        $profileInView = $response->viewData('profile');

        $this->assertTrue($items->contains($itemA), '表示リストに My Item A が含まれる');
        $this->assertTrue($items->contains($itemB), '表示リストに My Item B が含まれる');

        $response->assertSee('Test Seller');
        $response->assertSee('avatars/test_seller.png');
        $this->assertEquals($seller->id, $userInView->id);
        $this->assertEquals('avatars/test_seller.png', $profileInView->img_url);

        $this->assertCount(2, $items);
        $response->assertSee('My Item A');
        $response->assertSee('My Item B');
    }
}
