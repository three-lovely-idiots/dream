<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 12/31/18
 * Time: 9:25 PM
 */

namespace app\admin\controller;

use app\common\enum\AdminoperEnum;
use app\common\enum\StatusEnum;
use app\common\model\Image as ImageModel;
use app\common\service\CoreSeek;
use app\common\service\Util;
use app\common\validate\AttachmentEditValidate;
use app\common\model\ImgCat as ImgCatModel;

class Attachment extends Base
{
    public function index(){
        if(request()->isAjax())
        {
            $all = ImageModel::getImagePagnation();
            $return['rows'] = $all;
            $return['total'] = count(ImageModel::all());
            return $return;
        }
        $cats = ImgcatModel::all()->toArray();
        $cats = getTree2($cats,0);
        $this->assign([
            'category' => $cats
        ]);
       return view();
    }

    public function add(){
        return view();
    }

    public function edit(){
        //after edit we should use validate
        $ImageModel = new ImageModel();
        if(request()->isAjax())
        {
            $validate = new AttachmentEditValidate();
            //验证完以后进行数据判断
            if(!$validate->goCheck()){
                return Util::showMsg(AdminoperEnum::PARAM_FAIL,array_values($validate->getError())[0]);
            }
            $param = request()->param();
            $data = [
                'id'=>intval($param['id']),
                'url'=>$param['url'],
                'imagewidth'=>$param['imagewidth'],
                'imageheight'=>$param['imageheight'],
                'imagetype'=>$param['imagetype'],
                'update_time'=>time()
            ];
            if(!ImageModel::update($data)){
                return Util::showMsg(StatusEnum::FAIL,"数据库操作失败");
            }
            return Util::showMsg(StatusEnum::SUCCESS,"操作成功");
        }

        $ids = intval(input('param.ids'));
        //就当存在
        if($ids){
            $ids = explode(',',$ids);
        }
        //拿到id开始取数据
        $data = $ImageModel->where(['id'=>$ids[0]])->find()->toArray();
        $data['url'] = str_replace("/images","",$data['url']);
        $this->assign([
            'data'=>$data
        ]);
        return view();
    }

    public function delete(){
        if(request()->isAjax())
        {
            $params = input('post.params');
            $ids = explode(',',$params);
            if(empty($ids)){
                Util::showMsg(AdminoperEnum::ILLEGAL_OPERATION,"非法操作");
            }
            if(ImageModel::destroy($ids)){
                Util::showMsg(StatusEnum::SUCCESS,"删除成功");
            }else{
                Util::showMsg(StatusEnum::FAIL,"删除失败");
            }
        }
    }

    public function select(){

        if(request()->isAjax())
        {
            if(!($all = ImageModel::getImagePagnation())){ //不成功
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            $return['rows'] = $all;
            $return['total'] = count(ImageModel::all());
            // 总数据
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }

        return view();
    }

    public function uploadFileList($type = 'image', $page = 1, $dataType = 'json', $group_id = -1)
    {
        $offset = ($page - 1) * 20;
        $where = [];


        if ($group_id > 0) {
           $where = ['img_cat'=>$group_id];
        }
        if ($group_id == 0) {
            $where = ['img_cat'=>0];
        }

        $list = ImageModel::getImagePagnation($where)->toArray();

        foreach ($list as $index => $value) {
            $list[$index]['selected'] = 0;
        }
        if ($dataType == 'json') {
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'list' => $list,
                ],
            ];
        }
        if ($dataType == 'html') {
            $this->layout = false;
            return $this->render('upload-file-list', [
                'list' => $list,
            ]);
        }
    }


}
