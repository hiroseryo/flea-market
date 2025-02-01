<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_address_and_see_it_on_purchase_screen()
    {
        $user = User::factory()->create(
            [
                'name' => 'Existing Name',
            ]
        );
        Auth::login($user);

        Profile::factory()->create([
            'user_id' => $user->id,
            'postcode' => '000-0000',
            'address'  => 'Old Address',
            'building' => 'Old Building',
        ]);

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Item',
            'price' => 5000,
        ]);

        $formResponse = $this->get("/purchase/address/{$item->id}");
        $formResponse->assertStatus(200);
        $formResponse->assertViewIs('address');

        $newPostcode = '123-4567';
        $newAddress  = 'New Pref New City 1-2-3';
        $newBuilding = 'New Building 101';

        $updateResponse = $this->post("/purchase/address/{$item->id}", [
            'name' => $user->name,
            'postcode' => $newPostcode,
            'address'  => $newAddress,
            'building' => $newBuilding,
        ]);

        $updateResponse->assertStatus(302);
        $updateResponse->assertSessionHas('status', '住所が更新されました');
        $updateResponse->assertRedirect(route('items.checkoutForm', ['item_id' => $item->id]));

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'postcode' => $newPostcode,
            'address' => $newAddress,
            'building' => $newBuilding,
        ]);

        $checkoutResponse = $this->get(route('items.checkoutForm', ['item_id' => $item->id]));
        $checkoutResponse->assertStatus(200);

        $checkoutResponse->assertSee($newPostcode);
        $checkoutResponse->assertSee($newAddress);
        $checkoutResponse->assertSee($newBuilding);
    }

    // プロフィール編集画面を表示と更新
    public function test_profile_edit_and_update()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);
        Profile::factory()->create([
            'user_id'   => $user->id,
            'postcode'  => '000-0000',
            'address'   => 'Old Address',
            'building'  => 'Old Bld',
            'img_url'   => '/storage/images/old_profile.png',
        ]);

        Auth::login($user);

        $response = $this->get('/profile');

        $response->assertStatus(200);

        $response->assertViewIs('profile');

        $response->assertSee('Old Name');
        $response->assertSee('000-0000');
        $response->assertSee('Old Address');
        $response->assertSee('Old Bld');
        $response->assertSee('old_profile.png');

        $newName     = 'New Name';
        $newPostcode = '123-4567';
        $newAddress  = 'New City 1-2-3';
        $newBuilding = 'New Bld 101';

        Storage::fake('public');
        $uploadedFile = UploadedFile::fake()->image('new_image.png');

        $updateData = [
            'name'     => $newName,
            'postcode' => $newPostcode,
            'address'  => $newAddress,
            'building' => $newBuilding,
            'image'    => $uploadedFile,
        ];

        $updateResponse = $this->post('/profile/update', $updateData);

        $updateResponse->assertStatus(302);
        $updateResponse->assertSessionHas('status', 'プロフィールを更新しました');

        $updateResponse->assertRedirect('/?page=mylist');

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'name' => $newName,
        ]);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'postcode' => $newPostcode,
            'address' => $newAddress,
            'building' => $newBuilding,
        ]);

        $profile = $user->profile->fresh();
        $this->assertNotNull($profile->img_url, 'img_url should be updated');

        $this->assertStringContainsString('/storage/images/', $profile->img_url);
        $filename = str_replace('/storage/images/', 'images/', $profile->img_url);
        $this->assertTrue(Storage::disk('public')->exists($filename));
    }
}
