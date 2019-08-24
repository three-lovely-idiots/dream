<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//这个是新型的登录方式
Route::rule('index/token/login','index/Token/login','POST');
Route::rule('index/token/user','index/Token/getToken','POST');
Route::rule('index/token/verify','index/Token/verifyToken','POST');

Route::get('index/getStyles','index/Search/getAllStyles');
//搜索美图
Route::post('index/searchLike','index/Search/searchSelectedImg');

//获取图片本身信息  以及相关8张图片
Route::post('index/getCurrent','index/GetLike/getCurrentImage');

Route::post('index/getRelated','index/GetLike/getRelateImage');

//产品类目
Route::get('index/categories','index/Category/getCategories');
Route::get('index/product/by_categories','index/Product/getAllProductsByCategories');
//产品内页
Route::post('index/product','index/Product/getProductDetail');
//产品推广图片
Route::post('index/posterImage','index/ProductPoster/getimage');


//主页
Route::get('index/product/recent','index/Product/getRecentProducts');
Route::get('index/banner/:id','index/Banner/getBanner'); //主页banner
Route::get('index/theme','index/Theme/getThemeLists'); //主页theme
//主题单页
Route::get('index/theme/:id/:type','index/Theme/getSingleThemeInfo');

//个人主页
//1 更新个人信息
Route::post('index/user/wx_info','index/User/userInfo');
//2 签到信息
Route::post('index/sign','index/User/sign');
//3 主页初始化信息
Route::post('index/my','index/User/my');

/**
 * 订单相关
 */
//下单的api 存入数据库order与orderproduct
Route::post('index/order','index/Order/placeOrder');
//生成预定单并且开始支付
Route::post('index/pay/pre_order','index/Pay/getPreOrder');
Route::post('index/pay/cancel','index/Pay/cancelPayment');
//获取订单列表
Route::get('index/order/list','index/Order/orderList');
//获取单个订单信息 根据id
Route::get('index/order/:id','index/Order/getOrderByID',[],['id'=>'^\\d+$']);


//收藏接口
//收藏动作 产品收藏 图片收藏
Route::post('index/collect','index/Mycollect/collectLike');
Route::post('index/getCollect','index/Mycollect/getUserCollect');
Route::post('index/cancelCollect','index/Mycollect/cancelCollect');

