<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    public function test()
    {
        echo '<pre>';print_r($_SERVER);echo '</pre>';
    }

    // 用户注册
    public function reg(Request $request)
    {
        echo '<pre>';print_r($request->input());echo '</pre>';
        // 验证用户名 验证email 验证手机号
        $pass1 = $request->input('pass1');
        $pass2 = $request->input('pass2');

        if($pass1 != $pass2){
            die('两次输入的密码不一致');
        }

        $password = password_hash($pass1,PASSWORD_BCRYPT);

        $data = [
            'email'      => $request->input('email'),
            'name'       => $request->input('name'),
            'password'      => $password,
            'mobile'      => $request->input('mobile'),
            'last_login'      => time(),
            'last_ip'      => $_SERVER['REMOTE_ADDR'],       // 获取远程IP
        ];

       $uid = UserModel::insertGetId($data);
       var_dump($uid);
    }

    public function login(Request $request)
    {
        $name = $request->input('name');
        $pass = $request->input('pass');
        // echo "pass：" . $pass;

        $u = UserModel::where(['name'=>$name])->first();
        // var_dump($u);

        if($u){
            // echo '<pre>';print_r($u->toArray());echo '</pre>';
            // 验证密码
            if(password_verify($pass,$u->password)){
                // 登录成功
                // echo '登陆成功';
                // 生成token
                $token = Str::random(32);
                // echo $token;

                $response = [
                    'error' => 0,
                    'msg'   => 'ok',
                    'data'  => [
                        'token' => $token
                    ]
                ];
                return $response;
            }else{
                // echo "密码不正确";
                $response = [
                    'error' => 400003,
                    'msg'   => '密码不正确',
                ];
            }
            // $res = password_verify($pass,$u->password);
            // var_dump($res);
        }else{
            // echo "没有此用户";
            $response = [
                'error' => 400004,
                'msg'   => '用户不存在',
            ];
        }
        
        return $response;
    }

    /**
     * 获取用户列表
     * 
     */
    public function userList()
    {
        $list=UserModel::all();
        echo '<pre>';print_r($list->toArray());echo '</pre>';
    }

}
