<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Mail\TransactionCompleted;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    public function submitBuyerRating(Request $request)
    {
        $purchase = Purchase::findOrFail($request->input('purchase_id'));

        $purchase->buyer_rating = $request->input('rating');
        if ($purchase->buyer_rating && !$purchase->completed) {
            $purchase->completed = true; // 購入者の評価だけで取引完了
        }
        $purchase->save();

        $seller = $purchase->item->user;
        Mail::to($seller->email)->send(new TransactionCompleted($purchase));

        return redirect()->route('index');
    }

    public function submitSellerRating(Request $request)
    {
        $purchase = Purchase::findOrFail($request->input('purchase_id'));
        
        $purchase->seller_rating = $request->input('rating');

        if ($purchase->buyer_rating && $purchase->seller_rating) {
            $purchase->completed = true;
        }

        $purchase->save();

        return redirect()->route('index');
    }
}
