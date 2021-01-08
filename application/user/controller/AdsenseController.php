<?php
namespace app\user\controller;

use app\common\model\Adsense;

//广告位
class AdsenseController extends BaseController
{   
    //列表
    public function index()
    {
        $adsense = Adsense::where(['status'=> 1])->find();
        if ($adsense) {
            return show(200, '成功', $adsense);
        }else{
            return show(401, '未设置广告位');
        }
    }

}
