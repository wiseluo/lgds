<?php
namespace app\teacher\controller;

use think\Request;
use app\common\model\CourseEnroll;
use app\common\model\CourseLesson;
use app\common\model\CourseLessonSign;

class CourseLessonController extends BaseController
{
    //课时签到详情
    public function signRead(Request $request, $id)
    {
        $lesson = CourseLesson::alias('cl')
            ->join('fa_course c', 'c.id=cl.course_id', 'left')
            ->field('cl.id,cl.course_id,cl.lesson_no,cl.lesson_name,cl.start_time,cl.end_time,c.name course_name,c.used_quota,c.confirm_quota')
            ->where('cl.id', $id)
            ->find();
        if($lesson == false) {
            return show(400, '课时不存在');
        }
        $enroll = CourseEnroll::alias('ce')
            ->join('fa_user u', 'ce.user_id=u.id', 'left')
            ->field('u.id,u.username,u.avatar')
            ->where(['ce.course_id'=> $lesson['course_id'], 'ce.status'=> ['>', 0]])
            ->select();
        $sign = CourseLessonSign::alias('cls')
            ->join('fa_user u', 'cls.user_id=u.id', 'left')
            ->field('u.id,u.username,u.avatar')
            ->where('cls.lesson_id', $id)
            ->select();
        
        $signed_user = [];
        foreach($sign as $k => $v) {
            $signed_user[$v['id']] = [
                'id'=> $v['id'],
                'username'=> $v['username'],
                'avatar'=> $v['avatar'],
            ];
        }
        $unsigned_user = [];
        foreach($enroll as $i => $j) {
            if(!array_key_exists($j['id'], $signed_user)) {
                $unsigned_user[] = [
                    'id'=> $j['id'],
                    'username'=> $j['username'],
                    'avatar'=> $j['avatar'],
                ];
            }
        }
        $signed_user_number = count($sign);
        $res = [
            'id'=> $lesson['id'],
            'course_name'=> $lesson['course_name'],
            'lesson_no'=> $lesson['lesson_no'],
            'lesson_name'=> $lesson['lesson_name'],
            'lesson_time'=> date('Y.m.d H:i', strtotime($lesson['start_time'])) .'-'. date('H:i', strtotime($lesson['end_time'])),
            'signed_user_number'=> $signed_user_number,
            'unsigned_user_number'=> $lesson['confirm_quota'] - count($sign),
            'sign_rate'=> bcmul(bcdiv($signed_user_number, $lesson['confirm_quota'], 2), 100) .'%',
            'signed_user'=> array_values($signed_user),
            'unsigned_user'=> $unsigned_user,
        ];
        return show(200, '成功', $res);
    }

}
