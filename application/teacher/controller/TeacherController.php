<?php
namespace app\teacher\controller;

use think\Request;
use app\teacher\model\Teacher;

class TeacherController extends BaseController
{
    public function info(Request $request)
    {
        $teacher = Teacher::get(request()->teacher()['id']);
        $data = [
            'id'=> $teacher['id'],
            'nickname'=> $teacher['nickname'],
            'avatar'=> $teacher['avatar'],
            'gender'=> $teacher['gender'],
            'username'=> $teacher['username'],
            'phone'=> $teacher['phone'],
            'identity_card'=> $teacher['identity_card'],
            'work_unit'=> $teacher['work_unit'],
        ];
        return show(200, '成功', $data);
    }

    public function authentication(Request $request)
    {
        $param = $request->param();
        $validate = validate('TeacherValidate');
        if(!$validate->scene('authentication')->check($param)) {
            return json(['code'=> 401, 'msg'=> $validate->getError()]);
        }
        $teacher_id = request()->teacher()['id'];
        $teacher = Teacher::get($teacher_id);
        if($teacher['status'] == 1) {
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
        $teacher = new Teacher();
        $res = $teacher->allowField(true)->save($data, ['id'=> request()->teacher()['id']]);
        if($res) {
            return show(200, '成功');
        }else{
            return show(401, '失败');
        }
    }

    public function update(Request $request)
    {
        $param = $request->param();
        $validate = validate('TeacherValidate');
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
        $teacher = new Teacher();
        $res = $teacher->allowField(true)->save($data, ['id'=> request()->teacher()['id']]);
        if($res) {
            return show(200, '成功');
        }else{
            return show(401, '失败');
        }
    }
}
