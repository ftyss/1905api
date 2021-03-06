<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;

class TestController extends Controller
{
    public function test()
    {
        echo '<pre>';print_r($_SERVER);echo '</pre>';
    }

    // 用户注册
    public function reg0(Request $request)
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

    public function login0(Request $request)
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




    public function reg()
    {
        //$url="http://passport1905.com/api/user/reg";
        $url="http://passport.fangtaoys.com/api/user/reg";
        $response=UserModel::curlPost($url,$_POST);
        return $response;
    }

    public function login()
    {
        //$url="http://passport1905.com/api/user/login";
        $url="http://passport.fangtaoys.com/api/user/login";
        $response=UserModel::curlPost($url,$_POST);
        return $response;
    }

    public function showData()
    {
        $uid=$_SERVER['HTTP_UID'];
        $token=$_SERVER['HTTP_TOKEN'];
        echo "uid：".$uid;echo '</br>';
        echo "token：".$token;echo '</br>';

        //$url="http://passport1905.com/api/auth";    //鉴权接口
        $url="http://passport.fangtaoys.com/api/auth";
        $response=UserModel::curlPost($url,['uid'=>$uid,'token'=>$token]);
        //echo '<pre>';print_r($response);echo '</pre>';die;
        $status=json_decode($response,true);
        
        if($status['errno']==0)
        {
            $data=119;
            $response=[
                'errno'=>0,
                'msg'=>'ok',
                'data'=>$data
            ];
        }else{                                      
            $response=[
                'errno'=>40003,
                'msg'=>'授权失败'
            ];
        }
        return $response;

    }


    /**
     * 2月4日   接口测试
     */
    public function postman()
     {
         echo __METHOD__;
     }
 
     public function postman1()
     {

        $data = [
            'name'      => 'zhang',
            'age'       => '22',
            'email'     => 'zhang@qq.com',
        ];

        echo json_encode($data);

        //  //获取用户标识
        //  $token = $_SERVER['HTTP_TOKEN'];
        //  // 当前url
        //  $request_uri = $_SERVER['REQUEST_URI'];
 
        //  $url_hash = md5($token . $request_uri);
 
 
        //  $key = 'count:url:'.$url_hash;
        //  //echo 'Key: '.$key;echo '</br>';
 
        //  //检查 次数是否已经超过限制
        //  $count = Redis::get($key);
        //  echo "当前访问次数：" . $count;echo '<hr>';
 
        //  if($count >= 5){
        //      $time = 10;     
        //      echo "请勿频繁请求, $time 秒后重试";
        //      Redis::expire($key,$time);
        //      die;
        //  }
 
        //  // 访问数 +1
        //  $count = Redis::incr($key);
        //  echo '已访问次数为 : '. $count;
 
     }

     public function md5test()
     {
         $data="Chinese fireman";
         $key="1905";

         //md5计算签名
         $signature=md5($data.$key);
         //$signature='asgkdjagheriajklghie';
         echo "待发送数据 ：".$data;echo '</br>';
         echo "签名 ：".$signature;echo '</br>';

         //发送数据
         $url = "http://passport1905.com/test/check?data=".$data.'&signature='.$signature;
         echo $url;echo '<hr>';
         $response=file_get_contents($url);
         echo $response;
     }

     public function md5test1()
     {
         //待签名数据
         $order_info=[
            'name'      => 'zhang',
            'age'       => '22',
            'email'     => 'zhang@qq.com',
         ];
         $key='1905';
        $data_json=json_encode($order_info);
        $sign=md5($data_json.$key);     //计算签名
        echo "待发送数据 ：".$data_json;echo '</br>';
        echo "签名 ： ".$sign;echo '</br>';
        
        //post 发送数据
        $client=new Client();
        $response = $client->request('POST', 'http://passport1905.com/test/check1', [
            'form_params' => [
                "data"=>$data_json,
                "sign"=>$sign
            ]
        ]);

        //接收响应
        $response_data=$response->getBody();
        echo '<hr>';
        echo $response_data;
     }

     public function md5test2()
     {
        //待签名数据
        $data="Chinese fireman";
        echo "原始数据 ：".$data;echo '</br>';
        //计算签名
        $path=storage_path('keys/priv.key');
        $pkeyid=openssl_pkey_get_private("file://".$path);

        openssl_sign($data,$signature,$pkeyid);
        openssl_free_key($pkeyid);
        //var_dump($signature);
        echo "原数据签名后 ：".$signature;echo '</br>';

        //base64编码
        $sign_str=base64_encode($signature);
        echo "base64_encode后数据 ：".$sign_str;echo '<hr>';

        $url="http://passport1905.com/test/check2?".'data='.$data.'&sign='.urlencode($sign_str);
        //echo $url;
        $response=file_get_contents($url);
        echo $response;

     }

     /**
      * 对称加密
      */
    public function encryption()
    {
        $data="hello world";
        echo "原密文 ：".$data;echo '</br>';
        $method='AES-256-CBC';
        $key='1905';
        $iv='amfkjjjdsvkwdsja';

        //加密
        $enc_data=openssl_encrypt($data,$method,$key,OPENSSL_RAW_DATA,$iv);
        echo "加密密文 ：".$enc_data;echo '</br>';
        //echo 123;die;
        //发送数据
        $url="http://passport1905.com/test/decrypt?data=".urlencode(base64_encode($enc_data));
        echo "传输的url数据 ：".$url;echo '<hr>';
        
        $response=file_get_contents($url);
        echo $response;
    }

    /**
     * 非对称加密
     */
    public function encryption2()
    {
        $data="Chinese fireman";
        echo "原始数据  ：".$data;echo '</br>';
        
        //加密
        $priv_key=file_get_contents(storage_path('keys/priv.key'));
        openssl_private_encrypt($data,$enc_data,$priv_key);
        echo "加密密文 ：".$enc_data;echo '</br>';

        //发送数据
        $url="http://passport1905.com/test/decrypt2?data=".urlencode(base64_encode($enc_data));
        echo "传输的url数据 ：".$url;echo '<hr>';
        $response=file_get_contents($url);echo '</br>';
        echo $response;
    }

}
