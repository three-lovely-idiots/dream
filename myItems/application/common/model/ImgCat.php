<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 4/3/19
 * Time: 2:11 AM
 */

namespace app\common\model;


class ImgCat extends Base
{
    public static function getImgcatByID($id = '')
    {
        return self::find(['id'=>$id]);
    }


    //更新和保存分类数据
    public static function saveCats()
    {

        $res = '';
        $group = request()->param()['data'];
        //这种情况又出现了
        html_entity_decode($group);
        $group = json_decode(html_entity_decode($group), true);

        if($group['id']){
            $group['update_time'] = time();
            $res = self::where(['id'=>$group['id']])->update($group);
        }else{
            $group['update_time'] = time();
            $group['pid'] = 0;
            $res = self::create($group)->result;
        }

        return $res;

    }



}