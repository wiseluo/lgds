<?php
namespace app\teacher\controller;

use app\common\model\Sysconfig;

class SystemController extends BaseController
{
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
