<?php
namespace app\user\controller;

use think\Request;
use think\Controller;
use app\user\model\User;
use app\teacher\model\Teacher;
use app\user\common\UserJwtTool;
use app\teacher\common\TeacherJwtTool;
use app\common\behavior\WechatAppletTool;

class LoginController extends Controller
{
    //登录
    public function appletLogin(Request $request)
    {
        $param = $request->param();
        $validate = validate('LoginValidate');
        if(!$validate->scene('appletLogin')->check($param)) {
            return json(['code'=> 401, 'msg'=> $validate->getError()]);
        }
        // $user = User::get(57);
        // $token = UserJwtTool::setAccessToken($user);
        // return show(200, '登录成功', ['token'=> $token, 'type'=> 0]);
        
        $wechatAppletTool = new WechatAppletTool();
        $info_res = $wechatAppletTool->getJscode2sessionWxApi($param['code']);
        if($info_res['status']) {
            $openid_wx = $info_res['data']['openid'];
            //先登录教师
            $token_res = $this->getTeacherToken($openid_wx);
            if($token_res['status'] == 1) { //教师
                return show(200, '登录成功', ['token'=> $token_res['token'], 'type'=> 1, 'state'=> $token_res['state']]);
            }
            $token_res = $this->getUserToken($openid_wx);
            if($token_res['status'] == 1) { //学生
                return show(200, '登录成功', ['token'=> $token_res['token'], 'type'=> 0, 'state'=> $token_res['state']]);
            }
            return show(200, '请注册认证', ['token'=> '', 'type'=> 0, 'openid'=> $openid_wx]);
        }else{
            return show(401, $info_res['msg']);
        }
    }

    public function getTeacherToken($openid_wx)
    {
        $teacher = Teacher::where(['openid_wx'=> $openid_wx])->find();
        if($teacher) {
            $token = TeacherJwtTool::setAccessToken($teacher);
            return ['status'=> 1, 'token'=> $token, 'state'=> $teacher['status']];
        }else{
            return ['status'=> 0, 'token'=> ''];
        }
    }

    public function getUserToken($openid_wx)
    {
        $user = User::where(['openid_wx'=> $openid_wx])->find();
        if($user) {
            $token = UserJwtTool::setAccessToken($user);
            return ['status'=> 1, 'token'=> $token, 'state'=> $user['status']];
        }else{
            return ['status'=> 0, 'token'=> ''];
        }
    }

    //绑定注册，默认学员
    public function appletRegister(Request $request)
    {
        $param = $request->param();
        $validate = validate('LoginValidate');
        if(!$validate->scene('appletRegister')->check($param)) {
            return json(['code'=> 401, 'msg'=> $validate->getError()]);
        }
        if($param['openid'] == 'undefined') {
            return show(401, '微信标识错误');
        }
        $user = User::where(['openid_wx'=> $param['openid']])->find();
        if($user) {
            return show(401, '微信已存在，不能重复注册');
        }
        $user_data = [
            'openid_wx' => $param['openid'],
            'nickname'=> $param['nickname'],
            'avatar'=> $param['avatar'],
            'gender'=> $param['gender'],
        ];
        $user = new User($user_data);
        $res = $user->allowField(true)->save();
        if($res) {
            $user_data['id'] = $user['id'];
            $token = UserJwtTool::setAccessToken($user_data);
            return show(200, '注册成功', ['token'=> $token, 'type'=> 0]);
        }else{
            return show(401, '绑定注册失败');
        }
    }
}
