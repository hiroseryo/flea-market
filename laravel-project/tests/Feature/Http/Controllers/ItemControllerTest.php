<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;


    // 商品一覧取得
    public function test_items__sold__sell_all(): void
    {
        $userA = User::factory()->create();

        $userB = User::factory()->create();

        $ownItem = Item::factory()->create([
            'name' => 'Own Item',
            'user_id' => $userA->id,
        ]);

        $otherItem = Item::factory()->create([
            'name' => 'Other Item',
            'user_id' => $userB->id,
        ]);

        SoldItem::factory()->create([
            'item_id' => $otherItem->id,
            'user_id' => $userA->id,
            'payment_status' => 'paid',
        ]);

        Auth::login($userA);

        $response = $this->get('/');

        $viewItems = $response->viewData('items');
        $this->assertFalse($viewItems->contains($ownItem), "自分が出品したアイテムが表示されてはいけない。");

        $this->assertTrue($viewItems->contains($otherItem), "他ユーザーが出品したアイテムは表示される。");

        $response->assertSee('sold');

        $response->assertStatus(200);
        $response->assertViewIs('index');
    }



    // マイリスト一覧取得
    public function test_unauthenticated_user_cannot_see_likes(): void
    {
        $response = $this->get('/?page=mylist');

        $response->assertRedirect('/login');
    }

    public function test_mylist_shows_only_liked_other_users_items(): void
    {
        $userA = User::factory()->create();

        $userB = User::factory()->create();

        $itemB1 = Item::factory()->create([
            'user_id' => $userB->id,
            'name'    => 'itemB1',
        ]);
        $itemB2 = Item::factory()->create([
            'user_id' => $userB->id,
            'name'    => 'itemB2',
        ]);

        $ownItem = Item::factory()->create([
            'user_id' => $userA->id,
            'name'    => 'ownItem',
        ]);

        Like::factory()->create([
            'user_id' => $userA->id,
            'item_id' => $itemB1->id,
        ]);

        Auth::login($userA);
        $response = $this->get('/?page=mylist');

        $response->assertViewIs('my_list');

        $items = $response->viewData('items');

        $this->assertTrue($items->contains($itemB1), "いいねした他ユーザーのアイテムは表示される。");

        $this->assertFalse($items->contains($itemB2), "いいねしていない他ユーザーのアイテムは表示されない。");

        $this->assertFalse($items->contains($ownItem), "自分が出品したアイテムは表示されない。");
    }

    public function test_mylist_displays_sold_for_paid_items()
    {

        $userA = User::factory()->create();

        $userB = User::factory()->create();
        $itemB = Item::factory()->create([
            'user_id' => $userB->id,
            'name'    => 'itemB',
        ]);

        Like::factory()->create([
            'user_id' => $userA->id,
            'item_id' => $itemB->id,
        ]);

        SoldItem::factory()->create([
            'item_id'        => $itemB->id,
            'user_id'        => $userA->id,
            'payment_status' => 'paid',
        ]);

        Auth::login($userA);
        $response = $this->get('/?page=mylist');

        $response->assertSee('sold');

        $items = $response->viewData('items');
        $this->assertTrue($items->contains($itemB), "いいね済みのアイテムは表示される。");
    }



    // 商品検索機能
    public function test_search_keyword_is_preserved_across_main_list_and_mylist()
    {
        $userA = User::factory()->create();

        $userB = User::factory()->create();

        $item1 = Item::factory()->create([
            'name' => 'Bag',
            'user_id' => $userB->id,
        ]);
        $item2 = Item::factory()->create([
            'name' => 'LaptopBag',
            'user_id' => $userB->id,
        ]);
        $item3 = Item::factory()->create([
            'name' => 'Baggy Jeans',
            'user_id' => $userB->id,
        ]);

        $item4 = Item::factory()->create([
            'name' => 'Book',
            'user_id' => $userB->id,
        ]);

        Like::factory()->create([
            'user_id' => $userA->id,
            'item_id' => $item1->id,
        ]);
        Like::factory()->create([
            'user_id' => $userA->id,
            'item_id' => $item3->id,
        ]);
        Auth::login($userA);

        $response = $this->get('/?keyword=Bag');

        $response->assertViewIs('index');

        $items = $response->viewData('items');

        $this->assertTrue($items->contains($item1), "キーワード 'Bag' にマッチ");
        $this->assertTrue($items->contains($item2), "キーワード 'Bag' にマッチ (LaptopBag)");
        $this->assertTrue($items->contains($item3), "キーワード 'Bag' にマッチ (Baggy)");
        $this->assertFalse($items->contains($item4), "'Book' は 'Bag' 部分一致しないため除外");

        $response2 = $this->get('/?page=mylist');

        $response2->assertViewIs('my_list');

        $mylistItems = $response2->viewData('items');

        $this->assertTrue($mylistItems->contains($item1), "like済み + 'Bag' 部分一致 => 表示される");
        $this->assertTrue($mylistItems->contains($item3), "like済み + 'Bag' 部分一致 => 表示される");

        $this->assertFalse($mylistItems->contains($item2), "likeしてない => mylistに表示されない");

        $this->assertFalse($mylistItems->contains($item4));
    }


    // 商品詳細取得
    public function test_item_detail_displays_all_information()
    {
        $user = User::factory()->create(['name' => 'Test User']);
        Auth::login($user);

        Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'avatars/sample_user.png',
            'postcode' => '123-4567',
            'address' => 'Test Address',
            'building' => 'Test Building',
        ]);

        $condition = Condition::factory()->create([
            'condition' => '新品',
        ]);

        $item = Item::factory()->create([
            'user_id'      => $user->id,
            'name'         => 'Test Item',
            'price'        => 9999,
            'description'  => 'This is a test description.',
            'img_url'      => 'items/sample_item.png',
            'condition_id' => $condition->id,
        ]);

        $cat1 = Category::factory()->create(['name' => 'Category A']);
        $cat2 = Category::factory()->create(['name' => 'Category B']);
        $item->categories()->attach([$cat1->id, $cat2->id]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $userB = User::factory()->create(['name' => 'Commenter B']);
        Comment::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'First comment!',
        ]);
        Comment::factory()->create([
            'user_id' => $userB->id,
            'item_id' => $item->id,
            'comment' => 'Second comment!',
        ]);

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertViewIs('detail');

        $viewItem = $response->viewData('item');

        $response->assertSee("items/sample_item.png");

        $response->assertSee('Test Item');

        $response->assertSee('¥9,999 (税込)');

        $this->assertEquals(1, $viewItem->likes->count(), 'いいね数が正しい');
        $response->assertSee('1');

        $this->assertEquals(2, $viewItem->comments->count(), 'コメント数が正しい');
        $response->assertSee('2');

        $response->assertSee('This is a test description.');

        $this->assertTrue($viewItem->categories->contains($cat1));
        $this->assertTrue($viewItem->categories->contains($cat2));

        $response->assertSee('Category A');
        $response->assertSee('Category B');

        $this->assertEquals('新品', $viewItem->condition->condition);
        $response->assertSee('新品');

        $response->assertSee('First comment!');
        $response->assertSee('Second comment!');
        $response->assertSee('Test User');
        $response->assertSee('Commenter B');

        $response->assertSee('avatars/sample_user.png');

        $response->assertStatus(200);
    }

    public function test_user_can_store_item_with_categories_and_image()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $catA = Category::factory()->create(['name' => 'Category A']);
        $catB = Category::factory()->create(['name' => 'Category B']);

        $condition = Condition::factory()->create([
            'condition' => '新品',
        ]);

        Storage::fake('public');
        $uploadedFile = UploadedFile::fake()->image('test_image.png');

        $data = [
            'img'          => $uploadedFile,
            'condition_id' => $condition->id,
            'name'         => 'Test Item',
            'description'  => 'This is a test item',
            'price'        => 1234,
            'categories'   => [$catA->id, $catB->id],
        ];

        $response = $this->post(route('items.store'), $data);

        $response->assertStatus(302);
        $response->assertSessionHas('status', '商品を出品しました');
        $response->assertRedirect(route('mypage.index', ['page' => 'sell']));

        $this->assertDatabaseHas('items', [
            'user_id'      => $user->id,
            'condition_id' => $condition->id,
            'name'         => 'Test Item',
            'description'  => 'This is a test item',
            'price'        => 1234,
        ]);

        $item = Item::where('name', 'Test Item')->first();
        $this->assertNotNull($item, 'Item should exist');
        $this->assertCount(2, $item->categories, 'Item has 2 categories attached');

        $this->assertNotNull($item->img_url, 'img_url should be set in DB');
        $path = str_replace('/storage', '', $item->img_url);
        $this->assertTrue(Storage::disk('public')->exists($path), 'Image should be stored in public disk');
    }
}
