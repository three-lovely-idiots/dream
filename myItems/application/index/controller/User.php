<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 3/28/19
 * Time: 3:37 AM
 */

namespace app\index\controller;

use app\common\enum\OrderStatus;
use app\common\enum\StatusEnum;
use app\common\exception\MissException;
use app\common\model\Sign;
use app\common\service\SignService;
use app\common\service\Token;
use app\common\model\User as UserModel;
use app\common\service\Util;
use think\Exception;
use app\common\model\Order as OrderModel;

class User extends Base
{

    public function my(){
        $res = Token::getCurrentVarsByToken();
        if(request()->isPost()){
            //初始化 1 有没有签到 连续签到天数 2 总积分信息 2 待付款 待消费订单数目 3 预约项目（前台可以做一个倒计时不过这个后话吧）
            $date = $date = date('Ymd');
            $sign_status = 1;
            $return = [];
            //1 有没有签到
            if(!SignService::isCheckSign($res['uid'])){ //没有签到
                $sign_status = 0;
            }
            $return['sign_status'] = $sign_status;
            //连续签到天数
            $return['sign_days'] = SignService::getContinulySignDays($res['uid']);
            //2 查询订单数目 这个后来在做 直接存到redis里面其实很好的 订单的数目信息  一个人一个坑
            $orders = OrderModel::getUserOrderGroupByStatus($res['uid']);
            //分别放到$return里面

            if(!empty($res)){
                foreach($orders as $k=>$v){
                    switch($v['status']){
                        case OrderStatus::UNPAID:
                            $return['unpaied'] = $v['num'];
                            break;
                        case OrderStatus::PAID:
                            $return['paied'] = $v['num'];
                            break;
                        case OrderStatus::USED:
                            $return['unused'] = $v['num'];
                            break;
                    }
                }
            }

            //3 查询预约项目 这个也可以 而且之前好像已经做了 存到redis里面  更新的时候 取消的时候 查找的时候操作redis
            $return['valid_appoint'] = 4;
            //4 用户总积分
            $return['total_money'] =  UserModel::where(['id'=>$res['uid']])->column('money')[0];
            return Util::showMsg(StatusEnum::SUCCESS,$return);
        }
        $this->error('非法操作！');
    }
    public function userInfo()
    {
        $res = Token::getCurrentVarsByToken();
        if(request()->isPost()){
            //传进来的user信息
            $user_info = json_decode(html_entity_decode(request()->param()['user_info']),true);
            if(empty($user_info)){
                throw new MissException([
                    'msg' => '未获取到用户信息'
                ]);
            }
            //根据取出的openid 和 uid去取出数据库里已经存在的用户信息
            try{ //
                //这段从数据库里返回的数据可以缓存起来
                $old_user_info = UserModel::where(['id'=>$res['uid'],'openid'=>$res['openid']])->find()->toArray();
                //比较信息 看看有没有更新用户名和头像 如果有那就更新数据库 如果没有那就直接返回不做任何操作
                if ( ($old_user_info['nickName'] != $user_info['nickName'] || $old_user_info['avatarUrl'] != $user_info['avatarUrl'])){
                    $id = isset($old_user_info['id']) ? $old_user_info['id'] : '';
                    $params['openid'] = $old_user_info['openid'];
                    $params['nickName'] = $user_info['nickName'];
                    $params['gender'] = $user_info['gender'];
                    $params['avatarUrl'] = $user_info['avatarUrl'];
                    $params['extend'] = json_encode([$user_info['language'],$user_info['city'],$user_info['province'],$user_info['country']]);
                    $params['update_time'] = strtotime('now');
                    UserModel::where(['id'=>$id])->update($params);
                    unset($params,$old_user_info,$res,$user_info);
                }else{
                    //不做任何处理 直接返回
                    return true;
                }
            }catch(Exception $e){
                throw new MissException([
                    'msg' => $e->getMessage()
                ]);
            }
        }
    }

