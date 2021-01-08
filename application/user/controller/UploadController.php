<?php

namespace app\user\controller;

use think\Image;
use think\Request;
use think\Controller;

class UploadController extends BaseController
{
    /**
    * 通用上传图片
    *
    * @param  \think\Request $request
    * @return \think\Response
    */
    public function uploadImage(Request $request) {
        // 获取上传文件
        $file = request()->file('file');
        if (!$file) {
            return json(['code' => 401, 'msg' => '文件必填']);
        }

        $ym = date('Ym', time());
        $upload_path = ROOT_PATH . 'public' . DS . 'uploads/image/' . $ym;
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true); //创建多级目录
        }
        $info = $file->validate(['ext' => 'png,jpg,jpeg,gif,bmp'])->rule('uniqid')->move($upload_path);
        if ($info) {
            // 成功上传后 获取上传信息
            $filename = $info->getFilename();
            $file_path = $upload_path .'/'. $filename;
            $extension = $info->getExtension();
            //dump($file_path);
            $image_thumb = Image::open($file_path);
            $image_thumb->thumb(200, 200)->save($upload_path .'/'. $filename . '.thumb.' . $extension);
            $image_50 = Image::open($file_path);
            $image_50->thumb(50, 50)->save($upload_path .'/'. $filename . '.small.' . $extension);
            return json(['code' => 200, 'msg' => '上传成功', 'data' => '/uploads/image/'. $ym .'/'. $filename]);
        } else {
            // 上传失败获取错误信息
            return json(['code' => 401, 'msg' => $file->getError()]);
        }
    }
}
