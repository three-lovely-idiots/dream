<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9 0009
 * Time: 下午 6:32
 */

namespace app\common\model;


class OrderProduct extends Base
{
    public function img(){
        return $this->belongsTo('Image','img_id','id');
    }


}