<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9 0009
 * Time: 下午 10:36
 */

namespace app\admin\controller;


use app\common\enum\StatusEnum;
use app\common\model\PointsMallCat;
use app\common\model\PointsMallGoods;
use app\common\model\PointsMallSetting;
use app\common\service\Util;
use think\Request;

class PointMall extends Base
{
    public function settings()
    {
        $setting = PointsMallSetting::find();
        $integral_list = !empty($setting) && isset($setting['integral_shuoming']) ? json_decode($setting['integral_shuoming'],true) : '';
        if(Request::instance()->isAjax()){
              $param = request()->param();
              $data = [
                  'integral_shuoming' => !empty($param['attr']) ?
                      (json_encode(is_array($integral_list) ? array_merge($param['attr'],$integral_list) : $param['attr'])) : json_encode($integral_list), //新旧结合
                  'register_rule' => $param['model']['register_rule'],
                  'register_integral' => $param['model']['register_integral'],
                  'register_continuation' => $param['model']['register_continuation'],
                  'register_reward' => $param['model']['register_reward'],
              ];

               $PointsMallSetting = new PointsMallSetting();
               $where = [];
               if(isset($param['model']['id']) && !empty($param['model']['id'])){
                    $where = ['id' => $param['model']['id']];
               }

                if (($res = $PointsMallSetting->save($data,$where)) || $res == 0) { //没有更新信息 点击保存会返回0
                    return Util::showMsg(StatusEnum::SUCCESS,'成功');
                }else{
                    return Util::showMsg(StatusEnum::FAIL,'失败');
                }
        }
        //这里不分店铺了
        $this->assign([
            'setting' => $setting,
            'integral_list' =>  $integral_list
        ]);
        return view("settings");
    }


    //积分说明删除
    public function attrDelete()
    {
        $id = request()->param()['id'];
        $setting =  PointsMallSetting::find();
        if ($setting && $setting->integral_shuoming) {
            $shuoming = json_decode($setting->integral_shuoming,true);
            $newList = [];
            foreach ($shuoming as $key => $value) {
                if ($key == $id) {
                } else {
                    $newList[] = $value;
                }
            }
            $setting->integral_shuoming = json_encode($newList);
        }
        if ($setting->save()) {
            return [
                'code' => StatusEnum::SUCCESS,
            ];
        } else {
            return [
                'code' => StatusEnum::FAIL,
                'msg' => '网络异常',
            ];
        }
    }

    //积分说明修改
    public function attrEdit()
    {
        $get = request()->param();
        $shuoming = [];
        $id = $get['id'];
        $index = $get['index'];
        $pointMallModel = new PointsMallSetting();
        $setting =  $pointMallModel->find(['id'=>$id]);

        if ($setting && $setting['integral_shuoming']) {
            $shuoming = json_decode($setting['integral_shuoming'],true);
            foreach ($shuoming as $key => &$value) {
                if ($key == $get['index']) {
                    $value['title'] = $get['title'];
                    $value['content'] = $get['content'];
                }
            }

        }

        if ($pointMallModel->update(['id'=>$id,'integral_shuoming'=>json_encode($shuoming)])) {
            return [
                'code' => StatusEnum::SUCCESS,
            ];
        } else {
            return [
                'code' => StatusEnum::FAIL,
                'msg' => '网络异常',
            ];
        }
    }


    // 商品展示
    public function goods()
    {
        $pointMallGood = new PointsMallGoods();
        $arr = $pointMallGood->select()->toArray();
        $this->assign( [
            'list' => $arr,
            //'pagination' => $arr[1],
        ]);
        return view("goods");
    }

    //产品编辑
    public function actionGoodsEdit($id = null)
    {
        $goods = IntegralGoods::findOne(['id' => $id, 'store_id' => $this->store->id]);
        if (!$goods) {
            $goods = new IntegralGoods();
        }
        $form = new IntegralGoodsForm();
        $cat = IntegralCat::find()
            ->andWhere(['is_delete' => 0, 'store_id' => $this->store->id])
            ->asArray()
            ->orderBy('sort ASC')
            ->all();
        $postageRiles = PostageRules::find()->where(['store_id' => $this->store->id, 'is_delete' => 0])->all();
        if (\Yii::$app->request->isPost) {
            $model = \Yii::$app->request->post('model');
            if (!$model['use_attr']) {
                $model['use_attr'] = 0;
            }
            $model['store_id'] = $this->store->id;

            $form->attributes = $model;
            $form->attr = \Yii::$app->request->post('attr');
            $form->goods = $goods;
            return $form->save();
        }
        if ($goods && $goods->goods_pic_list) {
            $goods->goods_pic_list = \Yii::$app->serializer->decode($goods->goods_pic_list);
        }

        $levelForm = new LevelListForm();
        $levelList = $levelForm->getAllLevel();


        // 默认商品服务
        if (!$goods['service']) {
            $option = Option::get('good_services', $this->store->id, 'admin', []);
            foreach ($option as $service) {
                if ($service['is_default'] == 1) {
                    $goods->service = $service['service'];
                    break;
                }
            }
        }

        $goodsNum = 0;
        if (isset($goods['attr'])) {
            $attrs = \Yii::$app->serializer->decode($goods['attr']);
            foreach ($attrs as $attr) {
                $goodsNum += $attr['num'];
            }
            $goods['goods_num'] = $goodsNum;
        }

        return $this->render('goods-edit', [
            'goods' => $goods,
            'cat' => $cat,
            'levelList' => $levelList,
            'postageRiles' => $postageRiles,
        ]);
    }



    // 分类展示
    public function cat()
    {
        if(request()->isAjax()){
            //留了where进行各种查询条件的筛选
            if(!($data=PointsMallCat::getPagnation())){ //不成功 total直接从分页数据返回
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'抱歉数据库提取数据出错']);
            }
            $return['rows'] = $data->toArray();
            $return['total'] = count($data);
            // 总数据
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }

        $this->assign( [
            'title' => '积分商品分类',
            //'pagination' => $arr[1],
        ]);
        return view("cat");
    }


    // 分类编辑
    public function catEdit($id = null)
    {

        $this->assign( [
            'title' => '商品分类编辑',
            'data' => []
        ]);

        if(request()->isAjax()){ //添加时候
            if(!$id){ //没有id天剑

            }else{ //有id更新

            }
            return "dsdsd ";
        }
        return view("catedit");
//        $cat = IntegralCat::findOne(['id' => $id, 'is_delete' => 0, 'store_id' => $this->store->id]);
//        if (!$cat) {
//            $cat = new IntegralCat();
//        }
//        if (\Yii::$app->request->isPost) {
//            $form = new IntegralCatForm();
//            $form->attributes = \Yii::$app->request->post('model');
//            $form->store_id = $this->store->id;
//            $form->cat = $cat;
//            return $form->save();
//        }
//        return $this->render('cat-edit', [
//            'list' => $cat,
//        ]);
    }
}
