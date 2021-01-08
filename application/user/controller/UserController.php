<?php
namespace app\user\controller;

use think\Request;
use app\user\model\User;

class UserController extends BaseController
{
    public function info(Request $request)
    {
        $user = User::get(request()->user()['id']);
        $data = [
            'id'=> $user['id'],
            'nickname'=> $user['nickname'],
            'avatar'=> $user['avatar'],
            'gender'=> $user['gender'],
            'username'=> $user['username'],
            'phone'=> $user['phone'],
            'identity_card'=> $user['identity_card'],
            'work_unit'=> $user['work_unit'],
        ];
        return show(200, '成功', $data);
    }

    public function authentication(Request $request)
    {
        $param = $request->param();
        $validate = validate('UserValidate');
        if(!$validate->scene('authentication')->check($param)) {
            return json(['code'=> 401, 'msg'=> $validate->getError()]);
        }
        $user_id = request()->user()['id'];
        $user = User::get($user_id);
        if($user['status'] == 1) {
            return show(401, '用户已认证');
        }
        $data = [
            'nickname'=> $param['nickname'],
            'gender'=> $param['gender'],
            'username'=> $param['username'],
            'phone'=> $param['phone'],
            'identity_card'=> $param['identity_card'],
            'work_unit'=> $param['work_unit'],
            'status'=> 1,
        ];
        $user_obj = new User();
        $res = $user_obj->allowField(true)->save($data, ['id'=> $user_id]);
        if($res) {
            return show(200, '成功');
        }else{
            return show(401, '失败');
        }
    }

    public function update(Request $request)
    {
        $param = $request->param();
        $validate = validate('UserValidate');
        if(!$validate->scene('update')->check($param)) {
            return json(['code'=> 401, 'msg'=> $validate->getError()]);
        }
        $data = [
            'nickname'=> $param['nickname'],
            'gender'=> $param['gender'],
            'phone'=> $param['phone'],
            'work_unit'=> $param['work_unit'],
            'status'=> 1,
        ];
        $user = new User();
        $res = $user->allowField(true)->save($data, ['id'=> request()->user()['id']]);
        if($res) {
            return show(200, '成功');
        }else{
            return show(401, '失败');
        }
    }
}
