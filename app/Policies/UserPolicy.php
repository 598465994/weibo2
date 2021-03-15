<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 用户更新时的验证权限
     * User $currentUser::第一个参数默认为当前登录用户实例
     * User $user::第二个参数则为要进行授权的用户实例
     */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    /**
     * 用户删除
     * 必须是管理员才能删除
     * 用户不能删除自己
     *
     */
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id && $currentUser->is_admin;
    }
}
