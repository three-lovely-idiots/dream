<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/9/19
 * Time: 9:29 PM
 */

namespace app\admin\controller;


use app\common\enum\StatusEnum;
use app\common\model\NailMainStyles;
use app\common\model\NailStyles;
use app\common\model\SelectedImgStyle;
use app\common\service\Util;
use app\common\model\SelectedImage as SelectedImageModel;

class SelectedImage extends Base
{
    public function index(){
        if(request()->isAjax()){
            $data =  \app\common\model\SelectedImage::getPagnation();//这里我把这个方法提到了model基础类里面
            $return['total'] = count(\app\common\model\SelectedImage::all());  // 总数据
            $return['rows'] = $data;
            return $return;
        }
        return view();
    }

    public function add(){
        if(request()->isAjax()){
            //取到post数据
            $params = input("post.");
           //如果能获取到id说明是编辑添加 这个时候第一点是删除中间表数据
            if(isset($params['id'])){
                if(!\app\common\service\SelectedImage::deleteOriginSelected($params['id']))
                {
                    return Util::showMsg(StatusEnum::FAIL,"删除中间表数据失败");
                }
            }
            $default_tag_title = isset($params['default_tag_title']) ? $params['default_tag_title'] : '';
            $other_tags = isset($params['other_tags']) ? $params['other_tags'] : '';
            $tag_data = $default_tag_title;
            //自定义标签入库
            if(strlen($other_tags) > 0){
                \app\common\service\SelectedImage::dealWithOtherTags($other_tags);
                $tag_data = $default_tag_title.','.$other_tags;
            }
            //然后处理中间表,tag与图片的中间表
            $add_data = [
                'img_id'=>$params['img_id'],
                'name'=>$params['name'],
                'tag_data'=>$tag_data,
                'create_date'=>strtotime('now')
            ];
            $msg = '';
            if($params['id']){ //判断是否存在 更新
                $add_data['id'] = $params['id'];
                $result = \app\common\model\SelectedImage::update($add_data);
                $msg = '更新';
            }else{ //添加
                $result = \app\common\model\SelectedImage::create($add_data);
                $msg = '添加';
            }
            if(!($result)){
                return Util::showMsg(StatusEnum::FAIL,$msg."图片数据失败");
            }

            if(!\app\common\service\SelectedImage::updateSelectedImgStyle($result,$tag_data)){
                return Util::showMsg(StatusEnum::FAIL,"添加中间表数据失败");
            }else{
                return Util::showMsg(StatusEnum::SUCCESS,"添加中间表数据成功");
            }
        }

        $data = [];
        $all_styles = [];
        if($id = input('param.ids')){
            $data = \app\common\model\SelectedImage::getOne($id); //此图片的基础数据
            $all_styles = SelectedImgStyle::getStylesByID($id);
        }

        $self_styles = [];//自定义标签
        $default_styles = [];//默认标签
        if(count($all_styles) > 0){
            foreach($all_styles as $key => $value){
                if($value['nail_styles']['type'] != 0){
                    $self_styles[] = $value['nail_styles']['title'];
                }else{
                    $default_styles[] = $value['nail_styles']['title'];
                }
            }
        }

        $style_tags = NailMainStyles::getAllStyles();
        $this->assign([
            'data'=>$data,
            'style_tags'=>$style_tags,
            'self_styles'=>json_encode($self_styles,true),
            'default_styles'=>$default_styles
        ]);

        return view();
    }

    public function del(){
        if(request()->isAjax())
        {
            $result = true;
            $params = input('post.params');
            $ids = explode(',',$params);

            if(empty($ids)){
                Util::showMsg(AdminoperEnum::ILLEGAL_OPERATION,"非法操作");
            }
            //删除中间表与主表
            foreach($ids as $key=>$value){
                if( !\app\common\service\SelectedImage::deleteOriginSelected($value) ) //删除中间表的数据
                {
                    $result = false;
                };

                //删除主表 图片表
                if(!\app\common\model\SelectedImage::destroy($value)){
                    $result = false;
                }
            }
            //返回结果
            if($result){
                return Util::showMsg(StatusEnum::SUCCESS,"删除成功");
            }else{
                return Util::showMsg(StatusEnum::FAIL,"删除失败");
            }
        }
    }

    public function select(){
        if(request()->isAjax())
        {
            if(!($all = SelectedImageModel::getImagePagnation())){ //不成功
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            $return['rows'] = $all;
            $return['total'] = count(SelectedImageModel::all());
            // 总数据
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }
        return view();
    }
}