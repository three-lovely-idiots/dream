<?php
/**
 * 这个类用来生成
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/8 0008
 * Time: 下午 2:40
 */

namespace app\index\controller;

use app\common\enum\StatusEnum;
use app\common\service\Util;
use app\common\validate\ProductPosterValidate;
use app\common\model\Product as ProductModel;
use app\common\service\Token;
use app\common\lib\EasyWechat;
use think\Request;

class ProductPoster extends Base
{
    public function getimage($id)
    {
        //验证产品id是否存在
        (new ProductPosterValidate)->goCheck();

        //通过id找到商品信息
        $goods = ProductModel::getProductDetail($id);

        if (empty($goods))
        {
            return Util::showMsg(StatusEnum::FAIL,'商品未找到');
        }

        //来看看member是什么 当前请求会员的信息 之前授权的时候存入的信息
        $member = Token::getCurrentVarsByToken();
        if (empty($member))
        {
            $member = array();
        }
        //创建图片
        $imgurl = $this->createPoster($goods, $member);

        if (empty($imgurl))
        {
            return Util::showMsg(StatusEnum::FAIL,'海报生成失败');
        }
        return Util::showMsg(StatusEnum::SUCCESS,array('url' => $imgurl));
    }

    private function createPoster($goods = array(), $member = array())
    {
        set_time_limit(0);
        @ini_set('memory_limit', '256M');
        $path = IMAGE_PATH.'goods/' . $member['id'] . '/';
        if (!(is_dir($path)))
        {
           mkdir($path,777);
        }

        $md5 = md5(json_encode(array( 'openid' => $member['openid'], 'goodstitle' => $goods['name'], 'price' => $goods['price'])));
        $filename = $md5 . '.png';
        $filepath = $path . $filename;

        //如果有这张图片那么就直接返回
//		if (is_file($filepath))
//		{
//			return $this->getImgUrl($filename);
//		}
        $target = imagecreatetruecolor(750, 1127);
        $white = imagecolorallocate($target, 255, 255, 255);
        imagefill($target, 0, 0, $white);

        //画出商品的缩略图
        if (!(empty($goods['img']['thumb_url'])))
        {
            $thumb = $this->createImage($goods['img']['thumb_url']);
            imagecopyresized($target, $thumb, 30, 124, 0, 0, 690, 690, imagesx($thumb), imagesy($thumb));
        }

        //加载字体文件
        $font = ROOT_PATH.'public/static/fonts/pingfang.ttf';

        if (!(is_file($font)))
        {
            $font = ROOT_PATH.'public/static/fonts/msyh.ttf';
        }

        $avatartarget = imagecreatetruecolor(70, 70);
        $avatarwhite = imagecolorallocate($avatartarget, 255, 255, 255);
        //会员头像 其实就是微信头像
        imagefill($avatartarget, 0, 0, $avatarwhite);
        $avatar = $member['avatarUrl'];
        $image = $this->mergeImage($avatartarget, array('type' => 'avatar', 'style' => 'circle'), $avatar);

        imagecopyresized($target, $image, 32, 30, 0, 0, 70, 70, 70, 70);
        $name = $this->memberName($member['nickName']);

        $nameColor = imagecolorallocate($target, 82, 134, 207);
        imagettftext($target, 26, 0, 126, 80, $nameColor, $font, $name);
        $shareColor = imagecolorallocate($target, 56, 56, 56);
        $textbox = imagettfbbox(26, 0, $font, $name);
        $textwidth = (136 + $textbox[4]) - $textbox[6];
        imagettftext($target, 26, 0, $textwidth, 80, $shareColor, $font, '分享给你一个商品');
        $pricecolor = imagecolorallocate($target, 248, 88, 77);
        imagettftext($target, 52, 0, 56, 1016, $pricecolor, $font, $goods['discount_price']);
        imagettftext($target, 26, 0, 30, 1016, $pricecolor, $font, '￥');
        $titles = $this->getGoodsTitles($goods['name'], 28, $font, 690);
        $black = imagecolorallocate($target, 0, 0, 0);


        imagettftext($target, 28, 0, 30, 872, $black, $font, $titles[0]);
        imagettftext($target, 28, 0, 30, 922, $black, $font, $titles[1]);
        $boxstr = file_get_contents(ROOT_PATH.'public/images/poster/goodsbox.png');
        $box = imagecreatefromstring($boxstr);
        imagecopyresampled($target, $box, 546, 934, 0, 0, 150, 150, 176, 176);
        //创建二维码 这里出错了 这个在plugin/app/core/model.php里面
         $easyWechat = new EasyWechat();
         $qrcode = $easyWechat->getCodeUnlimit(array('scene' => 'id=' . $goods['id'] . '&mid=' . $member['id'], 'page' => 'pages/product/product'))->getBody()->getContents();

        if (!empty($qrcode))
        {
            $qrcode = imagecreatefromstring($qrcode);
            imagecopyresized($target, $qrcode, 546, 934, 0, 0, 150, 150, imagesx($qrcode), imagesy($qrcode));
        }
        $gary2 = imagecolorallocate($target, 152, 152, 152);
        imagettftext($target, 24, 0, 30, 1070, $gary2, $font, '长按识别小程序码访问');
        imagepng($target, $filepath);
        imagedestroy($target);
        return $this->getImgUrl($filename,$member['id']);
    }

