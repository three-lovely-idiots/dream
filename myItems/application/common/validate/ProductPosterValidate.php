<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 1:26
 */

namespace app\common\validate;


class ProductPosterValidate extends BaseValidate
{
    protected $rule = [
        'id'=>'require',//产品的id
    ];

    protected $message = [
        'id'=>'id必须存在',
    ];
}