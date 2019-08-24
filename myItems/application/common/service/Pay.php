<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/17
 * Time: 14:18
 */

namespace app\common\service;


use app\common\enum\OrderStatus;
use app\common\exception\TokenException;
use think\Exception;
use app\common\model\Order as OrderModel;
use app\common\exception\OrderException;

use think\Loader;
use think\Log;

//Loader::import('WxPay.WxPay',EXTEND_PATH,'Api.php');
require_once EXTEND_PATH."/WxPay/WxPay.Api.php";

class Pay
{

//<xml>
//<appid>wx2421b1c4370ec43b</appid>
//<attach>支付测试</attach>
//<body>JSAPI支付测试</body>
//<mch_id>10000100</mch_id>
//<detail><![CDATA[{ "goods_detail":[ { "goods_id":"iphone6s_16G", "wxpay_goods_id":"1001", "goods_name":"iPhone6s 16G", "quantity":1, "price":528800, "goods_category":"123456", "body":"苹果手机" }, { "goods_id":"iphone6s_32G", "wxpay_goods_id":"1002", "goods_name":"iPhone6s 32G", "quantity":1, "price":608800, "goods_category":"123789", "body":"苹果手机" } ] }]]></detail>
//   <nonce_str>1add1a30ac87aa2db72f57a2375d8fec</nonce_str>
//   <notify_url>http://wxpay.wxutil.com/pub_v2/pay/notify.v2.php</notify_url>
//   <openid>oUpF8uMuAJO_M2pxb1Q9zNjWeS6o</openid>
//   <out_trade_no>1415659990</out_trade_no>
//   <spbill_create_ip>14.23.150.211</spbill_create_ip>
//   <total_fee>1</total_fee>
//   <trade_type>JSAPI</trade_type>
//   <sign>0CB01533B8C1EF103065174F50BCA001</sign>
//</xml>
    private $orderID;
    private $orderNO;

    function __construct($id)
    {
        if(!$id){
          throw new Exception('订单id不能为null');
        }

        $this->orderID = $id;
    }

    public function pay(){
        $this->validateOrder($this->orderID);
        $orderService = new Order();
        $orderStatus = $orderService -> checkProductsStock($this->orderID);

        if(!$orderStatus['pass']){
              return [];
        }

       return $this->makePreOrder($orderStatus);
    }

  private function makePreOrder($orderStatus){

      $openID = Token::getCurrentVarsByToken()['openid'];
      if(!$openID){
          throw new TokenException();
      }

      $PreOrderData = new \WxPayUnifiedOrder();
      $PreOrderData->SetOut_trade_no($this->orderNO);
      $PreOrderData->SetOpenid($openID);
      $PreOrderData->SetBody('这是测试');
      $PreOrderData->SetNotify_url('https://www.lymmsf.com/zerg/public/index.php/index/v1/pay/notify');
      $PreOrderData->SetTrade_type('JSAPI');
      $PreOrderData->SetTotal_fee($orderStatus['orderPrice']*100);
      return $this->getPaySignature($PreOrderData);
  }

    private function recordPreOrder($wxData){
              OrderModel::where('id','=',$this->orderID)->update(['prepay_id'=>$wxData['prepay_id']]);
    }
    private function getPaySignature($PreOrderData){

         $wxData = \WxPayApi::unifiedOrder($PreOrderData);
        if($wxData['return_code'] != 'SUCCESS'|| $wxData['result_code'] != 'SUCCESS'){
                Log::record($wxData,'error');
                Log::record('获取预支付订单失败','error');
        }
        $this->recordPreOrder($wxData);
        $signature = $this->sign($wxData);

        return $signature;
    }

    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }
    private function validateOrder($orderID){
        $res = OrderModel::where('id','=',$orderID)->find();
        if(!$res){
            throw new OrderException();
        }
        if(!Token::validateOperate($res->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errCode' => '10003'
            ]);
        }
        if($res ->status != OrderStatus::UNPAID){
            throw new OrderException([
                'msg' => '订单已经付款',
                'errCode' => '80001'
            ]);
        }
        $this->orderNO = $res->order_no;
        return true;
    }

    /**
     * @param $orderID 订单编号  这个方法用来主动查询订单状态
     * @return bool
     * @throws OrderException
     * @throws \WxPayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function paymentCheck($orderID){
        $res = OrderModel::where('id','=',$orderID)->find();
        if(!$res){
            throw new OrderException();
        }
        $orderNO= $res['order_no'];
        $inputObj = new \WxPayOrderQuery();
        $inputObj->SetOut_trade_no($orderNO);

        $result = \WxPayApi::orderQuery($inputObj);

        if($result['trade_state'] == 'SUCCESS'){
             if(self::updateOrderStatus($orderID)){
                 return true;
             }
        }

        return false;
    }

    private static function updateOrderStatus($orderID)
    {
        $status =  OrderStatus::PAID;
        return OrderModel::where('id', '=', $orderID)
            ->update(['status' => $status]);
    }

    //取消订单
    public static function cancelOrder($orderNO){
        $inputObj = new \WxPayCloseOrder();
        $inputObj->SetOut_trade_no($orderNO);
        $result = \WxPayApi::closeOrder($inputObj, 6);

        if($result['return_code'] != 'SUCCESS'|| $result['result_code'] != 'SUCCESS'){
            Log::record($result,'error');
            Log::record('订单取消失败---'.time(),'error');
        }

    }
}