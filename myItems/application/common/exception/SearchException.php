<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 3/28/19
 * Time: 9:04 AM
 */

namespace app\common\exception;


class SearchException extends BaseException
{
    public $code = 404;
    public $msg = "无搜索数据返回";
    public $errorCode = 10002;
}