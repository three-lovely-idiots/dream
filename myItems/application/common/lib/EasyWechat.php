<?php
namespace app\common\lib;

use EasyWeChat\Factory;
//use EasyWeChat\Kernel\Messages\Image;
//use Grafika\Color;
//use Grafika\Grafika;
use think\Controller;
use think\Db;

class EasyWechat extends Controller
{
    protected $miniapp;
    protected $wechat;
    protected $app_pay;
    public function _initialize()
    {
        //这个是微信公众哈的配置
        $wechat_config = [
            'app_id'        => 'wx2fa7a4f0a97fcedc',
            'secret'        => 'fc15b670e1ed572c5a6cfd60f1912569',
            //'token'         => 'JOfggDDrVhODrrGqnfRUvTGvthXdvtzV',
            'aes_key'       => '', // EncodingAESKey，兼容与安全模式下请一定要填写！！！
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log'           => [
                'level' => 'debug',
                'file'  => APP_LOG . 'wechat.log',
            ],
            /**
            * OAuth 配置
            *
            * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
            * callback：OAuth授权完成后的回调页地址
            */
            'oauth'         => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => 'http://lj.lxx123.club/index/Easy_Wechat/oauth_callback',
            ],
         ];
        //这个是微信小程序的配置
        $miniapp_config = [
            'app_id' => 'wx2fa7a4f0a97fcedc',
            'secret' => 'fc15b670e1ed572c5a6cfd60f1912569',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => APP_LOG.'miniapp.log',
            ],
        ];
       //支付配置
        $config_pay = [
        // 必要配置
        'app_id'     => '',
        'mch_id'     => '',
        'key'        => '', // API 密钥

        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path'  => 'D:/lxx/qiangdan/extend/wxpay/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
        'key_path'   => 'D:/lxx/qiangdan/extend/wxpay/cert/apiclient_key.pem', // XXX: 绝对路径！！！！
        'notify_url' => 'http://lj.lxx123.club/index/Easy_Wechat/pay_callback', // 你也可以在下单时单独设置来想覆盖它
        ];
        //微信公众号的初始化
        $this->wechat = Factory::officialAccount($wechat_config);
        //微信小程序初始化
        $this->miniapp = Factory::miniProgram($miniapp_config);
        $this->app_pay = Factory::payment($config_pay);
    }

    /**
     * @param $code 小程序前端传过来的jscode
     * @return mixed
     */
    public function getSessionByJSCode($code)
    {
        //获取用户session信息，通过jscode
        return $this->miniapp->auth->session($code);
    }


    public function getDecryptData($session_key, $iv, $encrypted_data)
    {
        //获取用户session信息，通过jscode
        return $this->miniapp->encryptor->decryptData($session_key, $iv, $encrypted_data);
    }

   public function getCodeUnlimit($params)
   {
       return $this->miniapp->app_code->getUnlimit('scene-value', $params);
        // $response 成功时为 EasyWeChat\Kernel\Http\StreamResponse 实例，失败为数组或你指定的 API 返回类型

   }

}