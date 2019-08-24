<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 2017/7/9
 * Time: 14:23
 */

namespace app\common\exception;


class TokenException extends BaseException
{
    public $code = 401;
    public $msg = "Token无效或过期";
    public $errorCode = 10001;
}