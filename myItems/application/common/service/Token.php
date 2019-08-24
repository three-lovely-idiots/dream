<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 2017/7/9
 * Time: 13:49
 */

namespace app\common\service;


use app\common\enum\ScopeEnum;
use app\common\exception\ForbiddenException;
use app\common\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;
use app\common\lib\EasyWechat;

class Token
{
    public static function generateToken(){
          $randCode = getRandChar(32);
          $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
          $salt = config('secure.token_salt');
         return md5($randCode.$timestamp.$salt);
    }


    /**
     * 一下都是老方法可能要废弃
     */
    public static function getCurrentUidByToken($key = 'id'){

        $vars = self::getCurrentVarsByToken();
        if(array_key_exists($key,$vars)){
                 return $vars[$key];
             }else{
            throw new TokenException();
        }
    }

    public static function getCurrentVarsByToken(){
        $token = Request::instance()->header('token');
        $vars = cache($token);
        if(!$vars){
            throw new TokenException();
        }else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
        }
        return $vars;
    }


    public static function needCheckPrimaryScope(){
        $scope = self::getCurrentVarsByToken()['scope'];
        if($scope>=ScopeEnum::USER){
            return true;
        }else{
            throw new ForbiddenException();
        }
   }

    public static function needCheckExclusiveScope(){
        $scope = self::getCurrentVarsByToken()['scope'];
        if($scope == ScopeEnum::USER){
            return true;
        }else{
            throw new ForbiddenException();
        }
    }

    public static function validateOperate($uid){

        if(!$uid){
            throw new Exception('检查uid时候必须传入一个uid');
        }
         $curUID = self::getCurrentUidByToken();

        if($curUID == $uid){
            return true;
        }

        return false;
    }
}