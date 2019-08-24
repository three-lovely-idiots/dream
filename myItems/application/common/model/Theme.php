<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/7
 * Time: 11:57
 */

namespace app\common\model;
use app\common\model\Base;
use think\Cache;

class Theme extends Base
{
    public function Img(){
        return $this->belongsTo('Image','img_id','id');
    }
    //主题的主图
   public function topicImg(){
        return $this->belongsTo('Image','topic_img_id','id');
   }
   //主题主页面主图
    public function headImg(){
        return $this->belongsTo('Image','head_img_id','id');
    }
    //商品主题合集
    public function products(){
        return $this->belongsToMany('product','theme_product','product_id','theme_id');
    }
   //美图主题合集
    public function selectImage(){
        return $this->belongsToMany('SelectImage','theme_select_image','	select_img_id','theme_id');
    }

    public static function getAllTheme(){
        return self::with('products,headImg,topicImg')->select();
    }

    public static function getAllThemeList($where){
        return self::with(['headImg','topicImg'])->where($where)->select();
    }

    public static function getEditThemeInfoById($id){
        return self::with(['headImg','topicImg'])->find($id);
    }

    public static function getAllThemesForSelect($typeid = ''){
       $where = [];
       if($typeid){
           $where = ['type_id'=>$typeid];
       }
        return self::with(['headImg','topicImg'])->where($where)->field(['id','name','description','head_img_id','topic_img_id'])->select();
    }

    //分页查询
    public static function getThemesByWhere($where,$limit,$offset){
        return self::with(['headImg','topicImg'])->where($where)->limit($offset, $limit)->order('id desc')->select();
    }

    //前端**************************
    //主页主题列表
    public static function getThemeList(){
        return self::with('topicImg,headImg')->order('update_time','desc')->select();
    }
    //主页点击进入主题主页
    public static function getSingleThemeInfoById($id,$type){
        if($type==1){
            return self::with('products,headImg,topicImg')->find($id);
        }else{
            return self::with(['selectImage.Img','headImg','topicImg'])->find($id);
        }
    }
    public static function getSingleThemeInfoByIdNew($id,$type){
        //theme:[type]:[id]
        $redis_key = 'theme:'.$type.':'.$id;
        $redis_handler = Cache::store('redis')->handler();
        $theme = $redis_handler->get($redis_key);

        if(!$theme){ //没有缓存的时候开始取数据库
            $theme = self::with('headImg')->find($id);
            $theme_items = [];
            if($type==1 && isset($theme['related_ids'])){
                $theme_items = SelectedImage::with(['Img'])->where(['id'=>['in',ltrim(rtrim($theme['related_ids'],"]"),"[")]])->select();

            }else{
                $theme_items = Product::all(['id'=>['in',ltrim(rtrim($theme['related_ids'],"]"),"[")]]);
            }
            $theme['theme_items'] = $theme_items;
            $redis_handler->set($redis_key,json_encode($theme),config('redis_expire_time'));
        }else{
            $theme = json_decode($theme,true);
        }
        return $theme;
    }

}