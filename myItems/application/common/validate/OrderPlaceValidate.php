<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12
 * Time: 16:05
 */

namespace app\common\validate;


use app\common\exception\ParameterException;

class OrderPlaceValidate extends BaseValidate
{
//     protected $products = [
//         [
//             'product_id' => 3,
//             'count'=>5
//         ],
//         [
//             'product_id' => 3,
//             'count'=>5
//         ],
//         [
//             'product_id' => 3,
//             'count'=>5
//         ]
//     ];
        protected $rule = [
            'products'=>'checkProducts'
        ];

       protected  $singleRule = [
           'product_id' => 'require|IsPositiveInteger',
           'count' => 'require|IsPositiveInteger'
       ];

     protected function checkProducts($values,$data){
            if(empty($values)){
                 throw new ParameterException([
                     'msg' => '商品列表不能为空'
                 ]);
            }

            if(!is_array($values)){
                throw new ParameterException([
                    'msg' => '商品参数不正确'
                ]);
            }

           foreach($values as $value){
               $this->checkProduct($value);
           }
         return true;
     }

    protected function checkProduct($value){
        $validate = new BaseValidate($this->singleRule);
        $result = $validate ->check($value);

        if(!$result){
            throw new ParameterException([
                'msg' => '商品列表参数不正确'
            ]);
        }
    }
}