<?php

namespace app\common\service;
use think\Exception;
use app\common\exception\WeChatFailException;
use app\common\model\User as UserModel;
use app\common\service\Token;
use app\common\exception\TokenException;
use app\common\enum\ScopeEnum;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 17:36
 */
class UserToken extends Token
{


    public function verifyToken($token){
        $key =  cache($token);
        if(!$key){
            return false;
        }
        return true;
    }

    public static function grantToken($wxresult){
        //这里生成缓存数据需要的value
        $cacheValue = self::prepareCache($wxresult);
        //开始生成token并且返回数据
        self::saveToCache($cacheValue);

    }
    public static function saveToCache($cacheValue){
       $value = json_encode($cacheValue,true);
       $key =  $cacheValue['access_token'];       //不设过期时间了
//       $expire_in = config('setting.token_expire_in');

       $result = cache($key,$value);
       if(!$result){
             throw new TokenException([
                 'msg' => '服务器生成token错误',
                 'errorCode' => 10005
             ]);
       }
       return $key;

   }
    public static function prepareCache($wxresult){
        $cacheValue = $wxresult;
        $cacheValue['uid'] = $wxresult['id'];
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