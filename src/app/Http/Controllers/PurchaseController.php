<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();
        $shippingAddress = session('shipping_address', [
            'shipping_postal_code' => $user->postal_code,
            'shipping_address_line' => $user->address_line,
            'shipping_building' => $user->building,
        ]);

        return view('confirm', compact('item', 'shippingAddress'));
    }

    public function edit($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        return view('address', compact('item', 'user'));
    }

    public function update(AddressRequest $request, $item_id)
    {
        $user = auth()->user();
        $request->session()->put('shipping_address', [
            'shipping_postal_code' => $request->postal_code,
            'shipping_address_line' => $request->address_line,
            'shipping_building' => $request->building,
        ]);

        return redirect()->route('purchase.index', ['item_id' => $item_id]);
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        $shippingAddress = session('shipping_address');
        if (! $shippingAddress) {
            $shippingAddress = [
                'shipping_postal_code' => $user->postal_code,
                'shipping_address_line' => $user->address_line,
                'shipping_building' => $user->building,
            ];
        }
        $purchaseData = array_merge(
            $request->only(['payment_method']),
            $shippingAddress,
            ['user_id' => $user->id, 'item_id' => $item_id]
        );
        Purchase::create($purchaseData);
        $item->update(['sold_out' => true]);

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $amount = (int) $item->price;
        $paymentMethod = $request->input('payment_method');
        $paymentMethods = ['card'];
        if ($paymentMethod === 'konbini') {
            $paymentMethods = ['konbini'];
        }
        $session = Session::create([
            'payment_method_types' => $paymentMethods,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('profile.show', ['item_id' => $item_id]),
            'cancel_url' => route('profile.show', ['item_id' => $item_id]),
        ]);

        return redirect()->away($session->url);
    }
}