    public function sign()
    {
        $user = Token::getCurrentVarsByToken();
        if(request()->isPost()){ //签到逻辑通过post方式提交

//            if($this->_site['sign'] == 0 || !$this->_site['sign']){ //整体设置后期优化时候在加上吧
//                $this->error('未开启签到功能'); //后台需要配置签到功能管理栏目
//            }
            //检测今天是否已经签到
            if(SignService::isCheckSign($user['uid']))
            {
                return Util::showMsg(StatusEnum::FAIL,['msg'=>'今天已经签到过了，明天再来吧']);
            }else{
                //没有签到那就开始签到
                $res = SignService::doSign($user['uid']);
                if($res['status'] == StatusEnum::FAIL){ //表数据存储成功
                    //签到增加用户积分的金额
                    UserModel::where(['id'=>$user['uid']])->setInc('money',$res['money']);
                    //这里应该加一个模板消息发送但是 就先忽略吧
                    return Util::showMsg(StatusEnum::SUCCESS,['money'=>$res['money'],'days_count'=>$res['days_count'],'msg'=>'签到成功']);
                }else{ //表数据存储失败
                    //存储失败以后我们要考虑几点问题
                     //1 redis里面数据还存在 我们必须吧redis里面的数据删除掉 这也是一种回退机制 因为系统故障造成的原因
                    //2 传回的参数必须带上我们之前插入redis的key值，会不会出现两天交界时候的 下一秒问题
                    SignService::eraseSign($user['uid'],$res['key']);
                    return Util::showMsg(StatusEnum::FAIL,['msg'=>'系统故障签到失败，请稍等重试~']);
                }
            }
           //
//            $sign = Sign::where(['date'=>$date,'user_id'=>$res['uid']])->find();
//            if($sign){//判断是否签到
//
//            }else{
//
//                $id = M('sign')->add(array(
//                    'user_id'=>$this->user['id'],
//                    'date'=>$date,
//                    'money'=>$this->_site['sign'],//站点配置里面的sign代表签到金额 可控
//                    'create_time'=>NOW_TIME,
//                ));
//                if($id){//表数据存储成功进入下一步逻辑
//                    M('user')->where(array('id'=>$this->user['id']))->setInc('money',$this->_site['sign']);//签到增加金币金额
//                    flog($this->user['id'], 'money', $this->_site['sign'], 10);//添加财务日志 action就是代表什么样的动作 1 在线充值
//
//                    $dd = new \Common\Util\ddwechat;
//                    //浏览记录
//                    $a = "\n";
//                    $read = M('read')->where(array('user_id'=>$this->user['id']))->order('create_time desc')->find();
//                    if($read){
//                        if($read['type'] == "mh"){ //mh漫画  xs小说 继续上次阅读哈哈
//                            $url = U('Mh/inforedit',array('mhid'=>$read['rid'],'ji_no'=>$read['episodes']));
//                        }else{
//                            $url = U('Book/inforedit',array('bid'=>$read['rid'],'ji_no'=>$read['episodes']));
//                        }
//                        $url = complete_url($url);
//                        $a = "\n\n".'<a href="'.$url.'">点击我继续上次阅读</a>'."\n\n";
//                    }
//
//                    //历史阅读记录
//                    $li = "历史阅读记录\n\n";
//                    $lishi = M('read')->distinct(true)->field('type,rid')->where(array('id'=>array('neq',$read['id']),'user_id'=>$this->user['id']))->select();
//                    if($lishi){//这个逻辑太过懒省事了 还不如在阅读的时候进行表的更新处理 更新阅读时间与阅读章节 而且非要把html放到后台生成 直接前台传数据即可
//                        //误解了这里其实是想通过微信服务号去发信息的
//                        foreach($lishi as $v){
//                            $max = M('read')->where(array('type'=>$v['type'],'rid'=>$v['rid']))->order('episodes desc')->find();
//                            if($read['type'] == "mh"){
//                                $url = U('Mh/inforedit',array('mhid'=>$max['rid'],'ji_no'=>$max['episodes']));
//                            }else{
//                                $url = U('Book/inforedit',array('bid'=>$max['rid'],'ji_no'=>$max['episodes']));
//                            }
//                            $url = complete_url($url);
//                            $li .= '<a href="'.$url.'">>'.$max['title'].'</a>'."\n\n";
//                        }
//                    }else{
//                        $li ="";
//                    }
//
//                    $html = '本次签到成功，赠送'.$this->_site['sign'].'书币，请明天继续签到哦!'.$a.$li.'为方便下次阅读，请置顶公众号';
//                    $dd -> send_msg($this->user['openid'],$html);
//                    $this->success('签到成功');
//                }else{
//                    $this->error('签到失败');
//                }
//            }
//        }else{
//            $this->error('非法请求!');
        }
    }

}