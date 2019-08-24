<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 9:39
 */

namespace app\common\model;

use think\Cache;

class Category extends Base
{
    public function topicImg(){
        return $this->belongsTo('Image','topic_img_id','id')->field(['from','id','url']);
    }

    public function Product(){
        return $this->hasMany('Product','category_id','id');
    }

    /**
     * @return false|mixed|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCategories(){
        $redisHandler = Cache::store('redis')->handler();
        $redis_key = 'cats:'.request()->module();
        $cats = json_decode($redisHandler->get($redis_key),true);
        //判断前台后台返回不同的数据
        if(!$cats){
            $cats = (request()->module() == 'index' ? self::with('topicImg')->field(['id','name','topic_img_id'])->select()->toArray():
                self::with('topicImg')->select());
            $redisHandler->set($redis_key,json_encode($cats),config('expire_time'));
        }
        return $cats;
    }

    public static function getCatInfoByID($id){
        return self::with('topicImg')->find($id);
        //return null;
    }

    public static function getALLCategoryInfo($catId){
        return self::with(['topicImg','Product.Img','Product.productImg.Img'])->where(['id'=>$catId])->find();
    }

    public static function getSelectCategoryInfo(){
        return self::field(['id','name'])->select();
    }
}