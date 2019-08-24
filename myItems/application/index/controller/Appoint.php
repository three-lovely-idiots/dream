<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\9\8 0008
 * Time: 21:59
 */

namespace app\index\controller;

//use app\index\model\UserAppoint as  UserAppointModel;
//use app\index\model\UserAppoint;
//use app\index\service\Appoint as AppointService;
//use app\index\service\Token;
use app\common\enum\AdminoperEnum;
use app\common\enum\SocketOper;
use app\common\enum\StatusEnum;
use app\common\model\AppointRecord;
use app\common\model\User;
use app\common\service\Predis;
use app\common\service\Util;
use app\common\validate\AppointRecordValidate;
use App\Utils\Exception;
use think\Request;
use think\cache\driver\Redis;

class Appoint extends Base
{
    private $redisHandler = null;

    public function getDayDetail($num){
        $office_time = 'a:2:{s:4:"open";s:5:"09:00";s:5:"close";s:5:"21:00";}';
        $office_time = unserialize($office_time);
        $before_time = 60;//minutes

        $beforeTime = $before_time > 0 ? $before_time * 60 : 0;
        $startTime = strtotime(date('Y-n-j') . ' ' . $office_time['open']);
        $endTime = strtotime(date('Y-n-j') . ' ' . $office_time['close']);
        $day = 2;
        $info = '{"weekset":[1,2,3,4,5],"times":[{"start":"8:00","end":"9:00","number":1},{"start":"9:00","end":"10:00","number":1},{"start":"10:00","end":"11:00","number":1},{"start":"11:00","end":"12:00","number":2},{"start":"13:30","end":"14:30","number":2},{"start":"14:30","end":"15:30","number":1},{"start":"15:30","end":"16:30","number":1},{"start":"16:30","end":"17:30","number":1}]}';

        return [
            'day' => $day,
            'info'=>$info
        ];

    }

    public function saveAppoint(){
        $validate = new AppointRecordValidate();
        //验证完以后进行数据判断
        if(!$validate->goCheck()){
            return json(Util::showMsg(AdminoperEnum::PARAM_FAIL,array_values($validate->getError())[0]));
        }
        $data = input('post.');
        //先判断临时值是否存在 不存在就说明此预约失效
        $tempKey = $data['appoint_key'];
        $redis = Predis::getInstance()->redis;
        if($tempKey&&$redis->exists($tempKey)){
            $redis->del($tempKey);
        }else{
            return json(Util::showMsg(StatusEnum::APPOINT_INVALID,'此预约时间已经失效'));
        }
        $temp_arr = explode('|',$data['appoint_key']);
        $data['user_id'] = $temp_arr[2];
        $data['openid'] = User::get(['id'=>$data['user_id']])->toArray()['openid'];

        //second add the realket and the expired time should be the true appointed time one hour ago
        $realKey = str_replace('keytemp','trukey',$tempKey).'|'.strtotime('now');
        //$catime = strtotime($data['selectedDateAndTime']);
        $redis->setex($realKey,60,$data['num']);
        //save the appoint info in the database we will do it later
        try{
            $data['appoint_key'] = $realKey;
            AppointRecord::create($data);
        }catch(Exception $exception){
            $redis->del($realKey);
            return json_encode(Util::showMsg(StatusEnum::FAIL,"数据库操作失败"));
        }

        return json_encode(Util::showMsg(StatusEnum::SUCCESS,"操作成功"));
    }

    private function getDateArray($num,$tempTime){
        $timeOrder = [];
        $startTimeAppoint = strtotime('now');
        $endTimeAppoint = strtotime('+'.$num.' day');
        $dateArray[date('Y-n-j', $startTimeAppoint)] = date('Y-n-j', $startTimeAppoint);
        for ($date = $startTimeAppoint; $date < $endTimeAppoint; $date = $date + 86400) {
            $dateArray[date('Y-n-j', $date)] = date('Y-n-j', $date);
        }
        $dateArray[date('Y-n-j', $endTimeAppoint)] = date('Y-n-j', $endTimeAppoint);
        foreach ($dateArray as $i => $date) {
            $timeOrder[$date] = $tempTime;
        }
        return $timeOrder;
    }

    public function cancelTempAppoint(){
        $data = input('post.');
        $key = $data['tempKey'];
        $redis = Predis::getInstance()->redis;

        if($redis->exists($key)){
            $redis->del($key);//delete the temp key
        }
        return json_encode(Util::showMsg(SocketOper::APPOINTED_TEMP_DEL,"delete temp key success"),true);
    }
}