<?php

namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use app\common\model\CourseLesson;

/**
 * 课程签到管理
 *
 */
class Coursesign extends Backend
{
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 0);
            $filter = $this->request->get("filter", '');
            $filter = (array)json_decode($filter, true);
            $filter = $filter ? $filter : [];
            
            $where = [];
            
            if(isset($filter['periods_number'])) {
                $where['c.periods_number'] = ["LIKE", "%". $filter['periods_number'] ."%"];
            }
            if(isset($filter['name'])) {
                $where['c.name'] = ["LIKE", "%". $filter['name'] ."%"];
            }
            if(isset($filter['lesson_name'])) {
                $where['cl.lesson_name'] = ["LIKE", "%". $filter['lesson_name'] ."%"];
            }

            $total = CourseLesson::alias('cl')
                    ->join('fa_course c', 'cl.course_id=c.id', 'left')
                    ->join('fa_course_lesson_sign cls', 'cls.lesson_id=cl.id', 'left')
                    ->where($where)
                    ->group('cl.id')
                    ->count();
            $res = CourseLesson::alias('cl')
                    ->join('fa_course c', 'cl.course_id=c.id', 'left')
                    ->join('fa_course_lesson_sign cls', 'cls.lesson_id=cl.id', 'left')
                    ->field('c.periods_number,c.name,cl.id,cl.lesson_no,cl.lesson_name,c.quota,c.confirm_quota,count(cls.id) signed_number')
                    ->where($where)
                    ->group('cl.id')
                    ->order('c.periods_number desc, c.name asc, cl.sort asc')
                    ->limit($offset, $limit)
                    ->select();

            $list = [];
            foreach($res as $k => $v ) {
                $list[] = [
                    'id'=> $v['id'],
                    'periods_number'=> $v['periods_number'],
                    'name'=> $v['name'],
                    'lesson_no'=> $v['lesson_no'],
                    'lesson_name'=> $v['lesson_name'],
                    'quota'=> $v['quota'],
                    'confirm_quota'=> $v['confirm_quota'],
                    'signed_number'=> $v['signed_number'],
                    'unsigned_number'=> ($v['confirm_quota'] - $v['signed_number']),
                ];
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
}
