<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17 0017
 * Time: 下午 5:46
 */

namespace app\common\service;


class Util
{
    public static function showMsg($code,$msg){
        if($code){
            $return = [
                'code' => $code,
                'msg' => $msg
            ];

            return $return;
        }
    }
}