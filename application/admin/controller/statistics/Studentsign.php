<?php

namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use app\common\model\Course;

/**
 * 学员签到管理
 *
 */
class Studentsign extends Backend
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
            $where['ce.status'] = ['>', 0]; //已缴过费的
            
            if(isset($filter['periods_number'])) {
                $where['c.periods_number'] = ["LIKE", "%". $filter['periods_number'] ."%"];
            }
            if(isset($filter['name'])) {
                $where['c.name'] = ["LIKE", "%". $filter['name'] ."%"];
            }
            if(isset($filter['username'])) {
                $where['u.username'] = ["LIKE", "%". $filter['username'] ."%"];
            }
            if(isset($filter['phone'])) {
                $where['u.phone'] = ["LIKE", "%". $filter['phone'] ."%"];
            }

            $total = Course::alias('c')
                    ->join('fa_course_enroll ce', 'ce.course_id=c.id', 'left')
                    ->join('fa_user u', 'ce.user_id=u.id', 'left')
                    ->join('fa_course_lesson_sign cls', 'cls.course_id=c.id and cls.user_id=ce.user_id', 'left')
                    ->where($where)
                    ->group('c.id,ce.user_id')
                    ->count();
            $res = Course::alias('c')
                    ->join('fa_course_enroll ce', 'ce.course_id=c.id', 'left')
                    ->join('fa_user u', 'ce.user_id=u.id', 'left')
                    ->join('fa_course_lesson_sign cls', 'cls.course_id=c.id and cls.user_id=ce.user_id', 'left')
                    ->field('c.id,c.periods_number,c.name,ce.user_id,u.username,u.phone,c.class_number,count(cls.id) signed_number')
                    ->where($where)
                    ->group('c.id,ce.user_id')
                    ->order('c.periods_number desc, c.name asc')
                    ->limit($offset, $limit)
                    ->select();

            $list = [];
            foreach($res as $k => $v ) {
                $list[] = [
                    'id'=> $v['id'],
                    'periods_number'=> $v['periods_number'],
                    'name'=> $v['name'],
                    'user_id'=> $v['user_id'],
                    'username'=> $v['username'],
                    'phone'=> $v['phone'],
                    'class_number'=> $v['class_number'],
                    'signed_number'=> $v['signed_number'],
                    'unsigned_number'=> ($v['class_number'] - $v['signed_number']),
                ];
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

}
