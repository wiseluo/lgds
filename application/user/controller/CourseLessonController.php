<?php
namespace app\user\controller;

use think\Request;
use app\user\model\User;
use app\common\model\Course;
use app\common\model\CourseEnroll;
use app\common\model\CourseLesson;
use app\common\model\CourseLessonSign;

class CourseLessonController extends BaseController
{
    public function qrcodeSign(Request $request)
    {
        $lesson_id = $request->param('lesson_id', 0);
        $lesson = CourseLesson::get($lesson_id);
        if($lesson == false) {
            return show(401, '课时不存在');
        }
        $user_id = request()->user()['id'];
        $enroll = CourseEnroll::where(['course_id'=> $lesson['course_id'], 'user_id'=> $user_id])->find();
        if($enroll == false) {
            return show(401, '您未报名该课程，请先报名');
        }else if($enroll['status'] == 0) {
            return show(401, '您缴费未确认，请联系义乌市总工会！');
        }
        $user = User::get($user_id);
        $lesson_sign = CourseLessonSign::where(['lesson_id'=> $lesson_id, 'user_id'=> $user_id])->find();
        if($lesson_sign) {
            return show(401, '您已签到');
        }
        $sign_data = [
            'course_id' => $lesson['course_id'],
            'lesson_id' => $lesson_id,
            'user_id' => $user_id,
            'username' => $user['username'],
            'sign_time' => date('Y-m-d H:i:s'),
        ];
        $sign = new CourseLessonSign($sign_data);
        $res = $sign->allowField(true)->save();
        if ($res) {
            return show(200, '签到成功');
        }else{
            return show(401, '签到失败');
        }
    }
}
