<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/17
 * Time: 17:06
 */

namespace app\common\enum;


class OrderStatus
{
    const  UNPAID = 1;  //未付款
    const  PAID = 2;  //已经付款
    const  USED = 3;   //以消费
    const  CLOSED = 0;  //关闭
    const  PAID_OUT_OF_STOCK = 4;
}