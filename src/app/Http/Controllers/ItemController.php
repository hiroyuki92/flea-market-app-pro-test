<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;

class ItemController extends Controller
{
public function index()
    {
        return view('index');
    }

public function create()
    {
        return view('create');
    }

public function show()
    {
        return view('show');
    }

public function purchase()
    {
        return view('confirm');
    }

public function update()
    {
        return view('address');
    }
}



