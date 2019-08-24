<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/30
 * Time: 15:21
 */

namespace app\common\validate;
use app\common\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
  public function goCheck(){
       $request = Request::instance();
       $param = $request->param();
       $result = $this->batch()->check($param);
       $module = $request->module();
       //前台和后台返回的数据有所不同
       if($module == 'index')
       {
             if(!$result){
                 $e = new ParameterException([
                   'msg' => $this->getError()
                 ]);
                 throw $e;
             }else{
                 return true;
             }
       }else if($module == 'admin'){
           return $result;
       }
  }

    protected function IsPositiveInteger($value,$rule='',$data='',$field=''){

        if(is_numeric($value)&&is_int($value+0)&&($value+0)>0){
            return true;
        }else{
            return false;
        }
    }

    protected function isNotEmpty($value,$rule='',$data='',$field=''){
        if(empty($value)){
            return false;
        }else{
            return true;
        }
    }

    //没有使用TP的正则验证，集中在一处方便以后修改
    //不推荐使用正则，因为复用性太差
    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function getDataByRule($array){

        if(array_key_exists('user_id',$array)||array_key_exists('uid',$array)) {
            throw new ParameterException([
                'msg' => '参数中含有user_id或uid等非法字段'
            ]);
         }
            $newArray = [];

            foreach($this->rule as $key => $value){

                $newArray[$key] = $array[$key];
            }

            return $newArray;
        }

}