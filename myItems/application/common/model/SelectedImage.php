<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/9/19
 * Time: 9:40 PM
 */

namespace app\common\model;


use think\Cache;

class SelectedImage extends Base
{
    public function img(){
        return $this->belongsTo('Image','img_id','id');
    }
    public function NailStyles(){
        return $this->belongsToMany('NailStyles','selected_img_style','nsid','sid');
    }
    public function User(){
        return $this->belongsToMany('User','select_image_user','uid','sid');
    }

    public static function getOne($id)
    {
        return self::with(['img','User'])->where(['id'=>$id])->find()->toArray();
    }

    public static function getSelectedImgPagnation(){
        $param = input('param.');
        $limit = intval($param['length']);
        $offset = intval(($param['pageNO'] - 1) * $limit);
        return self::with(['img'])->limit($offset, $limit)->order('create_date desc')->select()->toArray();
    }

    public static function getFilterImages($res){
        $ids = array_keys($res['matches']);
        return self::with(['img'])->whereIn('id',$ids)->select()->toArray(); //后期要加上order排序

    }

    public static function getRand($count)
    {

       // $list = Cache::get('allImgs');
//        if(empty($list)){
            $list = self::with(['img'])->select()->toArray();
//        }
//        self::execute('SELECT * FROM `reportcard_patient_temp` WHERE id &gt;= (SELECT FLOOR( MAX(id) * RAND()) FROM `table` ) ORDER BY id LIMIT 5;');
        $random_keys = array_rand($list,$count);

        foreach($random_keys as $k => $v){
            $data[] = $list[$v];
        }
        return $data;
    }

    //用于图片管理提取数据
    public static function getImagePagnation(){
        $param = input('param.');
        $limit = intval($param['pageSize']);
        $offset = intval(($param['pageNumber'] - 1) * $limit);
        return self::with(['img'])->limit($offset, $limit)
            ->order('id desc')
            ->select();
    }

}