<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6 0006
 * Time: 下午 3:09
 */

namespace app\index\controller;

use app\common\exception\MissException;
use app\common\validate\IDMustBePositiveInt;
use app\common\model\Banner as BannerModel;
class Banner extends Base
{
    public function getBanner($id){
        $validate = new IDMustBePositiveInt();
        $result = $validate->goCheck();

        $bannerInfo = BannerModel::getBannerById($id);
        if($bannerInfo->isEmpty()){
            throw new MissException([
                'msg'=>"banner不存在"
            ]);
        }
        return $bannerInfo;
    }
}