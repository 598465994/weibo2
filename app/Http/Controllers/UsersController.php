<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    /**
     * 用户注册
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
     * 编辑用户资料页面
     * 这里的User $user 隐性路由模型绑定get传递的参数查询出对应的用户id信息
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * 用户信息修改
     * $user 第一个为自动解析用户 id 对应的用户实例对象
     * $request 第二个则为更新用户表单的输入数据
     */
    public function update(Uesr $user, Request $request)
    {
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
