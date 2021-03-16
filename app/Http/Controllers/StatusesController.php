<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StatusesController extends Controller
{

    public function __construct()
    {
        /**
         * Auth 中间件来验证用户的身份时
         * 如果用户未通过身份验证，则 Auth 中间件会把用户重定向到登录页面
         * 如果用户通过了身份验证，则 Auth 中间件会通过此请求并接着往下执行
         */
        $this->middleware('auth', [

        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request->content
        ]);

        session()->flash('success', '发布成功！');
        return redirect()->back();
    }
}
