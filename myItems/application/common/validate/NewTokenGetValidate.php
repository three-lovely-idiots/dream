<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 17:09
 */

namespace app\common\validate;


class NewTokenGetValidate extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty',
        'user_info' => 'require|isNotEmpty',
        'encrypted_data' => 'require|isNotEmpty',
        'iv' => 'require|isNotEmpty',
        'signature' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '没有code还想拿token与用户信息？做梦哦'
    ];
}