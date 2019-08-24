<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12
 * Time: 16:46
 */

namespace app\common\service;


use app\common\model\OrderProduct;
use app\common\model\Product as ProductModel;
//use app\common\model\UserAddress;
use app\common\exception\OrderException;
use app\common\exception\UserException;
use think\Exception;
use app\common\model\Order as OrderModel;


class Order
{
     protected $oProducts;
     protected  $products;
     protected  $uid;

     public function place($uid,$oProducts){
           $this->oProducts = $oProducts;
           $this->uid = $uid;
           //根据产品id数组 找出所有相关产品的信息
           $this->products = $this->getProductsByOrder($oProducts);
           $status = $this->getOrderStatus();
          if(!$status){
              $status['order_id'] = -1;
              return $status;
           }
        //开始创建订单 订单快照
          $snapOrder = $this -> snapOrder($status);
          $res = $this->createOrder($snapOrder);

         return $res;
     }
////这里生成订单的订单号核销二维码
//    private function generateQRCodeForOrder($orderNO){
//        $qr_img = getQRcode($orderNO,2);
//        return $qr_img;
//    }

//这里生成订单的订单号核销二维码 我们采用微信的二维码接口来做这个
    private function generateQRCodeForOrder($orderNO){
         return '测试';
        $qr_img = getQRcode($orderNO,2);
        return $qr_img;
    }


    private function createOrder($snap){

        try {
            $orderNo = $this->makeOrderNo();
            //这里我的qrcode要按照微信官方的方式去验证 通过微信本身的扫一扫
            $qrImg = $this->generateQRCodeForOrder($orderNo);
            $OrderModel = new OrderModel();
            $OrderModel->user_id = $this->uid;
            $OrderModel->order_no = $orderNo;
            $OrderModel->total_price = $snap['totalPrice'];
            $OrderModel->total_count = $snap['totalCount'];
            $OrderModel->snap_img = $snap['snapImg'];
            $OrderModel->snap_address = $snap['snapAddress'];
            $OrderModel->snap_name = $snap['snapName'];
            $OrderModel->snap_items = json_encode($snap['pStatus'], true);
            $OrderModel->create_time = strtotime('now');
            $OrderModel->qrImg = $qrImg;
            $OrderModel->save();

            $OrderID = intval($OrderModel->id);
            $createTime = $OrderModel->create_time;
            foreach ($this->oProducts as $key => &$value) {
                $value['order_id'] = $OrderID;
            }

            $OrderProductModel = new OrderProduct();
            //保存到附加表里面
            $OrderProductModel->saveAll($this->oProducts);

            return [
                'pass'=>true,
                'orderNO' => $orderNo,
                'orderID' => $OrderID,
                'create_time' => $createTime
            ];
         }catch(Exception $ex){
            throw $ex;
        }
        }


    private function snapOrder($status){


        $snap = [
            'totalPrice' => 0,
            'totalCount' => 0,
            'snapAddress'=> '',
            'snapName' => '',
            'snapImg'=>'',
            'pStatus' => []
        ];

        $snap['totalPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];

//        $snap['snapAddress'] = json_encode($this->getUserAddress(),true);
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];

          return $snap;
    }

//    private function getUserAddress(){
//        $userAddress = UserAddress::where('user_id','=',$this->uid)->find();
//
//        if(!$userAddress){
//            throw new UserException([
//                'msg' => '所查询用户地址信息不存在',
//                'errorCode'=>70001
//            ]);
//        }
//
//        return $userAddress->toArray();
//    }

    public function getOrderStatus(){
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];

        foreach($this->oProducts as $oProduct){
            $pStatus = $this->getProductStatus($oProduct['product_id'],$oProduct['count'],$this->products);

            if($pStatus['haveStock'] == false){
                 $status['pass'] = false;
            }

             $status['orderPrice'] += $pStatus['totalPrice'];
             $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'],$pStatus);
        }

        return $status;
    }

    public function checkProductsStock($id){
        $oProducts = OrderProduct::where('order_id','=',$id)->select()->toArray();
        $products = $this->getProductsByOrder($oProducts);

        $this->oProducts = $oProducts;
        $this->products =  $products;

        $status = $this->getOrderStatus();

        return $status;

    }
    private function getProductStatus($opid,$count,$products){
        $pIndex = -1;
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => 0
        ];

        for($i=0;$i<count($products);$i++){
             if($opid == $products[$i]['id']){
                 $pIndex = $i;
             }
        }

        if($pIndex == -1){
            throw new OrderException([
                'msg' => 'id为'.$opid.'的商品不存在'
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['count'] = $count;
            $pStatus['name'] = $product['name'];
            $pStatus['totalPrice'] = $count*$product['discount_price'];
            $pStatus['imgUrl'] = $product['main_img_url'];
            $pStatus['price'] = $product['price'];

            if($product['stock'] - $count >= 0){
                $pStatus['haveStock'] = true;
            }
        }

        return $pStatus;
    }

     private function getProductsByOrder($oProducts){
            $pid = [];
            foreach($oProducts as $item){
                 array_push($pid,$item['product_id']);
            }
            $products = ProductModel::getProductsInIds($pid);
         return $products;
     }


    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    public static function orderUnpaiedList($uid){
        if($res =\app\index\model\Order::getUpaiedOrderTotalNum($uid)){
            return $res;
        }
        return 0;
    }

    public static function cfvOrder($id){
        $uid = Token::getCurrentUidByToken();
        $orderModel = new \app\index\model\Order();
       if($res = $orderModel->field("id,order_no,status,snap_img,snap_name,total_count,qrImg")->where(["user_id"=>$uid,"id"=>$id])->find()){
           return $res->toArray();
       }
    }
}