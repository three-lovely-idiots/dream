<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/11
 * Time: 10:00
 */

namespace app\common\exception;


class UserException  extends BaseException
{
    public $code = 404;
    public $msg = "无此用户信息";
    public $errorCode = 70000;
}