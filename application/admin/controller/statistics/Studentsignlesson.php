<?php

namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use app\common\model\CourseLesson;
use app\common\model\CourseLessonSign;

/**
 * 学员签到课时
 *
 */
class Studentsignlesson extends Backend
{
    public function index()
    {
        $course_id = $this->request->get("course_id");
        $user_id = $this->request->get("user_id");
        if ($this->request->isAjax()) {
            $where = [];
            $where['c.id'] = $course_id;
            
            $res = CourseLesson::alias('cl')
                    ->join('fa_course c', 'cl.course_id=c.id', 'left')
                    ->field('cl.id,cl.sort,cl.lesson_no,cl.lesson_name,cl.start_time,cl.end_time')
                    ->where($where)
                    ->order('sort')
                    ->select();

            $list = [];
            foreach($res as $k => $v ) {
                $sign = CourseLessonSign::where(['lesson_id'=> $v['id'], 'user_id'=> $user_id])->find();
                $list[] = [
                    'id'=> $v['id'],
                    'sort'=> $v['sort'],
                    'lesson_no'=> $v['lesson_no'],
                    'lesson_name'=> $v['lesson_name'],
                    'start_time'=> $v['start_time'],
                    'end_time'=> $v['end_time'],
                    'signed_status'=> ($sign==false ? '未签到' : '已签到'),
                    'signed_time' => ($sign==false ? '' : $sign['createtime']),
                ];
            }

            $result = array("total" => 0, "rows" => $list);
            return json($result);
        }
        $this->view->assign("course_id", $course_id);
        $this->view->assign("user_id", $user_id);
        return $this->view->fetch();
    }
}
