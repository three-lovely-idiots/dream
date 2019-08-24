<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 2017/8/8
 * Time: 14:48
 */

namespace app\common\service;

use app\common\model\Order as OrderModel;
use app\common\service\Order as OrderService;
use app\common\model\Product as ProductModel;
use app\common\enum\OrderStatus as OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;


Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{
//<xml>
//<appid><![CDATA[wx2421b1c4370ec43b]]></appid>
//<attach><![CDATA[支付测试]]></attach>
//<bank_type><![CDATA[CFT]]></bank_type>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[Y]]></is_subscribe>
//<mch_id><![CDATA[10000100]]></mch_id>
//<nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
//<openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
//<out_trade_no><![CDATA[1409811653]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
//<sub_mch_id><![CDATA[10000100]]></sub_mch_id>
//<time_end><![CDATA[20140903131540]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
//</xml>

    public function NotifyProcess($data,&$msg){

        file_put_contents(ROOT_PATH.'my2log.txt',"kkllll");
        if($data['result_code'] == 'SUCCESS') {
            $orderNO = $data['out_trade_no'];

            Db::startTrans();
            try {
                $order = OrderModel::where('order_no', '=', $orderNO)->find();
                if ($order->status == 1) {
                    $orderService = new OrderService();
                    $status = $orderService->checkProductsStock($order->id);

                    if ($status['pass']) {
                        $this->updateOrderStatus($order->id, true);
//                        $this->reduceStock($status);
                    }

                }
                Db::commit();
            } catch (Exception $ex) {
                Db::rollback();
                Log::error($ex);
                // 如果出现异常，向微信返回false，请求重新发送通知
                return false;
            }
        }
        return true;
    }


    private function reduceStock($status){
//        $pIDs = array_keys($status['pStatus']);
        foreach ($status['pStatusArray'] as $singlePStatus) {
            ProductModel::where('id', '=', $singlePStatus['id'])
                ->setDec('stock', $singlePStatus['count']);
        }
    }

    private function updateOrderStatus($orderID, $success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_OUT_OF_STOCK;

        OrderModel::where('id', '=', $orderID)
            ->update(['status' => $status]);
    }


}