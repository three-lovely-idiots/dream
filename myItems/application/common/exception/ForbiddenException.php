<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12
 * Time: 10:56
 */

namespace app\common\exception;


use think\Exception;

class ForbiddenException extends BaseException
{
    public $code = 402;
    public $msg = "权限不够";
    public $errorCode = 80000;
}