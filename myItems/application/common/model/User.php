<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 17:36
 */

namespace app\common\model;


class User extends Base
{
     public function UserAddress(){
          return $this->hasOne('UserAddress','user_id','id');
     }
     public static function getUserByOpenid($openid){
          return self::where('openid','=',$openid)->find();
     }

//     public static function getUserAndAddressByUID($uid){
//         return self::with('UserAddress')->select($uid);
//     }

    public function SelectedImage(){
        return $this->belongsToMany('SelectedImage','selected_image_id','sid','uid');
    }

}