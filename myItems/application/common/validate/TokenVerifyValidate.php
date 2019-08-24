<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/4
 * Time: 16:29
 */

namespace app\common\validate;


class TokenVerifyValidate extends BaseValidate
{
    protected $rule = [
        'token' => 'require|isNotEmpty'
    ];

    protected $message = [
        'token' => '没有token怎么检验'
    ];
}