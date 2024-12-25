<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id)
    {
        Comment::create([
            'item_id' => $item_id,
            'user_id' => Auth::id(),
            'content' => $request->comment,
        ]);

        return redirect()->route('item.show', ['item_id' => $item_id]);
    }

}
