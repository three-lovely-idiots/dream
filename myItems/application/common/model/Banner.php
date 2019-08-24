<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14 0014
 * Time: 下午 2:57
 */

namespace app\common\model;

class Banner extends Base
{
    protected $visible = ['id','name','description','items'];
    //后台
    public static function getAllBannerByWhere($where,$limit,$offset)
    {
        return self::where($where)->limit($offset, $limit)->order('id desc')->select();
    }

    public static function getAllBanner($where=[])
    {
        return self::where($where)->select();
    }
    //前端
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }
   //前端取出
    public static function getBannerById($id){
        $result = self::with(['items','items.indexImg'])->select($id);
        return $result;
    }

}