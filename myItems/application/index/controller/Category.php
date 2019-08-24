<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 3/28/19
 * Time: 10:22 PM
 */

namespace app\index\controller;

use app\common\exception\MissException;
use app\common\model\Category as CategoryModel;
use think\Cache;

class Category extends Base
{
    public function getCategories(){
        //拦截器思想
        $result = CategoryModel::getCategories();
        if(empty($result)){
            throw new MissException([
                'msg' => '还没有任何类目',
            ]);
        }
        return $result;
    }
}