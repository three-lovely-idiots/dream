<?php
namespace app\common\exception;
use think\exception\Handle;
use think\Exception;
use think\Log;
use think\Request;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/30
 * Time: 16:32
 */
class ExceptionHandle extends Handle
{
     private $code;
    private $errorCode;
    private $msg;
     public function render(\Exception $e)
     {
         if($e instanceof BaseException){
             $this->code = $e->code;
             $this->errorCode = $e->errorCode;
             $this->msg = $e->msg;

         }else{

             if(config('app_debug')){
                 return parent::render($e);
             }else{
                 $this->code = 500;
                 $this->errorCode = 999;
                 $this->msg = "服务器错误，不告诉你";
                 //log记录错误
                 $this->recordErrorLog($e);
             }

         }
          $request = Request::instance();
          $result = [
              'errorCode'=>$this->errorCode,
              'msg'=>$this->msg,
              'request_url'=>$request->url()
          ];
         return json($result,$this->code);
     }

       private function recordErrorLog(\Exception $e){
           Log::init([
               'type'=>'File',
               'path'=>LOG_PATH,
               'level'=>['error']
           ]);
           Log::record($e->getMessage(),'error');
       }
}