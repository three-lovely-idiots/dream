<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/7/19
 * Time: 10:57 PM
 */

namespace app\admin\controller;


use AliyunMNS\Model\MailAttributes;
use app\common\enum\AdminoperEnum;
use app\common\enum\StatusEnum;
use app\common\model\NailMainStyles;
use app\common\model\NailStyles;
use app\common\service\Util;

class Nailstyle extends Base
{

    public function mainStyle(){
        if(request()->isAjax()){
            //这个写到了model里面都可以采用这种方式
            $data = NailMainStyles::getMainStylesPagnation();
            $return['total'] = count(NailMainStyles::all());  // 总数据
            $return['rows'] = $data;
            return $return;
        }
        return view();
    }

    public function addmain(){
        if(request()->isAjax()){
            $param = request()->param();
            if($param['id']){
                if(!NailMainStyles::update($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"更新成功");
                }
            }else{
                if(!NailMainStyles::create($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"添加成功");
                }
            }
        }
        $data = [];
        if($id = input('param.ids')){
            $data = NailMainStyles::getOne($id);
        }
        $this->assign([
            'data'=>$data
        ]);
        return view();
    }

    public function delmain(){
        if(request()->isAjax())
        {
            $params = input('post.params');
            $ids = explode(',',$params);
            if(empty($ids)){
                Util::showMsg(AdminoperEnum::ILLEGAL_OPERATION,"非法操作");
            }
            if(NailMainStyles::destroy($ids)){
                Util::showMsg(StatusEnum::SUCCESS,"删除成功");
            }else{
                Util::showMsg(StatusEnum::FAIL,"删除失败");
            }
        }
    }

    public function style(){
        if(request()->isAjax()){
            if(!($data=$data =  NailStyles::getNailStylesPagnation())){ //不成功
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            //后期优化时候把where条件限定加上
            $return['total'] = count(NailStyles::all());  // 总数据
            $return['rows'] = $data;
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }
        return view();
    }

    public function addstyle(){
        if(request()->isAjax()){
            $param = request()->param();

            if($param['id']){
                if(!NailStyles::update($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"更新成功");
                }
            }else{
                if(!NailStyles::create($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"添加成功");
                }
            }
        }

        $main_styles = NailMainStyles::all();
        $data = [];
        if($id = input('param.ids')){
            $data = NailStyles::getOne($id)->toArray();
        }
        $this->assign([
            'data'=>$data,
            'mainStyles'=>$main_styles
        ]);
        return view();
    }

    public function delstyle(){

    }
}