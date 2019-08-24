<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/3/19
 * Time: 2:41 AM
 */

namespace app\common\validate;


class AttachmentEditValidate extends BaseValidate
{
    protected $rule = [
        'id'=>'require',
        'url'=>'require',
        'imagewidth'=>'require',
        'imageheight'=>'require',
        'imagetype'=>'require'
    ];

    protected $message = [
        'id'=>'id必须存在',
        'url'=>'url必须存在',
        'imagewidth'=>'图片宽度必须存在',
        'imageheight'=>'高度必须存在',
        'imagetype'=>'图片类型必须存在'
    ];
}