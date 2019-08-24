<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9 0009
 * Time: 下午 6:26
 */

namespace app\index\controller;
use app\common\enum\StatusEnum;
use app\common\service\Util;
use app\common\validate\OrderPlaceValidate;
use app\common\service\Order as OrderService;
use app\common\service\Token;
use app\common\model\Order as OrderModel;

class Order extends Base
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only'=>'placeOrder']
    ];
    //点击去付款时候的下单接口
    public function placeOrder(){

        (new OrderPlaceValidate())->goCheck();
        if(request()->isPost()){
            $oProducts = request()->param()['products'];
            //这里是库存检测的好时机 但是我们继续往下面走吧 以后再优化
            $uid = Token::getCurrentUidByToken();
            $Order= new OrderService();
            $res = $Order -> place($uid,$oProducts);
            if($res['orderID'] == -1){
                return Util::showMsg(StatusEnum::FAIL,'创建订单失败！！');
            }
            return Util::showMsg(StatusEnum::SUCCESS,['data'=>$res,'msg'=>'创建订单成功！！']);
        }

        return Util::showMsg(StatusEnum::ILLEAG_ACCESS,'非法访问！！');
//        $orderID = intval($res['orderID']);
//        $pay =  new Pay();
//        $pay->getPreOrder($orderID);
    }

    /**
     * 前端的用户列表
     * @return mixed
     */
    public function orderList(){
        $uid = Token::getCurrentUidByToken();
        $order = OrderModel::where(['user_id'=>$uid])->where('status','<>',0)->select();
        return $order;
    }

    /**
     * @param $id   order表的id
     * @return mixed
     */
    public function getOrderByID($id){
        $res = OrderModel::getOrderInfo($id);
        return json_decode($res,true);
    }
}