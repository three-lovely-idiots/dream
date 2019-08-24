<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/7
 * Time: 16:44
 */

namespace app\common\model;


use think\Cache;

class Product extends Base
{

    public function Img(){
        return $this->belongsTo('Image','img_id','id');
    }

    public function Cat(){
        return $this->belongsTo('Category','category_id','id')->field('id,name');
    }
//    public function productImg(){
//        return $this->hasMany('ProductImage','product_id','id');
//    }
//   public function productProperty(){
//       return $this->hasMany('ProductProperty','product_id','id');
//   }
    public static function getRecent($count){
        return self::with(['Img'])->order('create_time desc')->
                        limit($count)->
                        field('id,name,price,stock,category_id,main_img_url,img_id,discount_price,sale')->
                        select();
    }


    public static function getAllProductsByCategories($categoryid){
           return self::with(['Img'])->where('category_id','=',$categoryid)->select();
    }

    public static function getAllProductsByCid($categoryid){
        return self::with('Img')->field(['id','name','category_id','name','img_id'])->where('category_id','=',$categoryid)->select();
    }
    public static function getAllProducts(){
        return self::with('Img')->select();
    }
    public static function getAllProductsForSelect(){
        return self::with('Img')->field(['id','name','category_id','name','img_id'])->select();
    }
    //
    public function getMainImgUrlAttr($value,$data){
        return $this->imgRebuild($value,$data);
    }

    public function getCategoryIdAttr($value,$data){ //对数据里面的category_id进行过滤
        //取出redis句柄
        $redis_handler = Cache::store('redis')->handler();
        //对分类进行缓存处理
        $exist = json_decode($redis_handler->exists('category')); //判断是否存在
        if(!$exist){ //缓存没有 那就从数据库取出
            $category = Category::all()->toArray();
            if($category){ //如果有值返回 便利插入redis hash表  category  category_id  json_info
                foreach($category as $k => $v){
                    $redis_handler->hset('category',$k,json_encode($v));
                }
                //设置过期时间
                $redis_handler->expire('category',config('expire_time'));
            }
            unset($category);
        }

        return [$value,json_decode($redis_handler->hget('category',$value),true)['name']];
    }

    /**
     * @param $id  产品的id  redis缓存键 product:[id]
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductDetail($id){
        $redis_handler = Cache::store('redis')->handler();
        $product_key = 'product:'.$id;
        $exist = json_decode($redis_handler->exists($product_key)); //判断是否存在
        if(!$exist){ //缓存没有 那就从数据库取出
             $data = self::with(['Img'])->find($id);
             //有过期时间限制
             $redis_handler->setex($product_key,config('expire_time'),json_encode($data));
             unset($data);
        }

        return json_decode($redis_handler->get($product_key),true);
    }

    public static function getProductByID($id){
        return self::with(['Img'])->field(['id','name','img_id'])->find($id);
    }

    //用于图片管理提取数据
    public static function getProductPagnation(){
        $param = input('param.');
        $limit = intval($param['pageSize']);
        $num = intval($param['pageNumber']);
//        $offset = intval(($param['pageNumber'] - 1) * $limit);
        $where = [];
        return self::where($where)
            ->order('id desc')
            ->field('id,main_img_url,name,from,category_id')
            ->paginate($limit,false,[
                'page'=>$num
            ]);
    }

    public static function getProductsInIds($ids){
        return self::where('id','in',$ids)->field('id,name,price,discount_price,sale,category_id,stock,main_img_url')->select()->toArray();
    }
}