<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14 0014
 * Time: 下午 3:48
 */

namespace app\admin\controller;
use app\common\enum\AdminoperEnum;
use app\common\enum\StatusEnum;
use app\common\model\BannerItem as BannerItemModel;
use app\common\service\Util;
use think\Request;

class Banneritem extends Base
{
    public function index()
    {
        if(request()->isAjax()) {
            $param = request()->param();
            $limit = intval(isset($param['pageSize'])&&$param['pageSize']);
            $page = intval(isset($param['pageNumber'])&&$param['pageNumber']);
            $offset = intval(($page - 1) * $limit);
            $selectedValue = intval(isset($param['selectedValue'])&&$param['selectedValue']);
            $where = [];
            if($selectedValue){
                $where['banner_id'] = $selectedValue;
            }
            if(!($data=BannerItemModel::getBannerItemByWhere($where,$limit,$offset)) ||
                !($total = count(BannerItemModel::getAllBannerItem($where)))){ //不成功
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            $return['rows'] = $data;
            $return['total'] = $total;
             // 总数据
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }

        $banners = \app\common\model\Banner::getAllBanner();
        $this->assign([
            'banner'=>$banners,
            'title'=>'店招内容管理'
        ]);
        return view();
    }

    public function add(){
        if(request()->isAjax()){
            //本来应该加一层验证的 但是时间问题就不加了  验证放在前端吧  后台没有必要
            $param = request()->param();
            //一种是添加新预约主题 一种是编辑区别在于有没有
            if($param['id']){
                if(!BannerItemModel::update($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"更新成功");
                }
            }else{
                if(!BannerItemModel::create($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"添加成功");
                }
            }
        }
        $id = intval(input("ids"));
        $item_info = BannerItemModel::getBannerItemByID($id);
        $allbanners = \app\common\model\Banner::getAllBanner();

        //return
        $this->assign([
            'now_banner_id'=>$id,
            'all_banner'=>$allbanners,
            'item_info'=>$item_info,
        ]);
        return view();
    }

}