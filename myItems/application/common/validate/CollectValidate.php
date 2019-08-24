<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 1:26
 */

namespace app\common\validate;


class CollectValidate extends BaseValidate
{
    protected $rule = [
        'tid'=>'require',
        'type'=>'require'
    ];

    protected $message = [
        'tid'=>'tid必须存在',
        'type'=>'type必须存在'
    ];
}