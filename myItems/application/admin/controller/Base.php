<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;


use app\common\enum\AdminoperEnum;
use app\common\service\Util;
use think\Controller;
use app\admin\model\RoleModel;

class Base extends Controller
{
    public static $success_msg = '加载成功';
    public static $fail_msg = '加载失败';

    public function _initialize()
    {
        if(empty(session('username')) || empty(session('id'))){

            $loginUrl = url('login/index');
            if(request()->isAjax()){
                return msg(111, $loginUrl, '登录超时');
            }

            $this->redirect($loginUrl);
        }

        // 检查缓存
        $this->cacheCheck();
        // 检测权限
        $controller = lcfirst(request()->controller());
        $action = lcfirst(request()->action());
//
//        if(empty(authCheck($control . '/' . $action))){
//            if(request()->isAjax()){
//                return msg(403, '', '您没有权限');
//            }
//
//            $this->error('403 您没有权限');
//        }

        //每次请求进行验证
        //1. 拼接验证类
        $validate = "\\app\\admin\\validate\\".ucfirst($controller).ucfirst($action);
        //2. 查看验证层次是否存在
        if(class_exists($validate) && request()->isAjax()){
            //3.错误返回
            if(!($val = new $validate)->goCheck())
            {
                //json返回机制  只能用在_initialize里面
                return json(Util::showMsg(AdminoperEnum::PARAM_FAIL,array_values($val->getError())[0]))->send();
            }
        }
        //委派初始化数组到页面
        $this->assign([
            'head'     => session('head'),
            'username' => session('username'),
            'rolename' => session('role'),
            'url' => strtolower(request()->controller()).'/'.request()->action(),
            'ver'=> time()
        ]);
    }

    private function cacheCheck()
    {
       
        $action = cache(session('role_id'));

        if(is_null($action) || empty($action)){

            // 获取该管理员的角色信息
            $roleModel = new RoleModel();
            $info = $roleModel->getRoleInfo(session('role_id'));
            cache(session('role_id'), $info['action']);
        }
    }

    protected function removRoleCache()
    {
        $roleModel = new RoleModel();
        $roleList = $roleModel->getRole();

        foreach ($roleList as $value) {
            cache($value['id'], null);
        }
    }
}
