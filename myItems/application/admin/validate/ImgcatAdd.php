<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 4/3/19
 * Time: 3:15 AM
 */

namespace app\admin\validate;
use app\common\validate\BaseValidate;
class ImgcatAdd extends BaseValidate
{
    protected $rule = [
        'name'=>'require',
        'pid'=>'require|filterSelf',
    ];

    protected $message = [
        'name'=>'图片分类名称必须存在',
        'pid.require'=>'父分类pid必须存在',
        'pid.filterSelf'=>'不能把自己当做父类',
    ];

     function filterSelf($value,$rule='',$data='',$field=''){
        if($value == $data['id'])
        {
            return false;
        }
        return true;
    }
}