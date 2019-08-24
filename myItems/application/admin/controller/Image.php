<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/15 0015
 * Time: 下午 2:16
 */

namespace app\admin\controller;

use app\common\model\Image as ImageModel;
use think\Request;

class Image extends Base
{
    public function mySelect()
    {
        if(Request::instance()->isAjax()){
            $all = ImageModel::all();
            return $all;
        }
        $this->assign('ver',time());
        return view('imageselect');
    }
}