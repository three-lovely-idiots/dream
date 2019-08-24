<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/25/19
 * Time: 12:53 AM
 */

namespace app\common\model;


class SelectImageUser extends Base
{
    public static function setUserLike($uid,$id){
        if($id && $uid){
            return self::create(['uid'=>$uid,'sid'=>$id]);
        }
        return false;
    }
}