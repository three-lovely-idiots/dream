<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19 0019
 * Time: 下午 7:17
 */

namespace app\common\validate;


class AppointSettingValidate extends BaseValidate
{
    protected $rule = [
        'before_time'=>'require|limitForTime',
        'limit_time'=>'require|limitForTime'
    ];

    protected function limitForTime($value,$rule='',$data='',$field=''){
        switch($field){
            case 'before_time':
                if(is_numeric($value)&&is_int($value+0)&&intval($value)>=60){
                    return true;
                }else{
                    return false;
                }
                break;
            case 'limit_time':
                if(is_numeric($value)&&is_int($value+0)&&($value+0)>=10&&($value+0)<=20){
                    return true;
                }else{
                    return false;
                }
                break;
            default:
                break;
        }
    }

    protected $message = [
        'before_time' => '预约提前时间必须大于60分钟',
        'limit_time' => '等待时间必须介于10-20分钟之间'
    ];
}