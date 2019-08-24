<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19 0019
 * Time: 下午 4:00
 */

namespace app\admin\controller;


use app\common\enum\AdminoperEnum;
use app\common\enum\StatusEnum;
use app\common\model\AppointSettings as AppointSettingsModel;
use app\common\service\Util;
use app\common\validate\AppointSettingValidate;
use app\common\validate\AppointValidate;
use think\Config;

class Appoint extends Base
{
    public function settings()
    {
        if(request()->isAjax()){
            $validate = new AppointSettingValidate();
            //验证完以后进行数据判断
            if(!$validate->goCheck()){
                return Util::showMsg(AdminoperEnum::PARAM_FAIL,array_values($validate->getError())[0]);
            }
            $data = input("post.");
            //根据id进行判断 有id 更新  无id保存
            if(!AppointSettingsModel::setSettings($data)){
                return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
            }
            return Util::showMsg(StatusEnum::SUCCESS,"操作成功");
        }

        $return = AppointSettingsModel::getSettings();
        if(!$return){
            $return = [
                'before_time' => config("default_appoint_setting.before_time"),
                'limit_time' => config("default_appoint_setting.limit_time"),
                'notify_type' => config("default_appoint_setting.notify_type"),
                'notify_cs_type' => config("default_appoint_setting.notify_cs_type")
            ];
        }
        $this->assign([
            'return'=>$return
        ]);
        return view();
    }

    public function index(){
        if(request()->isAjax())
        {
            $data = \app\common\model\Appoint::getAppointByPage();
            $return = [
                'rows' => $data,
                'total' => count($data)
            ];
            return $return;
        }
        return view();
    }

    public function add(){
        if(request()->isAjax()){
            $validate = new AppointValidate();
            //验证完以后进行数据判断
            if(!$validate->goCheck()){
                return Util::showMsg(AdminoperEnum::PARAM_FAIL,array_values($validate->getError())[0]);
            }
            $param = request()->param();
            //一种是添加新预约主题  一种是编辑 区别在于有没有
//            $data = [
//                $title => $param['title'],
//                image_id =>$("#image_id").val(),
//                description:$("#desc").val(),
//                appoint_time_list:$("input[name=srvtime]").val(),
//                exclude_date:exclude_date,
//                notify_cs_type:$("input[name=notify_cs_type][checked]").val(),
//                begin_time:$("#begin_time").val(),
//                end_time:$("#end_time").val(),
//                appoint_days:$("#appoint_days").val(),
//                notify_email:$("#notify_email").val(),
//                cs_templateid:$("#cs_templateid").val(),
//                fans_templateid:$("#fans_templateid").val(),
//                pre_total:$("#pre_total").val(),
//                day_total:$("#day_total").val(),
//                edit:$(".edit").val(),
//                code:$(".code").val(),
//                follow:$(".follow").val(),
//                isshow:$(".isshow").val()
//            ];
            if($param['id']){
                if(!\app\common\model\Appoint::update($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"更新成功");
                }
            }else{
                if(!\app\common\model\Appoint::create($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"添加成功");
                }
            }
        }
        $data = [];
        if($id = input('param.ids')){
            $data = \app\common\model\Appoint::getOne($id);
        }
        $this->assign([
            'data'=>$data
        ]);
        return view();
    }
}