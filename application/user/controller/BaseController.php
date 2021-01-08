<?php
namespace app\user\controller;

use think\Request;
use think\Controller;
use app\user\model\User;
use app\user\common\UserJwtTool;

class BaseController extends Controller
{
    public function __construct(Request $request)
    {
        $token = $request->param('token', '', 'trim');
        $jwt = UserJwtTool::validateToken($token);
        if($jwt) {
            $user = User::where(['id'=> $jwt->id])->find();
            if($user){
                //user方法注入到请求头中
                Request::hook('user', function () use($user){
                    return $user;
                });
            }else{
                echo json_encode(['code' => 400, 'msg'=>'user error', 'data' => '']);
                exit;
            }
        }else{
            echo json_encode(['code' => 400, 'msg'=>'token error', 'data' => '']);
            exit;
        }
    }

}
