<?php

namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use app\common\model\CourseEnroll;
use app\common\model\CourseLesson;
use app\common\model\CourseLessonSign;

/**
 * 课程未签到学员
 *
 */
class Courseunsignstudent extends Backend
{
    public function index()
    {
        $lesson_id = $this->request->get("lesson_id");
        if ($this->request->isAjax()) {
            $lesson = CourseLesson::get($lesson_id);

            $sign = CourseLessonSign::field('user_id')
                    ->where('lesson_id', $lesson_id)
                    ->select();
            $enroll = CourseEnroll::alias('ce')
                ->join('fa_user u', 'ce.user_id=u.id', 'left')
                ->field('u.id,u.username,u.phone,u.gender,u.work_unit,u.nickname,u.avatar')
                ->where(['ce.course_id'=> $lesson['course_id'], 'ce.status'=> ['>', 0]])
                ->select();

            $signed_user = [];
            foreach($sign as $k => $v) {
                $signed_user[] = $v['user_id'];
            }
            $unsigned_user = [];
            foreach($enroll as $i => $j) {
                if(!in_array($j['id'], $signed_user)) {
                    $unsigned_user[] = [
                        'id'=> $j['id'],
                        'username'=> $j['username'],
                        'phone'=> $j['phone'],
                        'gender'=> $j['gender'],
                        'work_unit'=> $j['work_unit'],
                        'nickname'=> $j['nickname'],
                        'avatar'=> $j['avatar'],
                    ];
                }
            }
            $result = array("total" => 0, "rows" => $unsigned_user);

            return json($result);
        }
        $this->view->assign("lesson_id", $lesson_id);
        return $this->view->fetch();
    }
    
}
