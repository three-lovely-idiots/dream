<?php

namespace app\common\service;
use think\Exception;
use app\common\exception\WeChatFailException;
use app\common\model\User as UserModel;
use app\common\service\Token;
use app\common\exception\TokenException;
use app\common\enum\ScopeEnum;
/**
 * Created by PhpStorm.  废弃的方案
 * User: Administrator
 * Date: 2017/7/8
 * Time: 17:36
 */
class UserTokenBefore extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginURL;

    public function __construct($code=''){
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginURL = sprintf(config('wx.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);
    }
    public function get(){
        $result = curl_get($this->wxLoginURL);
        $wxresult = json_decode($result,true);

       if(empty($wxresult)){
            throw new Exception('获取openid以及session_key失败，服务器内部错误');
       }else{
            $loginRes = array_key_exists('errcode',$wxresult);
           if($loginRes){
                $this->LoginFailProcess($wxresult);
           }else{
              $token = $this->grantToken($wxresult);
           }
           return $token;
       }
    }

    public function verifyToken($token){
        $key =  cache($token);
        if(!$key){
            return false;
        }
        return true;
    }

    public function grantToken($wxresult){
        $openid = $wxresult['openid'];
        $session_key = $wxresult['session_key'];
        $user =  UserModel::getUserByOpenid($openid);

        //判断是否存在用户
        if($user){
            $uid = $user->id;
        }else{
            $uid = $this->newUser($openid);
        }

        //这里生成缓存数据需要的value
       $cacheValue = $this->prepareCache($wxresult,$uid);
        //开始生成token并且返回数据
       $key =  $this->saveToCache($cacheValue);
       return ['token'=>$key,'uid'=>$uid];
    }
    public function saveToCache($cacheValue){
       $key = (new Token())->generateToken();
       $value = json_encode($cacheValue,true);
       $expire_in = config('setting.token_expire_in');

       $result = cache($key,$value);
       if(!$result){
             throw new TokenException([
                 'msg' => '服务器生成token错误',
                 'errorCode' => 10005
             ]);
       }
       return $key;

   }
    public function prepareCache($wxresult,$uid){
        $cacheValue = $wxresult;
        $cacheValue['uid'] = $uid;
        $cacheValue['scope'] = ScopeEnum::USER;
        //$cacheValue['scope'] = 15;
        return $cacheValue;
    }
    public function newUser($openid){
          $user =  UserModel::create([
              'openid'=>$openid
          ]);
         return $user->id;
    }
    public function LoginFailProcess($wxResult){
        throw new WeChatFailException([

        ]);
        throw new WeChatFailException([
               'msg' =>  $wxResult['errmsg'],
               'errCode' => $wxResult['errcode']
        ]);
    }


}