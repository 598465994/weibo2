<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
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
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store']
        ]);
        $this->middleware('guest', [
            'only' => ['create']
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
        return view('users.show', compact('user'));
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

        Auth::login($user);

        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
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
}
