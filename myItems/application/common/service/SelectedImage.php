<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/10/19
 * Time: 5:23 AM
 */

namespace app\common\service;


use app\common\model\NailStyles;
use app\common\model\SelectedImgStyle;

class SelectedImage
{
    //随机获取相关图片
    public static function getRandLikeImg($num){

        $selected_img = new \app\common\model\SelectedImage();
        $countcus = $selected_img->count();    //获取总记录数
        $min = $selected_img->min('id');    //统计某个字段最小数据

        if($countcus < $num){$num = $countcus;}
        $i = 1;
        $flag = 0;
        $ary = array();
        while($i<=$num){
            $rundnum = rand((int)$min, $countcus);//抽取随机数
            if($flag != $rundnum){
                //过滤重复
                if(!in_array($rundnum,$ary)){
                    $ary[] = $rundnum;
                    $flag = $rundnum;
                }else{
                    $i--;
                }
                $i++;
            }
        }
        $list = $selected_img->with(['img'])->where('id','in',$ary,'or')->select()->toArray();
        var_dump($list);
        return $list;
    }

    public static function dealWithOtherTags($other_tags)
    {
        if($other_tags){
            $other_tags_arr = explode(",",$other_tags);
            $data = NailStyles::all()->toArray();//全部取出然后再进行比较

            foreach($other_tags_arr as $key => $value){
                foreach($data as $k => $v){
                    if($v['title'] == $value && $v['type'] != 0){
                        //更新style的热度num 非固化标签 后台使用次数
                        NailStyles::update(['num'=>$v['num']+1],['id'=>$v['id']]);
                        //去掉更新后的
                        unset($other_tags_arr[$key]);
                    }
                }
            }
            if(($len = count($other_tags_arr)) > 0){
                for($i=0;$i<$len;$i++){
                    //没有存在的自定义标签就直接插入
                    $insert_data = [
                        'title' => $other_tags_arr[$i],
                        'type' => 1,
                    ];
                    NailStyles::create($insert_data);
                }
            }
            return true;
        }
        return false;
    }
    public static function updateSelectedImgStyle($obj,$tag_title){
        $tag_arr = explode(",",$tag_title);
        $data = [];
        foreach($tag_arr as $key=>$value){
            $nail_style = NailStyles::get(['title'=>$value]);
            if(isset($nail_style['id'])){
                $data[] = $nail_style['id'];
            }
        }
        if(count($data) > 0){
            if(!$obj->NailStyles()->saveAll($data)){
                return false;
            }
        }
        return true;
    }

    public static function deleteOriginSelected($id)
    {
        if(count(SelectedImgStyle::all(['sid'=>$id])) == 0) //没有数据不用删除操作 直接返回true
        {
            return true;
        }
        $selected_img_style = new SelectedImgStyle();
        if(!$selected_img_style->where(['sid'=>$id])->delete()){
            return false;
        }
        return true;
    }
}