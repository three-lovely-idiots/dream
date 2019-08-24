<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/17
 * Time: 13:54
 */

namespace app\index\controller;


use app\common\enum\OrderStatus;
use app\common\enum\StatusEnum;
use app\common\service\Util;
use app\common\validate\IDMustBePositiveInt;
use app\common\model\Order as OrderModel;
use app\common\exception\OrderException;
use app\common\service\WxNotify;
use app\common\service\Pay as PayService;

class Pay extends Base
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only'=>'getPreOrder'] //拦截器思想这个很好用 可以限制前端的权限验证
    ];
    public function getPreOrder($id =''){
        (new IDMustBePositiveInt())->goCheck();
        $payService = new PayService($id);
        $res = $payService->pay();
        return $res;
    }

    public function receiveNotify()
    {

        $xmlData = file_get_contents('php://input');
        file_put_contents(ROOT_PATH.'newlog.txt',$xmlData);

        $notify = new WxNotify();
        $notify->Handle();
//        $xmlData = file_get_contents('php://input');
//        $result = curl_post_raw('http:/zerg.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=13133',
//            $xmlData);
//        return $result;
//        Log::error($xmlData);
    }

    public function checkPayment(){
           $post = input('post.');
           $orderId = $post['orderId'];
           $result = PayService::paymentCheck($orderId);
           return $result;
    }

    public function cancelPayment($orderNO)
    {
        //这个地方只取消微信支付的prepayid 不
        PayService::cancelOrder($orderNO);
        //更改订单状态
        $res = OrderModel::where('order_no', '=',$orderNO)
            ->update(['status' => OrderStatus::CLOSED]);
        if(!$res){
            return Util::showMsg(StatusEnum::FAIL,['res'=>$res]);
        }
        return Util::showMsg(StatusEnum::SUCCESS,['res'=>$res]);
    }

}
