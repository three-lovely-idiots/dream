<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9 0009
 * Time: 下午 11:06
 */

namespace app\api\controller;


class CreateQr extends Easychat
{
    public function createQrcode()
    {
        var_dump($this->app->qrcode->temporary('foo', 6 * 24 * 3600));

    }
}