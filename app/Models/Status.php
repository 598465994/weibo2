<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    /**
     * 指明一条微博属于一个用户
     * 一对一
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The attributes that are mass assignable.
     * fillable：过滤用户提交的字段，只用包含在该属性中的字段才能被正常更新
     *
     * @var array
     */
    protected $fillable = [
        'content',
    ];
}
