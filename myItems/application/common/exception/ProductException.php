<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/7
 * Time: 23:31
 */

namespace app\common\exception;


class ProductException extends BaseException
{
    public $code = 404;
    public $msg = "请求的商品不存在";
    public $errorCode = 50000;
}