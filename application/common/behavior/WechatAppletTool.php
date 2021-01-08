<?php

namespace app\common\behavior;

use Curl\Curl;
use app\api\common\WxBizDataCrypt;

class WechatAppletTool
{
    private $appid = ''; //小程序唯一标识
    private $app_secret = ''; //小程序密钥

    public function __construct()
    {
        $this->appid = config('wechat.applet_app_id');
        $this->app_secret = config('wechat.applet_app_secret');
    }

    /**
      * return jsonObj: openid session_key unionid
    **/
    public function getJscode2sessionWxApi($code)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='. $this->appid .'&secret='. $this->app_secret .'&js_code='. $code .'&grant_type=authorization_code';
        $curl = new Curl();
        $curl->get($url);
        if($curl->error) {
            return ['status'=> 0, 'msg'=> '微信获取授权失败'];
        }else{
            $result = json_decode($curl->response, true);
        }
        if(isset($result['errcode'])) {
            return ['status'=> 0, 'msg'=> '微信获取授权失败-'. $result['errmsg']];
        }else{
            return ['status'=> 1, 'msg'=> '成功', 'data'=> $result];
        }
    }

}