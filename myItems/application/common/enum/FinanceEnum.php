<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9 0009
 * Time: 下午 4:43
 */

namespace app\common\enum;


class FinanceEnum
{
    //两种操作类型 积分与余额
    const POINTS_TYPE = 'points';
    const MONEY_TYPE = 'money';


    //分享的积分
    const SHARE_POINTS = 1;
    //签到得积分
    const SIGN_POINTS = 2;
    //买商品得积分
    const PURCHASE_POINTS = 3;
    //兑换积分商品减少积分
    const PURCHARE_DEC = 4;
    //会员充值 这个涉及会员充钱 这个是money操作
    const MEMBER_ADD_MONEY = 5;

}