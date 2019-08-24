<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 1/1/19
 * Time: 5:50 AM
 */

namespace app\admin\controller;
use app\common\enum\StatusEnum;
use app\common\service\Util;
use fast\Random;
use think\Config;
use think\File;

class Ajax extends Base
{
    /**
     * 上传文件
     */
    public function upload()
    {
        $thumb_per = 0.3;
        Config::set('default_return_type', 'json');
        $file = $this->request->file('file');
        //取到参数进行 img_cat获取 前端提供img_cat 后台在这里统一设置
        $post_params = $this->request->param();

        if (empty($file)) {
            $this->error('No file upload or server upload limit exceeded');
        }

        //判断是否已经存在附件
        $sha1 = $file->hash();
        $upload = Config::get('upload');
        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int)$upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $mimetypeArr = explode(',', strtolower($upload['mimetype']));
        $typeArr = explode('/', $fileInfo['type']);

        //验证文件后缀
        if ($upload['mimetype'] !== '*' &&
            (
                !in_array($suffix, $mimetypeArr)
                || (stripos($typeArr[0] . '/', $upload['mimetype']) !== false && (!in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr)))
            )
        ) {
            $this->error('Uploaded file format is limited');
        }

        $replaceArr = [
            '{year}'     => date("Y"),
            '{mon}'      => date("m"),
            '{day}'      => date("d"),
            '{hour}'     => date("H"),
            '{min}'      => date("i"),
            '{sec}'      => date("s"),
            '{random}'   => Random::alnum(16),
            '{random32}' => Random::alnum(32),
            '{filename}' => $suffix ? substr($fileInfo['name'], 0, strripos($fileInfo['name'], '.')) : $fileInfo['name'],
            '{suffix}'   => $suffix,
            '{.suffix}'  => $suffix ? '.' . $suffix : '',
            '{filemd5}'  => md5_file($fileInfo['tmp_name']),
        ];
        $savekey = $upload['savekey'];
        $savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //
        $splInfo = $file->validate(['size' => $size])->move(ROOT_PATH . '/public/images' . $uploadDir, $fileName);
        if ($splInfo) {
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }

            $image = \think\Image::open(new File('./images' . $uploadDir.$splInfo->getSaveName()));
            $thumb_path = '/thumb'.$uploadDir;

            $thumb_real_path = './images'.$thumb_path;
            if(!file_exists($thumb_real_path)){ //检测缩略图路径
                mkdir($thumb_real_path,0777,true);
            }

            $thumb_path = $thumb_path.$splInfo->getFilename();
            //开始生成缩略图
            $thumb_width = floor($imagewidth*$thumb_per);
            $thumb_height = floor($imageheight*$thumb_per);
            $image->thumb($thumb_width, $thumb_height)->save($thumb_real_path.$splInfo->getFilename());

            $params = array(
                'admin_id'    => session('id'),
                'thumb_url'  => $thumb_path,
                'url' =>  $savekey,
                'filesize'    => $fileInfo['size'],
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $suffix,
                'img_cat' => isset( $post_params['img_cat']) ?  $post_params['img_cat'] : 0,
                'thumbwidth'  => $thumb_width,
                'thumbheight' => $thumb_height
            );
            $image = model("image");
            $image->data(array_filter($params));
            $image->save();

            \think\Hook::listen("upload_after", $attachment);
            return Util::showMsg(StatusEnum::SUCCESS,['url' => config('img_perfix').$uploadDir . $splInfo->getSaveName(),'id'=>$image->id]);
        } else {
            // 上传失败获取错误信息
            return Util::showMsg(StatusEnum::SUCCESS,['error' => $file->getError()]);
        }
    }
}