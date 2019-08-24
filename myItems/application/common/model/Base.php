<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14 0014
 * Time: 下午 2:57
 */

namespace app\common\model;



use think\Cache;
use think\Config;
use think\Model;

class Base extends Model
{
    public static $redisHandler;

    public function imgRebuild($value,$data){
        $img_perfix = Config('img_perfix');
        //对应存在完整路径的特殊情况
        if(strpos($value,$img_perfix) != false){
            return $value;
        }
        if(isset($data['from'])&&$data['from']==1){
            $value = $img_perfix.$value;
        }
        return $value;
    }

    public static function buildparams(){

    }

    public function img(){
        return $this->belongsTo('Image','img_id','id');
    }
    public static function getPagnation($fields = '*',$where = ['1'=>1]){

        $param = input('param.');
        $limit = intval($param['pageSize']);
        $offset = intval(($param['pageNumber'] - 1) * $limit);

        return self::with(['img'])->
                where($where)->
                limit($offset, $limit)->
                order('id asc')->
                field($fields)->
                select();
    }
//    //缓存读取  这里运用php可变参数函数的方法
//    public static function cacheRedis(...$args)
//    {
//        $handler = Cache::store('redis')->handler();
//        $command = $args[0];
//        switch($command){
//            case 'get':
//
//            break;
//            case 'set':
//
//            break;
//        }
//    }

}