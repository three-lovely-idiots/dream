<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6
 * Time: 11:17
 */

namespace app\common\model;


use app\common\model\Base;


class BannerItem extends Base
{
     protected $table = 'banner_item';
     protected $hidden = ['delete_time','update_time'];

     public function getTypeAttr($value,$data)
     {
         $ret = [];
         if(request()->module() == 'admin'){
             $banner_item_type = config('banner_item_type');
             $type = isset($banner_item_type[$value])? $banner_item_type[$value] : '';
             if($value == 1){//商品
                 $info = Product::getProductByID($data['key_word']);
             }else{
                 $info = Product::getProductByID($data['key_word']);
             }
             $ret['type_id'] = $value;
             $ret['type'] = $type;
             $ret['info'] = $info;
         }else{
             //前端就什么也不做了
             $ret = $value;
         }
         return $ret;
     }
     public function img(){
         return $this->belongsTo('Image','img_id','id');
     }
   //为了不和之前的数据冲突 前端用另一套关联函数
    public function indexImg(){
        return $this->belongsTo('Image','img_id','id')->field('id,url,from');
    }

     public function banner()
     {
         return $this->belongsTo('Banner','banner_id','id');
     }

     public function product()
     {
         return $this->belongsTo('Product','key_word','id');
     }

    public static function getBannerItemDetail($id){
        return self::with(['img'])->where('banner_id','=',$id)->select();
    }

    public static function getBannerItemByWhere($where,$limit,$offset){
        return self::with(['img','banner'])->where($where)->limit($offset, $limit)->order('id desc')->select();
        //return self::with(['img'])->where('banner_id','=',$id)->select();
    }

    public static function getAllBannerItem($where)
    {
        return count(self::where($where)->select());
    }

    public static function getBannerItemByID($id)
    {
        return self::with(['img','banner'])->where('id','=',$id)->find();
    }

}