<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
//HasFactory 是模型工厂相关功能的引用
use Illuminate\Database\Eloquent\Factories\HasFactory;
//Authenticatable 是授权相关功能的引用
use Illuminate\Foundation\Auth\User as Authenticatable;
//Notifiable 是消息通知相关功能引用
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    //table属性来定义要进行数据交互的数据库表名称
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     * fillable：过滤用户提交的字段，只用包含在该属性中的字段才能被正常更新
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * 当我们需要对用户密码或其它敏感信息在用户实例通过数组或 JSON 显示时进行隐藏
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 生成用户头像
     */
    public function gravatar($size = '100')
    {
        //获取到用户邮箱
        $user_email = $this->attributes['email'];
        //剔除邮箱的前后空白
        $user_email = trim($user_email);
        //将邮箱转换为小写
        $user_email = strtolower($user_email);
        //将邮箱md5转码
        $hash = md5($user_email);
        //将邮箱与链接、尺寸拼接成完整的url
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    /**
     * boot 方法会在用户模型类完成初始化之后进行加载，因此我们对事件的监听需要放在该方法中
     */
    public static function boot()
    {
        parent::boot();
        //用户的激活令牌需要在用户创建（注册）之前就先生成好
        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }

    /**
     * 指明一用户拥有多条微博。因此在用户模型中我们使用了微博动态的复数形式 statuses 来作为定义的函数名
     * 一对多
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }


    /**
     * 将当前用户发布过的所有微博从数据库中取出
     */
    public function feed()
    {
        /**
         * 通过 followings 方法去除所用关注人列表的信息
         * pluck  将 id 分离并负值给 user_ids
         */
        $user_ids = $this->followings->pluck('id')->toArray();

        //将用户 id 加入到 当前  user_ids 数组中
        array_push($user_ids, $this->id);

        // 使用 Laravel 提供的 查询构造器 whereIn 方法取出所有用户的微博动态并进行倒序排序；
        $weibo_lists = Status::whereIn('user_id', $user_ids)
                                ->with('user')
                                ->orderBy('created_at', 'desc');

        return $weibo_lists;
    }


    /**
     * 用户关联粉丝，，，获取粉丝列表     就是用户查看自己有哪些粉丝
     * 多对多
     */
    public function followers()
    {
        // // 在 Laravel 中会默认将两个关联模型的名称进行合并，并按照字母排序，因此我们生成的关联关系表名称会是 user_user
        // return $this->belongsToMany(User::class);

        // // 自定义生成的名称，把关联表名改为 followers
        // return $this->belongsToMany(User::class, 'followers');

        /**
         * 通过传递额外参数至 belongsToMany 方法来自定义数据表里的字段名称
         *  方法的第三个参数 user_id 是定义在关联中的模型外键名
         * 第四个参数 follower_id 则是要合并的模型外键名
         */
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    /**
     * 粉丝关联用户，，，获取关注人列表    就是粉丝查看自己关注了哪些用户
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }


    /**
     * 定义关注
     */
    public function follow($user_ids)
    {
        // is_array:用户判断参数是否未数组
        // 如果 $user_ids 不是数组
        if ( ! is_array($user_ids) ) {
            // 把  $user_ids  函数创建一个包含变量名和它们的值的数组
            $user_ids = compact('user_ids');
        }

        //添加粉丝
        // attach  能重复添加
        // sync  不会重复添加 会接收两个参数，第一个参数是要进行添加的 id ，第二个参数指明是否要移除其他不包含在关联的  id  数组中的  id  ，true 表示移除， false 表示不移除。 默认值未 true 。 我们关注一个新用户的时候 任然要保持之前已关注的关系，不能移除， 这里填写false
        return $this->followings()->sync($user_ids, false);
    }

    /**
     * 定义取消关注
     */
    public function unfollow($user_ids)
    {

        if ( ! is_array($user_ids) ) {
            $user_ids = compact('user_ids');
        }

        return $this->followings()->detach($user_ids);
    }

    /**
     * 用户A是否关联用户B
     */
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }


}
