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
}
