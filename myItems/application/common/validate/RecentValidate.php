<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/7
 * Time: 23:03
 */

namespace app\common\validate;


class RecentValidate extends BaseValidate
{
    protected $rule = [
        'count'=>'IsPositiveInteger|between:1,15'
    ];
}