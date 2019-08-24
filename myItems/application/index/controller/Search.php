<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/16/19
 * Time: 3:51 AM
 */

namespace app\index\controller;


use app\common\enum\StatusEnum;
use app\common\exception\SearchException;
use app\common\model\NailMainStyles;
use app\common\model\SelectedImage;
use app\common\service\CoreSeek;
use app\common\service\Util;
use app\common\exception\MissException;
use app\common\validate\SearchValidate;
use think\Cache;
use think\Exception;

class Search extends Base
{
    //这里是获取所有的style 数据是固定的所以可以放到redis里面
    public function getAllStyles(){
        $data = Cache::store('redis')->get('styles');
        if(!$data){
            $data = NailMainStyles::getAllStyles();
            if($data->isEmpty()){
                throw new MissException([
                    'msg' => '获取风格分类有误'
                ]);
            }

            Cache::store('redis')->set('styles',json_encode($data),3600);
        }
        return json_decode($data);
    }

    public function searchSelectedImg($length = '',$pageNO = '',$total='',$searchword=NULL){
        (new SearchValidate())->goCheck();
        if(!$searchword){ //普通搜索 无keyword
            //缓存规律  search:[pageNo]
            $res = Cache::store('redis')->get('search:'.$pageNO); //获取缓存数据
            $total = Cache::store('redis')->get('total');
            if(!$res){
                $res = SelectedImage::getSelectedImgPagnation();
                if($total == 0){
                    $total = count(SelectedImage::all());
                }
                Cache::store('redis')->set('search:'.$pageNO,json_encode($res),3600);
                Cache::store('redis')->set('total',$total,3600);
            }else{
                $res = json_decode($res); //因为是自己转换的json 取出来的就是json字符串 这里需要自己转换一下
            }

        }else{ // 有搜索词 这里先不使用缓存吧
            try{
                //单利模式只有在常驻内存的php框架比如swoole里面才能体现真正的性能
                $coreseek = CoreSeek::getInstance();
                //分页处理
                $offset = $length * ($pageNO - 1);
                $res = $coreseek->query($searchword,$offset,$length,'myindex');
                if(!$res){
                    throw new MissException([
                        'code' => 200,
                        'msg' => '没有你想要的啦～'
                    ]);
                }

                if(!isset($res['matches'])){ //不存在索引的话
                    throw new MissException([
                        'code' => 200,
                        'msg' => '没有你想要的啦～'
                    ]);
                }
                $total = $coreseek->getTotal();
                $res = SelectedImage::getFilterImages($res);
            }catch (\Exception $e){
                throw new SearchException([
                    'msg'=>$e->getMessage().'---'.$e->getFile().'---'.$e->getLine()
                ]);
            }
        }

        if(!$res){
            throw new MissException([
                'code' => 404,
                'msg' => '没有你想要的啦～'
            ]);
        }
        return ['data'=>$res,'total'=>$total];
    }

}