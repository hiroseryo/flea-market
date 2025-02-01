<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\SoldItem;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function checkout(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $paymentMethod = $request->input('payment_method');

        $soldItem = SoldItem::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->whereIn('payment_status', ['pending', 'cancelled'])->first();

        if (!$soldItem) {
            $soldItem = SoldItem::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'payment_status' => 'pending',
                'stripe_session_id' => null,
                'payment_intent_id' => null,
            ]);
        } else {
            $soldItem->update([
                'payment_status' => 'pending',
                'stripe_session_id' => null,
                'payment_intent_id' => null,
            ]);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentTypes = ($paymentMethod === 'konbini') ? ['konbini'] : ['card'];

        $session = StripeSession::create([
            'payment_method_types' => $paymentTypes,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('purchase.cancel'),
        ]);

        $soldItem->update([
            'stripe_session_id' => $session->id,
            'payment_intent_id' => $session->payment_intent ?? null,
            'payment_status' => 'pending',
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('mypage.index', ['page' => 'buy'])->with('error', '購入処理に失敗しました');
        }

        $soldItem = SoldItem::where('stripe_session_id', $sessionId)->first();
        if (!$soldItem) {
            return redirect()->route('mypage.index', ['page' => 'buy'])->with('error', '購入商品が見つかりません');
        }

        $soldItem->update(['payment_status' => 'paid']);

        return redirect()->route('mypage.index', ['page' => 'buy'])->with('status', '購入が完了しました');
    }

    public function cancel()
    {
        return redirect()->route('mypage.index', ['page' => 'buy'])->with('error', '購入がキャンセルされました');
    }
}
