<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/8/19
 * Time: 6:12 AM
 */

namespace app\common\model;


class NailStyles extends Base
{
    public function SelectedImage(){
        return $this->belongsToMany('SelectedImage','selected_img_style','sid','nsid');
    }
    public function img(){
        return $this->belongsTo('Image','img_id','id')->field(['id','url','from']);
    }
    public static function getNailStylesPagnation(){
        $param = input('param.');
        $limit = intval($param['pageSize']);
        $offset = intval(($param['pageNumber'] - 1) * $limit);
        return self::with(['img'])->limit($offset, $limit)->order('id asc')->select();
    }
    public static function getOne($id){
        return self::with(['img'])->where(['id'=>$id])->find();
    }
}