<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/7/19
 * Time: 11:26 PM
 */

namespace app\common\model;


class NailMainStyles extends Base
{
    //与nailstyle的一对多关系
    public function styles(){
        return $this->hasMany("NailStyles","main_id","id")->field(['title','img_id','main_id']);
    }
    //用于图片管理提取数据
    public static function getMainStylesPagnation(){
        $param = input('param.');
        $limit = intval($param['pageSize']);
        $offset = intval(($param['pageNumber'] - 1) * $limit);
        return self::limit($offset, $limit)->order('id desc')->select();
    }

    public static function getOne($id){
        return self::where(['id'=>$id])->find();
    }

    public function getAll(){
        return $this->select();
    }

    public static function getAllStyles(){
        return self::with(["styles","styles.Img"])->select();
    }

}