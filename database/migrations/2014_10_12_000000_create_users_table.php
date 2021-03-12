<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * 当我们运行迁移时，up方法会被调用
     *
     * @return void
     */
    public function up()
    {
        //Schema类的create方法来创建表。create 方法会接收两个参数：一个是数据表的名称，另一个则是接收 $table（Blueprint 实例）的闭包。这里$table就是值得时users表
        Schema::create('users', function (Blueprint $table) {
            $table->id(); //id() 是 bigIncrements() 的封装，此方法创建了一个 bigint unsigned 类型的自增长 id。
            $table->string('name'); //由 string 方法创建了一个 name 字段， 用于保存用户名称。
            $table->string('email')->unique(); //由 string 方法创建了一个 email 字段，且在最后指定该字段的值为唯一值，用于保存用户邮箱。
            $table->timestamp('email_verified_at')->nullable(); //Email 验证时间，空的话意味着用户还未验证邮箱。nullable：为空
            $table->string('password', 60); //由 string 方法创建了一个 password 字段，且在 string 方法中指定保存的值最大长度为 60，用于保存用户密码。
            $table->rememberToken(); //由 rememberToken 方法为用户创建一个 remember_token 字段，用于保存『记住我』的相关信息。
            $table->timestamps(); //由 timestamps 方法创建了一个 created_at 和一个 updated_at 字段，分别用于保存用户的创建时间和更新时间。
        });
    }

    /**
     * Reverse the migrations.
     * 当我们回滚迁移时，down方法会被调用
     *
     * @return void
     */
    public function down()
    {
        //dropIfExists时删除表
        Schema::dropIfExists('users');
    }
}
