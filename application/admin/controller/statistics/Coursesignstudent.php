<?php

namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use app\common\model\CourseLessonSign;

/**
 * 课程签到学员
 *
 */
class Coursesignstudent extends Backend
{
    public function index()
    {
        $lesson_id = $this->request->get("lesson_id");
        if ($this->request->isAjax()) {
            $where = [];
            $where['cls.lesson_id'] = $lesson_id;

            $res = CourseLessonSign::alias('cls')
                    ->join('fa_user u', 'cls.user_id=u.id', 'left')
                    ->field('u.id,u.username,u.phone,u.gender,u.work_unit,u.nickname,u.avatar')
                    ->where($where)
                    ->select();

            $result = array("total" => 0, "rows" => $res);

            return json($result);
        }
        $this->view->assign("lesson_id", $lesson_id);
        return $this->view->fetch();
    }
    
}
