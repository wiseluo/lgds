<?php

namespace app\api\service;

use think\Cache;
use app\api\common\SmsTool;

class SmsService
{
    public function smsCodeService($phone)
    {
        $smscode = Cache::store('file')->get('sms_'. $phone);
        if($smscode) {
            if(time() - $smscode['time'] < 60) {
                return ['status'=> 0, 'msg'=> '每1分钟可获取一次验证码，请稍候再来。'];
            }
        }
        // 随机6位数
        $smscode = rand(100000, 999999);
        //变量模板ID
        $template = '380228';
        $content = "【商翔】您好，您的验证码是：". $smscode ."，有效期为5分钟。如非本人操作，请忽略此短信。";
        $res = SmsTool::sendSMS('', '', $phone, $content, $template);
        $res = json_decode($res);
        if ($res->code == 0) {
            $sms_data = [
                'time' => time(),
                'smscode' => (string)$smscode,
            ];
            Cache::store('file')->set('sms_'. $phone, json_encode($sms_data), 3000);
            return ['status'=> 1, 'msg'=> '短信发送成功'];
        } else {
            return ['status'=> 0, 'msg'=> '短信发送失败! 状态：' . $res->message];
        }
    }

    public function verifySms($phone, $smscode)
    {
        $cache_smscode = Cache::store('file')->get('sms_'. $phone);
        if($cache_smscode == null) {
            return ['status'=> 0, 'msg'=> '验证码不存在或已过期，请重新获取'];
        }
        $cache_smscode = json_decode($cache_smscode, true);
        if(!hash_equals($cache_smscode['smscode'], $smscode)) { //可防止时序攻击的字符串比较
            return ['status'=> 0, 'msg'=> '验证码错误'];
        }
        return ['status'=> 1, 'msg'=> 'sms验证成功'];
    }
}