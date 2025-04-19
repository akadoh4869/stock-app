<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * 退会処理
     */
    public function withdraw()
    {
        $user = Auth::user();

        // 論理削除（SoftDeletesを使っていれば）
        $user->delete();

        Auth::logout();

        return redirect('/')->with('message', '退会が完了しました');
    }
}
