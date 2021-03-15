<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
// 提供了一些处理字符串方法
use Illuminate\Support\Str;
use DB;
use Mail;
use Carbon\Carbon;

class PasswordController extends Controller
{
    /**
     * 填写email页面
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * 邮箱提交表单，发送邮件
     */
    public function sendResetLinkEmail(Request $request)
    {
        //1、验证邮箱
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        //邮箱
        $email = $request->email;

        //获取对应用户信息
        $user = User::where('email', $email)->first();

        //如果用户不存在
        if (is_null($user)) {
            // 显示错误信息
            session()->flash('danger', '邮箱未注册');
            //返回上一页，并显示表单的旧数据
            return redirect()->back()->withInput();
        }

        // 生成token
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        //添加到数据库，使用 updateOrInsert 来保持 Email 唯一
        DB::table('password_resets')->updateOrInsert(
            [
                'email' => $email
            ], [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => new Carbon,
            ]
        );

        //发送邮件
        Mail::send('emails.reset_link', compact('token'), function ($message) use ($email) {
            $message->to($email)->subject("忘记密码");
        });

        session()->flash('success', '重置邮件发送成功，请查收');

        return redirect()->back();
    }

    /**
     * 找回密码页面
     */
    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');
        return view('auth.passwords.reset', compact('token'));
    }

    /**
     * 重置密码
     */
    public function reset(Request $request)
    {
        //验证数据
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8'
        ]);

        $email = $request->email;
        $token = $request->token;

        //找回密码链接有效时间
        $expires = 60 * 10;

        // 获取对应用户
        $user = User::where('email', $email)->first();

        // 用户不存在
        if (is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        //读取充值记录
        $record = (array) DB::table('password_resets')->where('email', $email)->first();

        //如果记录不存在
        if (is_null($request)) {
            session()->flash('danger', '未找到重置记录');
            return redirect()->back();
        }

        //检查是否过期。。当前时间不在添加时间和有效时间内
        if (Carbon::parse($record['created_at'])->addSeconds($expires)->isPast()) {
            session()->flash('danger', '令牌错误');
            return redirect()->back();
        }

        // 更新用户密码
        $user->update(['password' => bcrypt($request->password)]);

        // 5.4. 提示用户更新成功
        session()->flash('success', '密码重置成功，请使用新密码登录');
        return redirect()->route('login');
    }
}
