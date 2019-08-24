<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6
 * Time: 12:45
 */

namespace app\common\model;


use app\common\model\Base;

class Image extends Base
{
    protected $table = 'image';
    protected $hidden = ['delete_time','update_time'];

    public function imgCategory(){
        return $this->belongsTo('ImgCat','img_cat','id');
    }

    public function getUrlAttr($value,$data){

         return $this->imgRebuild($value,$data);
    }

    public function getThumbUrlAttr($value,$data){

        return $this->imgRebuild($value,$data);
    }
    //用于图片管理提取数据
    public static function getImagePagnation($where){
        if(empty($where)){
            $where = '1 = 1';
        }
        $param = input('param.');
        $limit = intval(isset($param['pageSize']) ? $param['pageSize'] : 20);
        $offset = intval(($param['pageNumber'] - 1) * $limit);
        return self::with(['imgCategory'])
            ->where($where)
            ->limit($offset, $limit)
            ->order('id desc')
            ->select();
    }
}