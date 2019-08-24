<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/15 0015
 * Time: 下午 8:14
 */

namespace app\admin\controller;

use app\common\enum\StatusEnum;
use app\common\model\Category;
use app\common\model\Product as ProductModel;
use app\common\service\Util;
use think\Cache;
use think\Request;

class Product extends Base
{
    public function mySelect()
    {

        if(Request::instance()->isAjax()){
            $category_id = input("post.category_id");
            if(!$category_id) {
                $all = ProductModel::getAllProductsForSelect();
            }else{
                $all = ProductModel::getAllProductsByCid($category_id);
            }
            return $all;
        }
        $category_info = Category::getSelectCategoryInfo();
        $this->assign('category',$category_info);
        return view("productselect");
    }

    public function index()
    {


        if(Request::instance()->isAjax()){
            //留了where进行各种查询条件的筛选
            if(!($data=ProductModel::getProductPagnation())){ //不成功 total直接从分页数据返回
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            $return['rows'] = $data->toArray();
            $return['total'] = $data->total();
            // 总数据
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }
        return view();
    }

    /**
     * 添加产品/编辑产品
     */
    public function add()
    {
        if(request()->isAjax()){
            $param = request()->param()['data'];
            //去掉里面的file数组元素
            if(isset($param['file'])){
                unset($param['file']);
            }
            if($param['id']){
                $param['create_time'] = strtotime('now');
                if(!ProductModel::update($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"更新成功");
                }
            }else{
                $param['update_time'] = strtotime('now');
                if(!ProductModel::create($param)){
                    return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
                }else{
                    return Util::showMsg(StatusEnum::SUCCESS,"添加成功");
                }
            }
        }

        $category = Category::getCategories();
        $data = [];
        if($id = input('param.ids')){
            $data = ProductModel::get(['id'=>$id])->toArray();
        }
       // $data['original_main_img_url'] = substr($data['main_img_url'],stripos($data['main_img_url'],"images")+6);
        $this->assign([
            'category' => $category,
            'data' => $data,
            'url' => strtolower(request()->controller()).'/'.request()->action()
        ]);
        return view();
    }

    //主题添加产品关联的时候用的
    public function select()
    {
        if(request()->isAjax())
        {
            if(!($all = ProductModel::getProductPagnation())){ //不成功
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            $return['rows'] = $all;
            $return['total'] = count(ProductModel::all());
            // 总数据
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }
        $this->assign([
            'title'=>'选择产品'
        ]);
        return view();
    }

}
