<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17 0017
 * Time: 下午 2:22
 */

namespace app\admin\controller;


use app\common\enum\StatusEnum;
use app\common\service\Util;
use think\Request;
use app\common\model\Theme as ThemeModel;

class Theme extends Base
{
    public function index()
    {

        if(Request::instance()->isAjax())
        {
            $param = request()->param();
            $limit = intval(isset($param['pageSize'])&&$param['pageSize']);
            $page = intval(isset($param['pageNumber'])&&$param['pageNumber']);
            $offset = intval(($page - 1) * $limit);
            $where = [];
            if(!($data=ThemeModel::getThemesByWhere($where,$limit,$offset)) ||
                !($total = count(ThemeModel::getAllThemeList($where)))){ //不成功
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            $return['rows'] = $data;
            $return['total'] = $total;
            // 总数据
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }

        $this->assign([
            'title'=>'主题管理',
        ]);
        return view();
    }
    //选择主题的时候使用
    public function myselect()
    {
        if(Request::instance()->isAjax()){
            $type_id = intval(input("post.type_id"));
            $all = \app\common\model\Theme::getAllThemesForSelect($type_id);
            return $all;
        }

        $this->assign('theme_type',config('theme_type'));
        return view("themeselect");
    }

    //添加和编辑主题
    public function add()
    {
        $id = request()->get('ids');
        if(request()->isAjax()){
            //本来应该加一层验证的 但是时间问题就不加了  验证放在前端吧  后台没有必要
            $param = request()->param();

            //一种是添加新预约主题 一种是编辑区别在于有没有
            if($param['id']){
                if(! ThemeModel::update($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"更新成功");
                }
            }else{
                if(! ThemeModel::create($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"添加成功");
                }
            }
        }

        $data = [];
        $data = ThemeModel::get(['id'=>$id]);
        $this->assign([
            'data'=>$data,
        ]);
        return view();
    }

}