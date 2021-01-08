<?php

namespace app\common\behavior;

use Endroid\QrCode\QrCode;

class QrcodeTool
{
    public function qrcode($url)
    {
        // $content = common_func_domain(). $url;  //to do 去掉注释
        $content = 'https://www.yiwu8.com'. $url; //to do 注释
        $qrCode = new QrCode($content);
        // 输出二维码
        // header('Content-Type: '. $qrCode->getContentType());
        // echo $qrCode->writeString();

        $ym = date('Ym', time());
        $qrcode_path = ROOT_PATH . 'public'. DS .'qrcode'. DS . $ym;
        if (!file_exists($qrcode_path)) {
            mkdir($qrcode_path, 0777, true); //创建多级目录
        }
        $filename = md5(uniqid(md5(microtime(true)),true)) .'.png';
        $qrCode->writeFile($qrcode_path .'/'. $filename);
        return '/qrcode/'. $ym .'/'. $filename;
    }
    
}
