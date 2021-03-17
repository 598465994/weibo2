<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //获取出所有的用户
        $users = User::all();
        //获取第一个用户
        $user = $users->first();
        //第一个用户id
        $user_id = $user->id;

        //获取去掉 id=1 的所用用户 id 数组。 就是获取粉丝id
        $followers = $users->slice(1);
        //粉丝 id 数组。  pluck返回指定key的值组成的集合
        $follower_ids = $followers->pluck('id')->toarray();

        // 关注除了 1 号用户以外的所有用户
        $user->follow($follower_ids);

        // 除了 1 号用户以外的所有用户都来关注 1 号用户
        foreach ($followers as $key => $follower) {
            $follower->follow($user_id);
        }
    }
}
