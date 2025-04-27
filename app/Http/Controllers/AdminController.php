<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // ← これを忘れずに！

class AdminController extends Controller
{
    //
    public function index()
    {
        $userCount = User::count(); // ユーザー数を取得
        // return view('admin.index', compact('userCount')); // ビューに渡す
        return view('admin.dashboard', compact('userCount')); 
    }
}
