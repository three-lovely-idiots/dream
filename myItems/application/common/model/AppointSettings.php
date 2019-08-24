<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19 0019
 * Time: 下午 4:06
 */

namespace app\common\model;


class AppointSettings extends Base
{
    public static function getSettings(){
        return self::find(1);
    }

    public static function setSettings($data){
        if($data['id']){//update
            return self::update($data);
        }else{//save
            unset($data['id']);
            return self::create($data);
        }
    }

}