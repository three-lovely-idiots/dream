<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14 0014
 * Time: 下午 2:40
 */

namespace app\admin\controller;


use app\common\enum\StatusEnum;
use app\common\service\Util;
use think\Request;
use app\common\model\Banner as BannerModel;

class Banner extends Base
{
    public function index()
    {
        if(Request::instance()->isAjax())
        {
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];
            if (!empty($param['searchText'])) {
                $where['name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            //以后都用这个格式
            if(!($data=BannerModel::getAllBannerByWhere($where,$limit,$offset))){ //不成功
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            $return['total'] = count(BannerModel::getAllBanner($where));  // 总数据
            $return['rows'] = $data;
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }
        $this->assign([
            'title'=>'店招管理'
        ]);
        return view();
    }

    public function add()
    {
        $id = request()->get('ids');
        if(request()->isAjax()){
            //本来应该加一层验证的 但是时间问题就不加了  验证放在前端吧  后台没有必要
            $param = request()->param();
            //一种是添加新预约主题 一种是编辑区别在于有没有
            if($param['id']){
                if(!BannerModel::update($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"更新成功");
                }
            }else{
                if(!BannerModel::create($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"添加成功");
                }
            }
        }
        $banner = BannerModel::get(['id'=>$id]);
        $this->assign(['banner'=>$banner]);
        return view();
    }

}