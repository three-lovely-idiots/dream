<?php
namespace app\common\task\swooletask;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/3 0003
 * Time: 上午 10:30
 * 余额 积分的增加减少  记录财务信息 全部放到这里去处理
 */

class FinanceTask
{
    public $finance_logic_path = '\app\common\task\logic\FinanceLogic';
    /**
     * @param $data
     *  ['type'=>'coin'|'balance']
     *  coin => 积分操作
     *  balance => 余额
     *  积分操作类型的actions
     *  ['action'=>'1 sign'|'2 share'|'3 purchase_reduce'|'4 purchase_increase']
     *  余额操作类型的action
     *  [以后再加]
     */
    public function run($serv,$data)
    {
        //判断类型是coin积分 还是balance余额
        $type = isset($data['type']) ? isset($data['type']) : '';
        $class = '';
        $action = '';
        $params = '';
        if($type == 'coin'){
            switch($data['action'])
            {
                case 'share':
                    //任务逻辑类的类名
                    $class = $this->finance_logic_path;
                    //即将被投递的任务逻辑类的方法
                    $action = 'shareAddCoin';
                    //看看有没有附加信息
                    $params = isset($data['params']) ? $data['params'] : '';
                    break;
                default:
                    return [
                        'action'   => 'undefided',
                        'status' => false
                    ];
            }
        }else if($type == 'balance'){

        }else{
            return false;
        }
        //如果以上都没有出现差错 那么就进行真正意义上的任务投递
        $serv->task([
            'class' => $class,
            'action' => $action,
            'params' => $params //这个参数是定义好的参数 必须规定好不会出纰漏
        ]);

        return true;
    }
}