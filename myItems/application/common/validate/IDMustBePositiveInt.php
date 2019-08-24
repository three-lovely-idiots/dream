<?php
namespace app\common\validate;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/30
 * Time: 15:05
 */
class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
         'id'=>'require|IsPositiveInteger'
     ];

    protected $message = [
        'id' => 'id必须为正整数'
    ];


}