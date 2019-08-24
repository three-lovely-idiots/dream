<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12
 * Time: 17:51
 */

namespace app\common\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $msg = "订单状态异常";
    public $errorCode = 80000;
}