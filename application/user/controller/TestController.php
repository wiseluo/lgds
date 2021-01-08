<?php
namespace app\user\controller;

use think\Controller;
use app\common\behavior\SmsTool;

class TestController extends Controller
{
    public function makeSms()
    {
        $smsTool = new SmsTool();
        return $smsTool->sendSms('13735675918', '亲爱的学员：您在总工会举办的职工公益课堂报名的（课程名称）课程将于明天（2021年1月10日 18:00）准时上课，请您合理安排工作务必参加。此次课程上课次数X次，您已签到X次，未签到X次。具体信息请查看职工公益课堂微信小程序“我的课程”。');
    }
}
