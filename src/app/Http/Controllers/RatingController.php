<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;

class RatingController extends Controller
{
    public function submitBuyerRating(Request $request)
    {
        $purchase = Purchase::findOrFail($request->input('purchase_id'));

        $purchase->buyer_rating = $request->input('rating');
        $purchase->save();



        return redirect()->route('index');
    }

    public function submitSellerRating(Request $request)
    {
        $purchase = Purchase::findOrFail($request->input('purchase_id'));
        
        $purchase->seller_rating = $request->input('rating');
        
        // 両者が評価した場合、取引完了
        if ($purchase->buyer_rating && $purchase->seller_rating) {
            $purchase->completed = true;
        }

        $purchase->save();

        return redirect()->route('index');
    }
}
