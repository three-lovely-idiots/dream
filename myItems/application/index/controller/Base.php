<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 12/21/18
 * Time: 3:55 AM
 */

namespace app\index\controller;


use think\Cache;
use think\Controller;
use app\common\service\Token;

class Base extends Controller
{
    const SUCCESS_STATUS = 1;
    const FAIL_STATUS = 0;

    public function _initialize()
    {
        if($this->request->pathinfo() == 'index/categories'){ //判断是类目的pathinfo
            if(($result = Cache::store('redis')->get('categories'))){ //如果能取到缓存
                json(json_decode($result))->send();
                exit;
            }
        }

    }
    protected function checkPrimaryScope(){
        Token::needCheckPrimaryScope();
    }

    protected function checkExclusiveScope(){
        Token::needCheckExclusiveScope();
    }
}