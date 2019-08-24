<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 3:35
 */

namespace app\common\service;


use app\common\enum\StatusEnum;
use app\common\model\Product;
use app\index\controller\User;
use think\Cache;
use app\common\model\UserCollect;
use app\common\model\SelectedImage;
use app\common\model\User as UserModel;
class CollectService
{
    //取出收藏放入redis 根据product与image 进行区分
    public static function setCollectToRedis($uid,$type)
    {
        $redis_handler = Cache::store('redis')->handler();
        $key = 'collect:'.$uid.':'.$type;
        //商品图片收藏逻辑
        if(!$redis_handler->exists($key)){//不存在就从数据库里面取出然后放到集合里面
            $arr = UserCollect::all(function($query) use($uid,$type){
                $query->where(['type'=> ($type == 'product' ? StatusEnum::PRODUCT_TYPE:StatusEnum::IMAGE_TYPE),'uid'=>$uid]); //取出所有用户id下面的商品
            })->toArray();
            foreach($arr as $k=>$v){
                //用集合的方式去存储数据
                $redis_handler->sadd($key,$v['tid']);
            }
            //设置过期时间还是非常必要的
            $redis_handler->expire($key,config('redis_expire_time'));
        }
        return;
    }

    //这个是产品/图片对应的 收藏用户 是第一个方法的逆向操作 我们可以针对特定方法做前置操作
    public static function setCollectToRedisTwo($tid,$type)
    {
        $redis_handler = Cache::store('redis')->handler();
        $key = 'collect_user:'.$tid.':'.$type;  //每个产品或者图片都有一个redis数据
        //商品图片收藏逻辑
        if(!$redis_handler->exists($key)){//不存在就从数据库里面取出然后放到集合里面
            $arr = UserCollect::getItemCollectedUser($tid,$type);
            foreach($arr as $k=>$v){
                $user_info = [
                    'name' => $v['user']['nickName'],
                    'openid'=>$v['user']['openid'],
                    'avatarUrl'=>$v['user']['avatarUrl']
                ];
                //我们用hash表的方式去存用户信息 key=》uid  value=》userinfo
                $redis_handler->hset($key,$v['user']['id'],json_encode($user_info));
            }
            $redis_handler->expire($key,config('redis_expire_time'));
        }
        return;
    }

    //这个方法用来检测商品和图片是否被当前用户收藏
    public static function checkCollectStatus($type,$uid,$id){
        self::setCollectToRedis($uid,$type);
        self::setCollectToRedisTwo($id,$type); //先进行redis检测 这一步不可或缺 我们不知道客户们会执行那个动作在先
        $redis_handler = Cache::store('redis')->handler();
        $key = 'collect:'.$uid.':'.$type;
        return $redis_handler->sismember($key,$id); //存在返回true 不存在返回false或者0
    }

    public static function setItemCollectIncr($type,$id)
    {
        if($type == 'product'){
            Product::where(['id'=>$id])->setInc('collect',1);
        }else{
            SelectedImage::where(['id'=>$id])->setInc('collect',1);
        }
    }

    public static function addUserIntoItem($key,$uid){
        $redis_handler = Cache::store('redis')->handler();
        $user_info = UserModel::where(['id'=>$uid])->field('nickName,openid,avatarUrl')->find();
        $redis_handler->hset($key,$uid,json_encode($user_info));
    }

    /**
     * @param $type
     * @param $id
     * @param $tid
     * @param $uid
     */
    public static function cancelRedisCollect($type,$id,$tid,$uid){
         //1 取消redis的 集合  user-》item   collect:[$uid]:[$type]
        $redis_handler = Cache::store('redis')->handler();
        $key1 = 'collect:'.$uid.':'.$type;
        $key2 = 'collect_user:'.$tid.':'.$type;
        if($redis_handler->exists($key1)){
            $redis_handler->srem($key1,$tid);
        }
         //2 取消redis的 哈希表 item-》user  collect_user:[$tid]:[$type]
        if($redis_handler->exists($key2)){
            $redis_handler->hdel($key2,$uid);
        }

   }
}