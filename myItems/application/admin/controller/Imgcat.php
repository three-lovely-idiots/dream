<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 4/3/19
 * Time: 1:57 AM
 */

namespace app\admin\controller;

use app\common\enum\StatusEnum;
use app\common\model\ImgCat as ImgcatModel;
use app\common\service\Util;

class Imgcat extends Base
{
    public function index()
    {
        if(request()->isAjax())
        {
            $return = [];
            $all = ImgcatModel::getPagnation();
            if($all->isEmpty()){//empty说明没有数据
                $return['code'] = StatusEnum::FAIL;
                $return['msg'] = '没有数据';
            } else {
                $return['code'] = StatusEnum::SUCCESS;
                $return['msg'] = '加载成功';
                $return['rows'] = getTree2($all->toArray(),0);
                $return['total'] = count(ImgcatModel::all());
            }
            return $return;
        }
        return view();
    }

    public function add()
    {
        if(request()->isAjax()){
            $params = request()->param();
            $params['update_time'] = strtotime('now');
            if($params['id']){
                if(!ImgcatModel::update($params)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"更新成功");
                }
            }else{
                if(!ImgcatModel::create($params)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"添加成功");
                }
            }
        }

        $cats = ImgcatModel::all()->toArray();
        $cats = getTree2($cats,0);
        //这里的data是编辑时候的data get方式传递参数
        $data = [];
        if($id = input('param.ids')){
            $data = ImgcatModel::getImgcatByID($id)->toArray();
        }

        $this->assign([
            'category' => $cats,
            'data' => $data,
        ]);
        return view();
    }

    public function edit()
    {

    }

    public function del()
    {

    }

    /**
     * 新的图片管理
     */

    public function cats(){

        $cats = [
            [
                'id'=>-1,
                'name'=>'全部',
                'is_default' => 1
            ],
            [
                'id'=>0,
                'name'=>'未分组',
                'is_default' => 1
            ]
        ];
        $cats2 = ImgcatModel::all(['pid'=>0])->toArray();

        if(!empty($cats2)){
            $cats = array_merge($cats,$cats2);
        }else{
            $cats = $cats;
        }
        return [
            'code' => StatusEnum::SUCCESS,
            'data' => $cats
        ];
    }

    //编辑当个分组
    public function editGroupOne()
    {
        if(request()->isAjax()){

            //新建分组或者保存分组
            if(!ImgcatModel::saveCats()){
                return Util::showMsg(StatusEnum::FAIL,"更新失败");
            }
            //找出全部分组 返回
            $data = $this->cats()['data'];
            return [
                'code' => StatusEnum::SUCCESS,
                'data'=>$data
            ];
        }
        return Util::showMsg(StatusEnum::FAIL,"非法操作");
    }


}