<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/7/19
 * Time: 3:37 AM
 */

namespace app\common\validate;


class AppointRecordValidate extends BaseValidate
{
    protected $rule = [
        'appoint_time'=>'require',
        'name'=>'require',
        'mobile'=>'require',
        'remark'=>'require',
        'num'=>'require',
        'appoint_key'=>'require',
    ];

    protected $message = [
        'appoint_time'=>'预约时间是必选项',
        'name'=>'姓名是必选项',
        'mobile'=>'电话是必选项',
        'remark'=>'备注信息必须填上',
        'num'=>'预约人数必须',
        'appoint_key'=>'key值出现错误',
    ];
}