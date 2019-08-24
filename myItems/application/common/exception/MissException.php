<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 3/28/19
 * Time: 7:33 AM
 */

namespace app\common\exception;


use app\common\exception\BaseException;

class MissException extends BaseException
{
    public $code = 404;
    public $msg = "无数据返回";
    public $errorCode = 10001;
}