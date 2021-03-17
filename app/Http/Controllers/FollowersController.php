<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class FollowersController extends Controller
{
    public function __construct()
    {
        //用户登录后，请求过滤
        $this->middleware('auth', [

        ]);
    }

    /**
     * 关注用户
     */
    public function store(User $user)
    {
        //策略不能自己关注自己
        $this->authorize('follow', $user);

        //检测用户是否已经被关注了, 如果没有关注
        if ( ! Auth::user()->isFollowing($user->id) ) {
            //关注用户
            Auth::user()->follow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }

    /**
     * 取消关注
     */
    public function destroy(User $user)
    {
        //策略用户不能关注自己
        $this->authorize('follow', $user);

        //检测用户是否已经被关注了， 如果被关注了
        if ( Auth::user()->isFollowing($user->id) ) {
            //取消关注用户
            Auth::user()->unfollow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }
}
