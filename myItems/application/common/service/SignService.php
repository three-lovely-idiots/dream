<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/8 0008
 * Time: 下午 5:48
 *
 * 构思 用redis的bitmap去进行签到处理
 * key： sign:[uid]:
 */

namespace app\common\service;

use app\common\enum\FrontConfig;
use app\common\enum\StatusEnum;
use app\common\model\Sign;
use think\Cache;


class SignService
{
    public static $key_suffix = "sign-";
    public static function doSign($uid){
        $redis = Cache::store('redis')->handler();
        $params = []; //入mysql数据表sign的数据
        $date = date('Ymd');
        $key = self::$key_suffix.$date;
        //先存入当天的bitmap
        $redis->setbit($key,$uid,1);
        $redis->expire($key,20*24*60*60); //过期时间20天左右
        //更新打卡天数 返回最新的打卡天数
        $days_count = self::updateSignDays($uid,$redis);
        //在把今天的签到信息入库
        //1: 拼装信息  date user_id money money根据打卡天数定 create_time
        $params['user_id'] = $uid;
        //本来用该后台设置的我先用枚举类表示吧 连续天数 * 基础奖金
        $params['money'] = FrontConfig::SIGN_MONEY * $days_count;
        $params['create_time'] = strtotime('now');
        $params['date'] = $date;

        if(!Sign::create($params)->getLastInsID()){
            return ['status'=>-1,'key'=>$key];
        }
        return ['status'=>StatusEnum::FAIL,'money'=>$params['money'],'days_count'=>$days_count];
    }

    /**
     * @param $uid  用户的id
     * @param $key  要删除的key值
     */
    public static function eraseSign($uid,$key)
    {
        $redis = Cache::store('redis')->handler();
        $sign_days_key = self::$key_suffix.$uid;
        $redis->setbit($key,$uid,0);
        if(($sign_day = $redis->get($sign_days_key))){ //如果存在 或者不为0 那么就进行递减操作
            $sign_day--;
            $redis->set($sign_days_key,$sign_day);
        }
    }

    public static function isCheckSign($uid){
        //检查今天是否已经签到
        $redis = Cache::store('redis')->handler();
        $date = date('Ymd');
        $key = self::$key_suffix.$date;
        return $redis->getbit($key,$uid);
    }

    public static function updateSignDays($uid,$redis="")
    {
        if(!$redis){
            $redis = Cache::store('redis')->handler();
        }
        $sign_days_key = self::$key_suffix.$uid;
        $days_count = self::getContinulySignDays($uid);

        //再继续检查昨天的签到情况 第一种没有签到那就归一  第二种程序的第一天这种极端情况 那么直接赋值为1
        $yesterday_key = self::$key_suffix.date("Ymd",strtotime("-1 day"));
        if(!$redis->getbit($yesterday_key,$uid)){ //不存在或者没有签到返回的都是0
            $days_count = 1;
        }else{
            //以上条件都不符合 那就是一直在连续签到啊
            $days_count++;
        }
        //开始向redis设置值
        $redis->set($sign_days_key,$days_count);
        return $days_count;
    }

    public static function getContinulySignDays($uid,$redis=""){
        if(!$redis){
            $redis = Cache::store('redis')->handler();
        }
        $sign_days_key = self::$key_suffix.$uid;
        $days_count = $redis->get($sign_days_key);
        //不存在 存在但是为0 7天 或者不是七天但是没有连续签到 用大于等于最严谨
        if(!$days_count || $days_count >= 7){
            $redis->set($sign_days_key,0);
            $days_count = 0;
        }
        return $days_count;
    }

}