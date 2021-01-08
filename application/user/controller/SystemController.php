<?php
namespace app\user\controller;

use app\common\model\Sysconfig;

class SystemController extends BaseController
{
    public function bankcardqrcode()
    {
        $res = Sysconfig::where(['type'=> 'bankcardqrcode'])->find();
        if ($res) {
            return show(200, '成功', ['bankcardqrcode'=> $res['image']]);
        }else{
            return show(401, '未设置银行卡二维码');
        }
    }

    public function contactInfo()
    {
        $res = Sysconfig::where(['type'=> 'contactinfo'])->find();
        if ($res) {
            return show(200, '成功', ['contactinfo'=> $res['content']]);
        }else{
            return show(401, '未设置联系方式');
        }
    }
}
