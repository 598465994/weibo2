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


    /**
     * 删除微博
     */
    public function destroy(Status $status)
    {
        //自动授权策略，表示当前微博的 user_id 等于 用户的 id，才往下执行， 不等于就会报错
        $this->authorize('destroy', $status);

        //删除数据
        $status->delete();

        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }
}
