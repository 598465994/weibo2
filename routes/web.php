<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

//注册
Route::get('/signup', 'UsersController@create')->name('signup');

//resource 定义路由资源
Route::resource('users', 'UsersController');
// //下面方法同等与上面一个方法
// //显示所用用户列表的页面
// Route::get('/users', 'UsersController@index')->name('users.index');
// //创建用户的页面
// Route::get('/users/create', 'UsersController@create')->name('users.create');
// //显示用户个人信息的页面
// Route::get('/users/{user}', 'UsersController@show')->name('users.show');
// //创建用户
// Route::post('/users', 'UsersController@store')->name('users.store');
// //编辑用户个人资料的页面
// Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');
// //更新用户
// Route::patch('/users/{user}', 'UsersController@update')->name('users.update');
// //删除用户
// Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');


// 会话
//显示登录页面
Route::get('/login', 'SessionsController@create')->name('login');
//登录页面的表单提交
Route::post('/login', 'SessionsController@store')->name('login');
//推出登录
Route::delete('/logout', 'SessionsController@destroy')->name('logout');


// 邮件激活页面
Route::get('singnup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');


// 忘记密码页面
// 填写email表单
Route::get('password/reset', 'PasswordController@showLinkRequestForm')->name('password.request');
// 邮件表单提交
Route::post('password/email', 'PasswordController@sendResetLinkEmail')->name('password.email');
// 更新密码的表单，喊token
Route::get('password/reset/{token}', 'PasswordController@showResetForm')->name('password.reset');
// 忘记密码页面提交表单，修改密码
Route::post('password/reset', 'PasswordController@reset')->name('password.update');


//resource 定义微博路由
Route::resource('statuses', 'StatusesController', ['only'=>['store', 'destroy']]);
// // 下面同等下面一句话
// //创建微博
// Route::post('statuses', 'StatusesController@store')->name('statuses.store');
// //删除微博
// Route::delete('statuses/{status}', 'StatusesController@destroy')->name('statuses.destroy');



//关注的用户列表页面
Route::get('users/{user}/followings', 'UsersController@followings')->name('users.followings');
//粉丝列表页面
Route::get('users/{user}/followers', 'UsersController@followers')->name('users.followers');
//关注用户
Route::post('users/followers/{user}', 'FollowersController@store')->name('followers.store');
//取消关注
Route::delete('users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');
