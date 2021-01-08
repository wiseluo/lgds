<?php
namespace app\teacher\controller;

use think\Request;
use think\Controller;
use app\teacher\model\Teacher;
use app\teacher\common\TeacherJwtTool;

class BaseController extends Controller
{
    public function __construct(Request $request)
    {
        $token = $request->param('token', '', 'trim');
        $jwt = TeacherJwtTool::validateToken($token);
        if($jwt) {
            $teacher = Teacher::where(['id'=> $jwt->id])->find();
            if($teacher){
                //teacher方法注入到请求头中
                Request::hook('teacher', function () use($teacher){
                    return $teacher;
                });
            }else{
                echo json_encode(['code' => 400, 'msg'=>'teacher error', 'data' => '']);
                exit;
            }
        }else{
            echo json_encode(['code' => 400, 'msg'=>'token error', 'data' => '']);
            exit;
        }
    }

}
