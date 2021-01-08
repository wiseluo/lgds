<?php

namespace app\common\behavior;

use Curl\Curl;

class SmsTool
{
    private $message_account = ''; //短信账号
    private $message_pwd = ''; //短信密码
    //短信模板-报名成功短信
    public $message_template_enroll_success = '亲爱的学员：您在总工会举办的职工公益课堂报名已成功。请您用微信扫一扫课程群二维码加入课程大家庭。'; 
    //短信模板-学员上课提醒
    public $message_template_class_reminder = '亲爱的学员：您在总工会举办的职工公益课堂报名的（课程名称）课程将于明天（datetime）准时上课，请您合理安排工作务必参加。此次课程上课次数class_number次，您已签到sign_number次，未签到unsigned_number次。具体信息请查看工会课堂微信小程序“我的课程”。'; 

    public function __construct()
    {
        $this->message_account = config('sms.message_account');
        $this->message_pwd = config('sms.message_pwd');
    }

    public function sendSms($mobiles = '', $content = '')
    {
        if(empty($this->message_account) || empty($this->message_pwd)) {
            return ['status'=> 0, 'msg'=> '短信账号密码未配置'];
        }
        $url = 'http://112.35.1.155:1992/sms/norsubmit';
        $ecName = "义乌市数据管理中心";
        $apId = $this->message_account;
        $secretKey = $this->message_pwd;
        $sign = "wJcInYaXr";
        $addSerial = "";
        $mac = $ecName.$apId.$secretKey.$mobiles.$content.$sign.$addSerial;
        $md5_mac = strtolower(md5($mac));

        $curl = new Curl();
        $message_data = [
            'ecName' => $ecName,
            'apId' => $apId,
            'secretKey' => $secretKey,
            'mobiles' => $mobiles,
            'content' => $content,
            'sign' => $sign,
            'addSerial' => $addSerial,
            'mac' => $md5_mac,
        ];
        $message_json = json_encode($message_data);
        $base64_json = base64_encode($message_json);
        $curl->post($url, $base64_json);
        if($curl->error) {
            return ['status'=> $curl->error_code, 'msg'=> $curl->errorMessage];
        }else{
            $res = $curl->response;
            $result = json_decode($res, true);
        }
        $curl->close();
        if($result['success'] == true) {
            return ['status'=> 200, 'msg'=> $result['rspcod'], 'data'=> $result['msgGroup']];
        }else{
            return ['status'=> 400, 'msg'=> $result['rspcod']];
        }
    }
    
}
