d<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 12/20/18
 * Time: 7:14 PM
 */

class Ws{
    CONST HOST = "0.0.0.0";
    CONST PORT = "8812";
    CONST APPOINT_PORT = "8813";
    CONST TASK_PORT = "8814";
    CONST CLIENT_ID = "client_id";

    public $ws = NULL;
    public $task_server = NULL;

    //任务类的命名空间
    private $swooleTaskPath = '\\app\common\\task\\swooletask\\';

    public function __construct()
    {
        $this->initRedisData();
        $this->ws = new \swoole_websocket_server(self::HOST,self::PORT);
        //这个是预约专用
        $this->ws->listen(self::HOST,self::APPOINT_PORT,SWOOLE_SOCK_TCP);
        //异步任务投递专用
        $this->task_server = $this->ws->listen(self::HOST,self::TASK_PORT,SWOOLE_SOCK_TCP);
        //这个端口专门监听其他的异步任务
        //$this->ws->listen(self::HOST,9701,SWOOLE_SOCK_TCP);
        //$this->ws->redisPool = new SplQueue();
        $this->ws->set(
            [
                'enable_static_handler' => true,
                'document_root' => "/home/projects/snake-master/public",
                'worker_num' => 4,
                'task_worker_num' => 4,
            ]
        );

        //关闭websocket模式 专门针对task服务
        $this->task_server->set([
            'open_websocket_protocol' => false,
        ]);

        //task服务专用
        $this->task_server->on('receive',[$this,'onTaskReceive']);
        $this->task_server->on('close',[$this,'onTaskClose']);
       //普通的预约服务专用
        $this->ws->on("start", [$this, 'onStart']);
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on("workerstart", [$this, 'onWorkerStart']);
        $this->ws->on("request", [$this, 'onRequest']);
        $this->ws->on("connect", [$this, 'onConnect']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this, 'onClose']);
        $this->ws->start();
    }

    /**
     * 监听task机制的消息
     * @param $serv
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function onTaskReceive($serv, $fd, $from_id, $data)
    {
        //接受任务参数
        $receive = json_decode($data,true);
        //要实例化的任务类
        $class = '';
        //初始化任务类
        switch ($receive['task'])
        {
            case 'Finance':
                $class = $this->swooleTaskPath.$receive['task'].'Task';
                break;
            default:
                return [
                    'type'   => 'undefided',
                    'status' => false
                ];
        }
        //调用任务类
        call_user_func_array(
            array(new $class(),'run'),
            array($serv,$receive['data'])
        );
    }

    /**
    监听task机制关闭事件的回调
     */
    public function onTaskclose($ser, $fd)
    {
        print_r("你好,我的{$fd}\n");
    }

    public function initRedisData(){
        $redis = new \Redis();
        $redis->connect("127.0.0.1", 6379);
        $res = $redis->sMembers(self::CLIENT_ID);

        if(!empty($res)){
            foreach($res as $key=>$value){
                $redis->sRem(self::CLIENT_ID,$value);
            }
        }

        $invalid_locks = $redis->keys('lock-*');
        if(!empty($invalid_locks)){
            foreach($invalid_locks as $v){
                $redis->del($v);
            }
        }
        $redis->close();
    }

    /**
     * @param $server
     */

    public function onStart($server){
        swoole_set_process_name("live_master");
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server,$worker_id){
        define('APP_PATH', __DIR__ . '/../../../application/');
        require __DIR__ . '/../../../thinkphp/start.php';
//        require __DIR__ . '/../../../thinkphp/base.php';
        $type = $server->taskworker ? 'Tasker':'Worker';
        swoole_set_process_name('APPOINT_'.$type.'_'.$worker_id);
        $GLOBALS['ws'] = $server;

        if($type == 'Worker'){
            if($worker_id == 0){
                \app\common\service\Predis::getInstance()->redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
                \app\common\service\Predis::getInstance()->redis->psubscribe(array('__keyevent@0__:expired'),'psCallback');
            }
        }
    }

    /**
     * @param $request
     * @param $response
     */
    public function onRequest($request,$response){
        if($request->server['request_uri'] == '/favicon.ico') {
            $response->status(404);
            $response->end();
            return ;
        }

        $_SERVER  =  [];
        if(isset($request->server)) {
            foreach($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        if(isset($request->header)) {
            foreach($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        $_GET = [];
        if(isset($request->get)) {
            foreach($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        $_FILES = [];
        if(isset($request->files)) {
            foreach($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }
        $_POST = [];
        if(isset($request->post)) {
            foreach($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

        //$this->writeLog();
        $_POST['http_server'] = $this->ws;

        ob_start();
        // 执行应用并响应
        try {
            think\App::run()->send();
        }catch (\Exception $e) {
            // todo
        }

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }


    /**
     * @param $server
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($server,$taskId,$workerId,$data){
        if(isset($data['cmd'])){//专门针对的是预约的任务
            //应该分门别类  这里只是一个中转站
            $obj = new app\common\service\AppointTask;
            $method = $data['cmd'];
            $flag = $obj->$method($data, $server);
            return $flag;
        }else{
            //正常的任务投递机制
            //实例化任务逻辑类
            $obj = new $data['class'];
            $action = isset($data['action']) ? $data['action'] : '';
            $params = (isset($data['params']) && $data['params'] != '') ? $data['params'] : '';

            $flag = $obj->$action($params);
            return $flag;

            //其实这个地方也可以用call_user_func_array 下面是个例子
//            $className = $data['class'];
//            $action    = $data['action'];
//            return call_user_func_array(array(
//                new $className(),
//                $action
//            ), array($data['datas']));

        }

    }

    /**
     * @param $server
     * @param $taskId
     * @param $data
     */
    public function onFinish($server,$taskId,$data){
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    public function onConnect($server,$fd,$id){
        //var_dump($server);
    }
    /**
     * listen ws connect
     * @param $ws
     * @param $request
     */
    public function onOpen($ws,$request){
        //every connect
        \app\common\service\Predis::getInstance()->redis->sAdd("client_id",$request->fd);
        //every send mesage about the appoint parameters
//        $appoint_ctrl = new \app\index\controller\Appoint();
//        $data = [
//            'dayDetail'=>$appoint_ctrl->getDayDetail(3)
//        ];
//        //get the tempappoint message and the
//        $return = \app\common\service\Util::showMsg(\app\common\enum\SocketOper::INIT_APPOINT,$data);
//        $res = $ws->push($request->fd,json_encode($return));

    }

    /**
     * listen ws message,from client
     * @param $ws
     * @param $request
     */
    public function onMessage($ws,$frame){
        //message from client
        $data = json_decode($frame->data,true);
        $redis = \app\common\service\Predis::getInstance()->redis;
        switch($data['oper']){
            case \app\common\enum\SocketOper::GET_APPOINT:
                $keyTemp = 'keytemp';
                $offical_key = 'trukey';
                $appoint_ctrl = new \app\index\controller\Appoint();
                $data = [
                    'dayDetail'=>$appoint_ctrl->getDayDetail(3)
                ];
                //GET ALL TEMP KEY
                $tempAppoints = $redis->keys($keyTemp.'*');
                //GET ALL TRUE KEY
                $trueAppoints = $redis->keys($offical_key.'*');
//                $trueAppoints = [
//                    'trukey|2018-12-30 10:00|2|1',
//                    'trukey|2018-12-30 14:30|1|1'
//                ];
                $return = [
                    'trueAppoints' => $trueAppoints,
                    'tempAppoints' => $tempAppoints,
                    'code' => \app\common\enum\SocketOper::INIT_APPOINT,
                    'data'=>$data
                ];
                $ws->push($frame->fd,json_encode($return));
                break;
            break;
            case \app\common\enum\SocketOper::SUBMIT_TEMP_APPOINT:
                  $key = 'keytemp|'.$data['date'];
                  $offical_key = 'trukey|'.$data['date'];
                  $appoint_num = $data['num'];
                  $limited_num = $data['limited_num'];
                  $lockKey = 'loc-'.$key;
                  $expireTime = 20;
                  $last_stat = 1;
                  $status = true;
                  //set the redis lock
                  while($status)
                  {
                      $result = $redis->setnx($lockKey, time()+$expireTime);
                      if(!empty($result)||($redis->get($lockKey)<time() && $redis->getset($lockKey, time()+$expireTime) < time())){
                          $redis->expire($lockKey, $expireTime);
                          $status = false;
                      }else{
                          sleep(2);
                      }
                  }
                    //do the logic
                    //first,we should check the totoal appointed number include the temp appoint and the offical appoint
                    $limited_keys = array_merge($redis->keys($key.'*'),$redis->keys($offical_key.'*'));
                    $total_num = 0;
                    if(!empty($limited_keys)){
                        foreach($limited_keys as $value){
                            $total_num += intval($redis->get($value));
                        }
                    }
                    $last_num = $limited_num - $total_num;
                    //the last num is not enough
                    if($last_num < $appoint_num){
                        $last_stat = 0;
                    }else{  //begin to occupy the rooms
                        $key = $key."|".$data['uid']."|".$appoint_num;
                        $redis->setex($key,60,$appoint_num);
                    }
                  //unset the redis lock
                 if($redis->ttl($lockKey)){
                     $redis->del($lockKey);
                 }

                 //begin to return client
                $return = [
                    'code' => \app\common\enum\SocketOper::APPOINTED_TEMP_FAILED,
                    'appointed_num' => $appoint_num,
                    'date' => $data['date'],
                ];

                if($last_stat){
                    $return['code'] = \app\common\enum\SocketOper::APPOINTED_TEMP_SUCCESS;
                    $return['date'] = $data['date'];
                    $return['key'] = $key;
                }
                //here we must also do more classify
               //这里是往前端推送消息
                $ws->push($frame->fd,json_encode($return));
                break;
            default:
                break;
        }

//        $return = [];
//        $ws->push($frame->fd, json_encode($return,true));
    }

    /**
     * @param $ws
     * @param $fd
     */
    public function onClose($ws,$fd){
        \app\common\service\Predis::getInstance()->redis->sRem('client_id',$fd);
        echo "clientid:{$fd}\n";
    }

    public function redisPool($ws,$order){
        if (count($ws->redisPool) == 0) {
            $redis = new Swoole\Coroutine\Redis();
            $res = $redis->connect('127.0.0.1', 6379);
            $ws->redisPool->enqueue($redis);
        }
        $ws->redisPool->dequeue()->$order();
    }
}

new Ws();

function psCallback($redis, $pattern, $chan, $msg){
     //first three elem compare
    switch(substr($msg,0,3)){
        case 'key':
            $infoList = explode("|",$msg);
            $return = [
                'cmd'=> 'pushExpired',
                'date' => $infoList[1],
                'expired_appointed_num'=>$infoList[3],
                'uid'=>$infoList[2]
            ];
            break;
        case 'tru':
            $infoList = explode("|",$msg);
            $return = [
                'cmd' => 'deleteAppoint',
                'appoint_key' => $msg
            ];
            break;
        default:
            return;
    }
    $GLOBALS['ws']->task($return);
}
