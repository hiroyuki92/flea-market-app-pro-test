<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionChatController extends Controller
{
    public function index(Request $request)
    {
        return view('transaction-chat');
    }
}
