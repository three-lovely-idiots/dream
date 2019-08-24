<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6 0006
 * Time: 下午 4:13
 */

namespace app\index\controller;

use app\common\enum\StatusEnum;
use app\common\exception\MissException;
use app\common\model\Theme as ThemeModel;
use app\common\service\Util;

class Theme extends Base
{
    //取出首页需要的主题页面
    public function getThemeLists(){

        $res = ThemeModel::getThemeList();
        if(!$res){
            throw new MissException([
                'msg' => '所请求主题不存在'
            ]);
        }
        return $res;
    }

    //取出单个主题单页需要的数据
     public function getSingleThemeInfo($id,$type){
        $theme = ThemeModel::getSingleThemeInfoByIdNew($id,$type);

        if(!$theme){
            throw new MissException([
                'msg' => '主题内容不存在'
            ]);
        }
        return Util::showMsg(StatusEnum::SUCCESS,['data'=> $theme]);
    }
}