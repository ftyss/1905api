<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test/pay','TestController@alipay');        //支付宝沙箱测试
Route::get('/test/alipay/return','Alipay\PayController@aliReturn');
Route::post('/test/alipay/notify','Alipay\PayController@notify');


Route::get('/api/test','Api\TestController@test');
Route::post('/api/user/reg','Api\TestController@reg');  // 用户注册
Route::post('/api/user/login','Api\TestController@login');  // 用户登录
Route::get('/api/show/data','Api\TestController@showData');  // 用户获取数据

Route::get('/api/user/list','Api\TestController@userList')->middleware('filter');  // 用户列表

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//curl测试
Route::get('/test/curl1','Test\CurlController@curl1');
Route::post('/test/curl2','Test\CurlController@curl2');
Route::post('/test/curl3','Test\CurlController@curl3');
Route::post('/test/curl4','Test\CurlController@curl4');
//
Route::get('/test/rsa1','TestController@rsa1');


//用户管理
Route::get('/user/addkey','User\IndexController@addSSHKey1');
Route::post('/user/addkey','User\IndexController@addSSHKey2');
//解密数据
Route::get('/user/decrypt/data','User\IndexController@decrypt1');
Route::post('/user/decrypt/data','User\IndexController@decrypt2');
//验证签名
Route::get('/sign1','TestController@sign1');
Route::get('/test/get/signonlie','Sign\IndexController@signOnline');
Route::post('/test/post/signonlie','Sign\IndexController@signOnline1');
Route::get('/test/get/sign1','Sign\IndexController@sign1');
Route::post('/test/post/sign2','Sign\IndexController@sign2');



