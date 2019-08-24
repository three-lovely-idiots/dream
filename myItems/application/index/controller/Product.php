<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6 0006
 * Time: 下午 12:13
 */

namespace app\index\controller;
use app\common\enum\StatusEnum;
use app\common\enum\TasknameEnum;
use app\common\service\CollectService;
use app\common\service\Util;
use app\common\validate\IDMustBePositiveInt;
use app\common\model\Product as ProductModel;
use app\common\exception\MissException;
use app\common\validate\RecentValidate;
use app\common\exception\ProductException;
use app\common\service\Token;
use app\common\task\SwooleClient;

class Product extends Base
{
//    protected $beforeActionList = [
//        'checkCollectStatus' => ['only'=>'getproductdetail'] //前置方法驼峰不能用  控制器里面方法不能写成驼峰 用来检测权限是最好的
//    ];

    //类目里面 取出某类目下的产品
    public function getAllProductsByCategories($id){

        (new IDMustBePositiveInt())->goCheck();
        $products =  ProductModel::getAllProductsByCategories($id);

        if($products->isEmpty()){
            throw new MissException([
                'msg' => '此类木下没有产品',
            ]);
        }
        return Util::showMsg(StatusEnum::SUCCESS,['data'=>$products]);
    }
  //主页： 主页里面取出当前产品
    public function getRecentProducts($count=6){
        (new RecentValidate())->goCheck();
        $result = ProductModel::getRecent($count);
        if($result->isEmpty()){
            throw new ProductException();
        }
        return Util::showMsg(StatusEnum::SUCCESS,['data'=>$result]);
    }
   //取出产品详细信息
    public function getProductDetail($id,$mid = ''){
        $uid = Token::getCurrentUidByToken();
        (new IDMustBePositiveInt())->goCheck();
        $res = ProductModel::getProductDetail($id);
        if(empty($res)){
            throw new ProductException();
        }
        //属于客户推广给其他客户然后其他客户进来观看此商品 次mid是推广客户本人的需要对此客户进行积分增加操作 直接放入任务队列
        //不能用自己的号去刷单
        if($mid != '' && $mid != $uid){
            $client = new SwooleClient();
            //模拟分享获得积分的操作 投递异步任务
            $data = ['type'=>'coin','action'=>'share','params'=>['mid'=>$mid,'uid'=>$uid]];
            //用枚举类去表示任务类本身名称
            $client->send(TasknameEnum::FINANCE_TASK,$data);
        }
        //检测商品是否被当前用户收集
        if($this->checkCollectStatus()){
            $res['collected'] = 1;
        }else{
            $res['collected'] = 0;
        }
        return Util::showMsg(StatusEnum::SUCCESS,['data'=>$res]);
    }

    public function checkCollectStatus(){
        $id = request()->param('id');
        $uid = Token::getCurrentUidByToken();

       return CollectService::checkCollectStatus('product',$uid,$id);
    }

}