<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17 0017
 * Time: 下午 6:17
 */

namespace app\common\enum;


class StatusEnum
{
    const SUCCESS  = 1;
    const FAIL  = -1;

    const APPOINT_INVALID = -99; //预约失效 或者提交预约过程中未知错误都可以
    //签到的状态
    const SIGNED_ALREADY = -9; //已经签到过了

    //非法访问
    const ILLEAG_ACCESS = -13;  // 非法访问

    //产品和图片类型
    const PRODUCT_TYPE = 1;
    const IMAGE_TYPE = 0;
    //收藏重复
    const COLLECT_ALREADY = -9;
}