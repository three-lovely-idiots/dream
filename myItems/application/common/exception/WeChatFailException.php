<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 2017/7/9
 * Time: 12:16
 */

namespace app\common\exception;


class WeChatFailException extends BaseException
{
    public $code = 400;
    public $msg = "微信服务接口调用失败";
    public $errorCode = 999;
}