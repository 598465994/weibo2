<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{

    /**
     * 自动执行
     */
    public function __construct()
    {
        /**
         * Auth 中间件来验证用户的身份
         * 如果用户未通过身份验证，则 Auth 中间件会把用户重定向到登录页面
         * 如果用户通过了身份验证，则 Auth 中间件会通过此请求并接着往下执行
         * 只允许已登录用户访问之外
         */
        $this->middleware('auth', [
            //except 方法来设定 指定动作 不使用 Auth 中间件进行过滤。。就是一下动作不需要中间件auth来处理，，， 除了此处指定的动作以外，所有其他动作都必须登录用户才能访问
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        /**
         * guest 中间件是没有用户登录
         * 用于指定一些只允许未登录用户访问的动作
         */
        $this->middleware('guest', [
            //only 方法来设定 指定动作 使用 gust 中间件进行过滤。。。此处指定的动作以外，所有其他动作必须要登录才能访问。。。。就是登录过后的用户不能再打开注册页面
            'only' => ['create']
        ]);

        // 注册限流，1小时只能提交10次
        $this->middleware('throttle:60,10', [
            'only' => ['store']
        ]);
    }

    /**
     * 用户注册页面
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 显示用户个人信息的页面
     */
    public function show(User $user)
    {
        $statuses = $user->statuses()
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        return view('users.show', compact('user', 'statuses'));
    }

    /**
     * 创建用户
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        // create方法是插入成功返回一个新的模型实例
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $this->sendEmailConfirmationTo($user);

        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect()->route('home');
    }

    /**
     * 邮件发送
     */
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'summer@example.com';
        $name = 'Summer';
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";
        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    /**
     * 所用用户列表
     */
    public function index()
    {
        $users = User::paginate(6);
        return view('users.index', compact('users'));
    }

    /**
     * 编辑用户资料页面
     * 这里的User $user 隐性路由模型绑定get传递的参数查询出对应的用户id信息
     */
    public function edit(User $user)
    {
        /**
         * authorize::方法来验证用户授权策略
         * 第一个参数是::授权策略名称
         * 第二个参数是::进行授权验证的数据
         */
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * 用户信息修改
     * $user 第一个为自动解析用户 id 对应的用户实例对象
     * $request 第二个则为更新用户表单的输入数据
     */
    public function update(Uesr $user, Request $request)
    {
        //验证用户授权策略
        $this->authorize('update', $user);

        // nullable提交空白也能通过
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user->id);
    }

    /**
     * 删除用户
     *
     */
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    /**
     * 邮件激活
     */
    public function confirmEmail($token)
    {
        // firstOrFail取出第一个用户
        $user = User::where('activation_token', $token)->firstOrFail();

        // 更新用户数据
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        //用户登录
        Auth::login($user);

        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }


    /**
     * 关注的用户列表页面
     */
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    /**
     * 用户的粉丝列表页面
     */
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }

}
