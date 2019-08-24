<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19 0019
 * Time: 下午 10:15
 */

namespace app\common\model;


class Appoint extends Base
{
    public function img(){
        return $this->belongsTo('Image','image_id','id');
    }
    //protected $visible = ['id','title','image_id'];
    public static function getAppointByPage(){
        $param = input('param.');
        $limit = intval($param['pageSize']);
        $offset = intval(($param['pageNumber'] - 1) * $limit);
        return self::with(['img'])->limit($offset, $limit)->order('id desc')->select();
    }

    public static function getOne($id){
        return self::with(['img'])->find(['id'=>$id]);
    }
}