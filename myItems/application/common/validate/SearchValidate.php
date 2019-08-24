<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 3/28/19
 * Time: 9:12 AM
 */

namespace app\common\validate;


class SearchValidate extends BaseValidate
{
    protected $rule = [
        'length' => 'require|isNotEmpty',
        'pageNO' => 'require|isNotEmpty'
    ];

    protected $message = [
        'length' => '没有total怎么能行',
        'pageNO' => '没有total怎么能行',
    ];
}