<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    /**
     * 自动执行
     */
    public function __construct()
    {
        /**
         * middleware::两个参数，一个时中间件名称，一个是要进行过滤的动作
         * auth::允许已登录访问
         * guest::未登录用户访问
         * except::指定不过滤的动作，首选except,这样新增控制器方法时，默认时安全的
         * only::指定过滤的动作
         */
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * 登录页面
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * 登录页面表单提交
     */
    public function store(Request $request)
    {
        //$credentials = ["email" => "Summer@text.com", "password" => "123"];
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        // 通过给定的信息来尝试对用户进行认证（成功后会自动启动会话）,第一个数组就是认证的参数: ['参数名'=>'参数值', '参数名'=>'参数值]，第二个参数true就是'记住我'功能
        if (Auth::attempt($credentials, $request->has('remember'))) {
            //登录成功后的相关操作
            session()->flash('success', '欢迎回来！');
            //重定向个人信息页面。。Auth::user()：：获取当前的认证用户，一个提供者的模型
            $fallback = route('users.show', Auth::user());
            return redirect()->intended($fallback);
        } else {
            //登录失败的相关操作
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');

            //重定向会登录页面，back()返回上一页，withInput()：获取到用户上一次提交的内容，视图页面用old来显示出来，这样用户就无需再次输入邮箱等内容
            // return redirect()->route('login')->withInput();
            return redirect()->back()->withInput();
        }

        return;
    }

    /**
     * 用户退出登录
     */
    public function destroy()
    {
        //使用户退出登录
        Auth::logout();

        session()->flash('success', '您已成功退出！');

        // 重定向到命名路由，就是路由别名name('login');
        return redirect()->route('login');
        // redirect重定向 URL 相当于在域名后面拼接login
        // return redirect('login');
    }
}
