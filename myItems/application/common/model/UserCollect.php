<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 1:44
 */

namespace app\common\model;


use app\common\enum\StatusEnum;

class UserCollect extends Base
{
    public function user(){
        return $this->belongsTo('User','uid','id');
    }
    public function selectedImg()
    {
        return $this->belongsTo('SelectedImage','tid','id');
    }

    public function product(){
        return $this->belongsTo('Product','tid','id');
    }

    public function getPicAttr($value,$data){
        $img_perfix = Config('img_perfix');
        $value = $img_perfix.$value;
        return $value;

     }
    //前端用户中心用来获取用户收藏信息 根据type怕判断要的是什么
    public static function getUserCollect($type,$length,$pageNO,$uid)
    {
        $limit = intval($length);
        $offset = intval(($pageNO - 1) * $limit);
        $with = [];
        if($type == 'product'){
            $type = 1;
            $with = ['product'];
        }else{
            $type = 0;
            $with = ['selectedImg','selectedImg.img'];
        }
        return self::with($with)->where(['uid'=>$uid,'type'=>$type])->limit($offset, $limit)->select()->toArray();
    }

    public static function getItemCollectedUser($tid,$type){
        return self::with(['user'])->where(['type'=> ($type == 'product' ? StatusEnum::PRODUCT_TYPE:StatusEnum::IMAGE_TYPE),'tid'=>$tid])->select()->toArray();
    }

    /**
     * @param $type  类型 图片/商品
     * @param $id    收藏表user_collect id
     * @param $uid   当前用户id
     * @param $tid   收藏便对应 图片或者商品的外键
     * @return bool
     * @throws \think\exception\PDOException
     */
    public static function cancelUserCollect($type,$id,$uid,$tid)
    {
        $type = ($type == 'product') ? StatusEnum::PRODUCT_TYPE : StatusEnum::IMAGE_TYPE;
        $item_model = ($type == 'product') ? '\app\common\model\Product' : '\app\common\model\Image';
        $self = new self();
        $self->startTrans();
        $res = $self->where(['id'=>$id,'tid'=>$tid,'uid'=>$uid,'type'=>$type])->delete();

        if(!$res){ //删除失败
            $self->rollback(); //回滚
            return false;
        }

        //然后把对应item的collect数量减一 这里也用事务模型
        $model = new $item_model();
        $model->startTrans();
        $res = $model->where(['id'=>$id])->setDec('collect',1);

        if(!$res){ //collect减1失败
            $self->rollback();//usercollect也回滚
            $model->rollback(); //回滚
            return false;
        }
        //上面都成功的情况下我们commit 提交
        $self->commit();
        $model->commit();
        return true;
    }

}