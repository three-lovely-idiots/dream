<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/16/19
 * Time: 11:22 PM
 */

namespace app\index\controller;

use app\common\enum\StatusEnum;
use app\common\model\SelectedImage;
use app\common\model\SelectImageUser;
use app\common\service\CollectService;
use app\common\service\Util;
use think\Cache;
use app\common\service\Token;

class GetLike extends Base
{
    protected $beforeActionList = [
        'setCollectUserToRedis' => ['only'=>'getcurrentimage']
    ];
    //取出当年图片方法 getcurrentimage的前置操作方法
    public function setCollectUserToRedis(){
        $tid = request()->param('id');
        $uid = Token::getCurrentUidByToken();
        $type = 'image';
        CollectService::setCollectToRedis($uid,$type);
        CollectService::setCollectToRedisTwo($tid,$type);
    }
    //图片内页的显示
    public function getCurrentImage(){
        $uid = \app\common\service\Token::getCurrentUidByToken();
        $post = input('post.');
        $id = isset($post['id']) ? $post['id'] : '';
        $redis_handler = Cache::store('redis')->handler();
        $key = 'collect_user:'.$id.':image';
        $it = null;
        if($id){
            if(!($data = SelectedImage::getOne($id))){
                return Util::showMsg(StatusEnum::FAIL,'获取图片信息失败');
            }else{
                //redis取出关于这幅图片的收藏用户 以及用户总数量
                $res = $redis_handler->hscan($key,$it,'*',1);
                foreach($res as $k => $v){
                    $res[$k] = json_decode($v);
                }
                $len = $redis_handler->hlen($key);
                $data['collected_user'] = $res;
                $data['collected_user_num'] = $len;
                //查看当前的用户是否收藏该图片
                $data['is_collected'] = CollectService::checkCollectStatus('image',$uid,$id);
                return Util::showMsg(StatusEnum::SUCCESS,['data'=>$data]);
            }
        }
    }
   //随机取出六张相关图片
    public function getRelateImage()
    {
        $count = intval(input('post.count'));
        if(!$count){
            $count = 6;
        }
        return Util::showMsg(StatusEnum::SUCCESS,['data'=>SelectedImage::getRand($count)]);;
    }

}