    private function getImgUrl($filename,$member_id)
    {

        return  config('img_perfix'). '/goods/' . $member_id . '/'.$filename . '?v=1.0';
    }
    private function getGoodsTitles($text, $fontsize = 30, $font = '', $width = 100)
    {
        $titles = array('', '');
        $textLen = mb_strlen($text, 'UTF8');
        $textWidth = imagettfbbox($fontsize, 0, $font, $text);
        $textWidth = $textWidth[4] - $textWidth[6];
        if ((19 < $textLen) && ($width < $textWidth))
        {
            $titleLen1 = 19;
            $i = 19;
            while ($i <= $textLen)
            {
                $titleText1 = mb_substr($text, 0, $i, 'UTF8');
                $titleWidth1 = imagettfbbox($fontsize, 0, $font, $titleText1);
                if ($width < ($titleWidth1[4] - $titleWidth1[6]))
                {
                    $titleLen1 = $i - 1;
                    break;
                }
                ++$i;
            }
            $titles[0] = mb_substr($text, 0, $titleLen1, 'UTF8');
            $titleLen2 = 19;
            $i = 19;
            while ($i <= $textLen)
            {
                $titleText2 = mb_substr($text, $titleLen1, $i, 'UTF8');
                $titleWidth2 = imagettfbbox($fontsize, 0, $font, $titleText2);
                if ($width < ($titleWidth2[4] - $titleWidth2[6]))
                {
                    $titleLen2 = $i - 1;
                    break;
                }
                ++$i;
            }
            $titles[1] = mb_substr($text, $titleLen1, $titleLen2, 'UTF8');
            if (($titleLen1 + $titleLen2) < $textLen)
            {
                $titles[1] = mb_substr($titles[1], 0, $titleLen2 - 1, 'UTF8');
                $titles[1] .= '...';
            }
        }
        else
        {
            $titles[0] = $text;
        }
        return $titles;
    }
    /**
     * 画出商品缩略图
     * @param $imgurl
     * @return resource|strin
     */
    private function createImage($imgurl)
    {
        if (empty($imgurl))
        {
            return '';
        }


        return imagecreatefromstring(curl_get($imgurl));
    }

    private function memberName($text)
    {
        $textLen = mb_strlen($text, 'UTF8');
        if (5 <= $textLen)
        {
            $text = mb_substr($text, 0, 5, 'utf-8') . '...';
        }
        return $text;
    }

    private function imageZoom($image = false, $zoom = 2)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $target = imagecreatetruecolor($width * $zoom, $height * $zoom);
        imagecopyresampled($target, $image, 0, 0, 0, 0, $width * $zoom, $height * $zoom, $width, $height);
        imagedestroy($image);
        return $target;
    }
    private function imageRadius($target = false, $circle = false)
    {
        $w = imagesx($target);
        $h = imagesy($target);
        $w = min($w, $h);
        $h = $w;
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        $radius = (($circle ? $w / 2 : 20));
        $r = $radius;
        $x = 0;
        while ($x < $w)
        {
            $y = 0;
            while ($y < $h)
            {
                $rgbColor = imagecolorat($target, $x, $y);
                if ((($radius <= $x) && ($x <= $w - $radius)) || (($radius <= $y) && ($y <= $h - $radius)))
                {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                else
                {
                    $y_x = $r;
                    $y_y = $r;
                    if (((($x - $y_x) * ($x - $y_x)) + (($y - $y_y) * ($y - $y_y))) <= $r * $r)
                    {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    $y_x = $w - $r;
                    $y_y = $r;
                    if (((($x - $y_x) * ($x - $y_x)) + (($y - $y_y) * ($y - $y_y))) <= $r * $r)
                    {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    $y_x = $r;
                    $y_y = $h - $r;
                    if (((($x - $y_x) * ($x - $y_x)) + (($y - $y_y) * ($y - $y_y))) <= $r * $r)
                    {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    $y_x = $w - $r;
                    $y_y = $h - $r;
                    if (((($x - $y_x) * ($x - $y_x)) + (($y - $y_y) * ($y - $y_y))) <= $r * $r)
                    {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                }
                ++$y;
            }
            ++$x;
        }
        return $img;
    }
    private function mergeImage($target = false, $data = array(), $imgurl = '', $local = false)
    {
        if (empty($data) || empty($imgurl))
        {
            return $target;
        }
        if (!($local))
        {
            $image = $this->createImage($imgurl);
        }
        else
        {
            $image = imagecreatefromstring($imgurl);
        }
        $sizes = $sizes_default = array('width' => imagesx($image), 'height' => imagesy($image));
        $sizes = array('width' => 70, 'height' => 70);
        if (($data['style'] == 'radius') || ($data['style'] == 'circle'))
        {
            $image = $this->imageZoom($image, 4);
            $image = $this->imageRadius($image, $data['style'] == 'circle');
            $sizes_default = array('width' => $sizes_default['width'] * 4, 'height' => $sizes_default['height'] * 4);
        }
        imagecopyresampled($target, $image, intval(isset($data['left']) ? $data['left'] : 0) * 2, intval(isset($data['top']) ? $data['top'] : 0) * 2, 0, 0, $sizes['width'], $sizes['height'], $sizes_default['width'], $sizes_default['height']);
        imagedestroy($image);
        return $target;
    }
}
