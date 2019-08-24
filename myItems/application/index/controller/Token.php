<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 12/23/18
 * Time: 2:41 AM
 */

namespace app\index\controller;


use app\common\enum\StatusEnum;
use app\common\service\UserToken;
use app\common\validate\NewTokenGetValidate;
use  app\common\validate\TokenGetValidate;
use  app\common\validate\TokenVerifyValidate;
use app\common\service\Token as TokenService;
use app\common\service\Util;
use app\common\lib\EasyWechat;
use app\common\lib\WXBizDataCrypt;
use app\common\model\User as UserModel;


class Token
{
    public function getToken($code = '')
    {
        (new TokenGetValidate())->goCheck();
        $userToken = new UserToken($code);
        $token = $userToken->get();
        return $token;
    }

    /**
     * 用户主动授权的方法运用 ，在必要的栏目用授权
     */
    public function login($code = '', $user_info = '', $encrypted_data = '', $iv = '', $signature = '')
    {
       // 做相应的验证 这个是新的验证方法
        (new NewTokenGetValidate())->goCheck();
        //调取微信接口获取openid
        $app = new EasyWechat();
        $res = $app->getSessionByJSCode($code);
        if (!$res || empty($res['openid'])) {
            return Util::showMsg(StatusEnum::FAIL,'获取用户OpenId失败');
        }
         $session_key = $res['session_key'];
        //这个是之前的加密验证方式
        $data = ''; //引用变量
        $pc = new WXBizDataCrypt(config('wx.app_id'), $session_key);
        $errCode = $pc->decryptData($encrypted_data, $iv, $data);
//        //这里我们不采用easywehcat提供的方法 因为与我们的业务逻辑不相符
//         $decryptedData = $app->getDecryptData($session_key, $iv, $encrypted_data);

        if ($errCode == 0 || $errCode == -41003) { //41003是偶然出现的突发状况
            if ($errCode == -41003) {
                //这个地方的错误一定要记住对于前端传过来的json数据必须有过滤措施
                $user_info = json_decode(htmlspecialchars_decode($user_info),true);
                $data = [
                    'openId' => $res['openid'],
                    'nickName' => $user_info['nickName'],
                    'gender' => $user_info['gender'],
                    'city' => $user_info['city'],
                    'language'=>$user_info['language'],
                    'province' => $user_info['province'],
                    'country' => $user_info['country'],
                    'avatarUrl' => $user_info['avatarUrl'],
                    'unionId' => isset($res['unionid']) ? $res['unionid'] : '',
                ];
            } else {
                $data = json_decode($data, true);
            }
            //找找看库里面有没有这个user 是不是重复授权 第一次进入就保存生成新用户 重复的话那么就更新操作
            $user  = UserModel::get(['openid'=>$data['openId']]);
            //如果没有user就进行下面这一步操作 这是save保存
            if (!$user) {
                $insert_data = [
                    'openid' => $data['openId'],
                    'nickName' => $data['nickName'],
                    'extend' => json_encode([$data['language'],$data['city'],$data['province'],$data['country']]),
                    'delete_time' => 0,
                    'create_time' => time(),
                    'update_time' => time(),
                    'gender' => $data['gender'],
                    'avatarUrl' => $data['avatarUrl'],
                    'access_token' => generateToken($data['openId']),//这里调用common里面的函数 一个人一个编码
                    'platform' => 0,
                    'unionId' => isset($data['unionId']) ? $data['unionId'] : ''
                ];
                $user = UserModel::create($insert_data);

            } else { //这里是update更新
                $update_data = [
                    'openid' => $data['openId'],
                    'nickName' => $data['nickName'],
                    'extend' => json_encode([$data['language'],$data['city'],$data['province'],$data['country']]),
                    'delete_time' => 0,
                    'create_time' => time(),
                    'update_time' => time(),
                    'gender' => $data['gender'],
                    'avatarUrl' => $data['avatarUrl'],
                ];
                UserModel::update($update_data,['id'=>$user->id]);
            }
            unset($insert_data,$data);

            //进行本地缓存 用来进行后面的验证
            //if(!cache($user->access_token)){ //如果没有再缓存
            UserToken::grantToken($user->toArray());
            //}

            return Util::showMsg(StatusEnum::SUCCESS,['data'=>$user->toArray()]);
        } else {
            return Util::showMsg(StatusEnum::FAIL,'登录失败');
        }
    }

    public function verifyToken($token = '')
    {
        (new TokenVerifyValidate())->goCheck();
        $userToken = new UserToken();
        $result =  $userToken->verifyToken($token);
        if($result){
            return ['isValid' => 1];
        }
        return ['isValid' => 0];
    }
}