<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;

class checkToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       // echo 123;die;
        //验证 token是否有效
        $uid = $_SERVER['HTTP_UID'];
        $token = $_SERVER['HTTP_TOKEN'];
        
        //请求passport
        $client = new Client();
        $response = $client->request('POST', 'http://passport1905.com/api/auth', [
            'form_params' => [
                'uid' => $uid,
                'token' => $token,
            ]
        ]);
        $response_data = $response->getBody();
        $arr = json_decode($response_data,true);

        //判断
        if($arr['errno']>0){        
            echo "鉴权失败";die;
        }

        return $next($request);

    }
}
