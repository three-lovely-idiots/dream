<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/13/19
 * Time: 3:47 AM
 */

namespace app\common\model;


class SelectedImgStyle extends Base
{
    public function NailStyles()
    {
        return $this->belongsTo('NailStyles','nsid','id');
    }

    public static function getAllStyles()
    {
        return self::with(['NailStyles'])->select()->toArray();
    }

    public static function getStylesByID($id){
        return self::with(['NailStyles'])->where(['sid'=>$id])->select()->toArray();
    }

}