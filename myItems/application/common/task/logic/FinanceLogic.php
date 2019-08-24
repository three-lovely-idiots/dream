<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/3 0003
 * Time: 下午 10:09
 */

namespace app\common\task\logic;


use app\common\model\User;
use app\common\model\UserShare;

class FinanceLogic
{
    /**
     * 分享增加金币
     */
    public function shareAddCoin($params = '')
    {
//        array(2) {
//        ["mid"]=>
//  string(1) "7"
//        ["uid"]=>
//  int(7)


        //先检查时候分享给这个客户过，给用户id为 $mid的加金币比  然后把这两条映射关系入库 留待以后检查用
        if(!empty($params) && isset($params['mid']) && isset($params['uid']) &&
            !UserShare::get(['mid'=>$params['mid'],'sid'=>$params['uid']])) {
            //加金币 增加财务记录  增加分享映射表的财务操作
            return UserShare::addCoindOper($params);
        }else{
            return false;
        }
    } 
}