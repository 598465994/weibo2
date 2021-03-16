<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Status;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
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
     * 微博删除
     * 用户只能删除自己的
     *
     */
    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id;
    }
}
