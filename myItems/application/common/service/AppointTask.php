<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 12/20/18
 * Time: 10:16 PM
 */

namespace app\common\service;


use app\common\enum\SocketOper;
use app\common\model\AppointRecord;

class AppointTask
{
    public function pushExpired($data,$ws)
    {
        $clients = Predis::getInstance()->redis->sMembers("client_id");
        if($data){
            $data['code'] = SocketOper::APPOINTED_TEMP_EXPIRED;
            unset($data['cmd']);
        }
        foreach($clients as $fd){
            $ws->push($fd,json_encode($data,true));
        }
    }

    public function deleteAppoint($data,$ws)
    {
        $appointRecord = new AppointRecord();
        $appointRecord->where(['appoint_key'=>$data['appoint_key']])->delete();
    }
}