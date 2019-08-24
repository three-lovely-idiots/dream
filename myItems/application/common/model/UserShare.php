<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9 0009
 * Time: 下午 3:59
 */

namespace app\common\model;


use app\common\enum\FinanceEnum;
use app\common\enum\FrontConfig;

class UserShare extends Base
{
    //1用户加金币  2 财务记录表添加记录 3 分享映射表
    public static function addCoindOper($params)
    {
//        var_dump($params);
        //用户模型 mid分享者才能得到金币
        $user_model = new User();
        $user_model->startTrans();
        $res = $user_model->where(['id'=>$params['mid']])->setInc('money',FrontConfig::SHARE_MONEY);
//        var_dump("user:".$res);
        if(!$res){ //删除失败
            $user_model->rollback(); //回滚
            return false;
        }

        //  `id` int(11) NOT NULL AUTO_INCREMENT,
        //  `user_id` int(11) NOT NULL,
        //  `type` varchar(20) NOT NULL COMMENT 'type => money:余额记录,points:积分记录 这个有利于以后的扩展',
        //  `money` float(10,2) NOT NULL,
        //  `action` tinyint(4) NOT NULL COMMENT '1 分享获得积分 2 签到获得积分 3 买商品获得积分 4 买积分商品减少积分 5 会员充值' ,
        //  `create_time` int(11) NOT NULL,
        //  PRIMARY KEY (`id`)

        //财务记录模型
        $finance_log = new FinanceLog();
        $finance_log->startTrans();
        $res = $finance_log->insertGetId([ //这个方法插入成功的时候直接返回主键值
            'user_id' => $params['mid'],
            'type' => FinanceEnum::POINTS_TYPE,
            'money' => FrontConfig::SHARE_MONEY,
            'action' => FinanceEnum::SHARE_POINTS,
            'create_time' => time()
        ]);

//        var_dump("finance:".$res);
        if(!$res){ //添加财务记录失败
            $finance_log->rollback(); //回滚
            return false;
        }

    //  `id` int(11) NOT NULL AUTO_INCREMENT,
    //  `mid` int(11) NOT NULL COMMENT '分享者id',
    //  `sid` int(11) NOT NULL COMMENT '被分享者id',
    //  `fid` int(11) NOT NULL COMMENT '财务记录id',
    //  `create_time` int(11) NOT NULL,
    //  PRIMARY KEY (`id`)

        //分享映射表
        $user_share_model = new UserShare();
        $user_share_model->startTrans();

        $res = $user_share_model->insert([
            'mid' => $params['mid'],
            'sid' => $params['uid'],
            'fid' => intval($res), //这里的res是财务记录里面的主键值
            'create_time' => time()
        ]);
//        var_dump("usershare:".$res);
        if(!$res){ //添加财务记录失败
            $user_share_model->rollback(); //回滚
            return false;
        }

        //上面都成功的情况下我们commit 提交
        $user_model->commit();
        $finance_log->commit();
        $user_share_model->commit();
        return true;
    }
}