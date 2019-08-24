<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/2
 * Time: 15:18
 */

namespace app\common\exception;


class ParameterException extends BaseException
{
    public $code = 404;
    public $msg = "参数错误";
    public $errorCode = 20000;
}
