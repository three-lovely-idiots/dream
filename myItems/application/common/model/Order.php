<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/13
 * Time: 12:13
 */

namespace app\common\model;


class Order extends Base
{
     protected $hidden = ['user_id','delete_time','update_time'];

     public function img(){
          return $this->belongsTo('Image','img_id','id');
     }
     public function orderProduct(){
          return $this->hasMany('OrderProduct','order_id','id');

     }

     public function getSnapImgAttr($value,$data){
         $img_perfix = config('img_perfix');
         return $img_perfix.$value;
     }
     public static function getOrderInfo($id){
          return self::with(['orderProduct'])->select($id);
     }

     public static function getUpaiedOrderTotalNum($uid){
          return self::where(['status'=>1,'user_id'=>$uid])->count();
     }

     public static function getUserOrderGroupByStatus($uid){
         return self::where(['user_id'=>$uid])->group('status')->field('status,count(*) as num')->select()->toArray();
     }
}