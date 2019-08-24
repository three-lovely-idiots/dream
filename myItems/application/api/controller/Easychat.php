<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9 0009
 * Time: 下午 10:28
 */

namespace app\api\controller;

use think\Controller;
use EasyWeChat\Factory;
class Easychat extends Controller
{
    protected $app;
    public function _initialize()
    {
        $config = [
            'app_id'        => 'wx9669ce214f0e136a',
            'secret'        => '8c0bb8589ce661932e91365569fe8d37',
            'token'         => 'cVc5BV8vzZVc8vyv8CDB5X8BVc7xaG71',
            'aes_key'       => '', // EncodingAESKey，兼容与安全模式下请一定要填写！！！
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log'           => [
                'level' => 'debug',
                'file'  => __DIR__ . '/wechat.log',
            ],
            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址
             */
            'oauth'         => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => 'http://lj.lxx123.club/api/Easychat/oauth_callback',
            ],
        ];
        $this->app = Factory::officialAccount($config);
    }
    /**
     * 微信接入地址,验证echostr
     * @return [type] [description]
     */
    public function response_echostr()
    {
        $this->app->server->serve()->send();
        die;
    }
    /**
     * 授权地址
     * @return [type] [description]
     */
    public function index()
    {
        $response = $this->app->oauth->scopes(['snsapi_userinfo'])->redirect();
        $response->send();
    }
    /**
     * 授权成功跳转地址,获得用户信息
     * @return [type] [description]
     */
    public function oauth_callback()
    {
        $user = $this->app->oauth->user();
        // $user 可以用的方法:
        // $user->getId();  // 对应微信的 OPENID
        // $user->getNickname(); // 对应微信的 nickname
        // $user->getName(); // 对应微信的 nickname
        // $user->getAvatar(); // 头像网址
        // $user->getOriginal(); // 原始API返回的结果
        // $user->getToken(); // access_token， 比如用于地址共享时使用
    }
}