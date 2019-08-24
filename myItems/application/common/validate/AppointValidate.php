<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/6/19
 * Time: 1:10 AM
 */

namespace app\common\validate;


class AppointValidate extends BaseValidate
{
    protected $rule = [
        'title'=>'require',
        'appoint_time_list'=>'require',
        'begin_time'=>'require',
        'end_time'=>'require',
        'appoint_days'=>'require',
        'pre_total'=>'require',
        'day_total'=>'require',
    ];

    protected $message = [
        'title'=>'预约标题必须存在',
        'appoint_time_list'=>'预约时间表必须存在',
        'begin_time'=>'开始时间必须标注',
        'end_time'=>'结束时间必须标注',
        'appoint_days'=>'预约天数间隔必须编著',
        'pre_total'=>'每人可预约次数必须标注',
        'day_total'=>'每人每天可预约次数需要标注',
    ];

//id:$("input[name=id]").val(),
//title:$("input[name=title]").val(),
//image_id:$("#image_id").val(),
//description:$("#desc").val(),
//appoint_time_list:$("input[name=srvtime]").val(),
//exclude_date:exclude_date,
//notify_cs_type:$("input[name=notify_cs_type][checked]").val(),
//begin_time:$("#begin_time").val(),
//end_time:$("#end_time").val(),
//appoint_days:$("#appoint_days").val(),
//notify_email:$("#notify_email").val(),
//cs_templateid:$("#cs_templateid").val(),
//fans_templateid:$("#fans_templateid").val(),
//pre_total:$("#pre_total").val(),
//day_total:$("#day_total").val(),
//edit:$(".edit").val(),
//code:$(".code").val(),
//follow:$(".follow").val(),
//isshow:$(".isshow").val()
}