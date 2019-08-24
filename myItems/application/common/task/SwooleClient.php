<?php
namespace app\common\task;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/3 0003
 * Time: 上午 10:51
 */

class SwooleClient
{
    public $client;
    public function __construct()
    {
        $this->client = new \swoole_client(SWOOLE_SOCK_TCP);
        $this->client->connect('127.0.0.1', 8814, 1);
    }

    /**
     * @param $task  app/task/swooletask/... 任务类名
     * @param $data  具体的任务数据
     */
    public function send($task,$data)
    {
        $task_data = json_encode(['task'=>$task,'data'=>$data]);
        $this->client->send($task_data);
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->client->close();
    }
}