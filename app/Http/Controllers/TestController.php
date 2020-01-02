<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function alipay()
    {
        $ali_gateway = 'https://openapi.alipaydev.com/gateway.do';  //支付网关
        // 公共请求参数
        $appid = '2016101100657125';
        $method = 'alipay.trade.page.pay';
        $charset = 'utf-8';
        $signtype = 'RSA2';
        $sign = '';
        $timestamp = date('Y-m-d H:i:s');
        $version = '1.0';
        $return_url = 'http://api1905.fangtaoys.com/test/alipay/return';       // 支付宝同步通知
        $notify_url = 'http://api1905.fangtaoys.com/alipay/notify';   //支付宝异步通知地址
        $biz_content = '';


        // 请求参数
        $out_trade_no = time() . rand(1111,9999);   //随机商户订单号
        $product_code = 'FAST_INSTANT_TRADE_PAY';
        $total_amount = 8888.88;
        $subject = '测试订单' . $out_trade_no;


        $request_param = [
            'out_trade_no' => $out_trade_no,
            'product_code' => $product_code,
            'total_amount' => $total_amount,
            'subject'      => $subject
        ];


        $param = [
            'app_id'    =>  $appid,
            'method'    =>  $method,
            'charset'   =>  $charset,
            'sign_type' =>  $signtype,
            'timestamp' =>  $timestamp,
            'version'   =>  $version,
            'return_url'=>  $return_url,
            'notify_url'=>  $notify_url,
            'biz_content'=> json_encode($request_param)
        ];


        //echo '<pre>';print_r($param);echo '</pre>';


        // 字典序排序
        ksort($param);
        
        // 2 拼接 key1=value1&key2=value2...
        $str = "";
        foreach($param as $k=>$v)
        {
            $str .= $k . '=' . $v . '&';
        }
        //echo 'str：'.$str;die;
        $str=rtrim($str,'&');


        // 3 计算签名
        $key = storage_path('keys/app_priv');
        $priKey = file_get_contents($key);
        //dd($priKey);
        $res = openssl_get_privatekey($priKey);
        //var_dump($res);echo '</br>';die;
        openssl_sign($str, $sign, $res, OPENSSL_ALGO_SHA256);
        $sign = base64_encode($sign);
        $param['sign'] = $sign;

        // 4 urlencode
        $param_str = '?';
        foreach($param as $k=>$v){
            $param_str .= $k.'='.urlencode($v) . '&';
        }
        $param_str = rtrim($param_str,'&');
        $url = $ali_gateway . $param_str;
        //发送GET请求
        //echo $url;die;
        header("Location:".$url);



        //$url = 'https://openapi.alipaydev.com/gateway.do?';
    }
}
