<?php
namespace app\teacher\controller;

use think\Request;
use app\common\model\Course;
use app\common\model\CourseLesson;
use app\common\model\CourseEnroll;

class CourseController extends BaseController
{
    //课程列表
    public function index(Request $request)
    {
        $pagesize = $request->param('pagesize',10);
        $status = $request->param('status', '');

        $where['c.status'] = 0; //上架

        $res = Course::alias('c')
            ->join('fa_course_type ct', 'ct.id=c.type_id', 'left')
            ->field('c.id,c.image,c.name,c.teachername,c.crowd,c.quota,c.used_quota,c.start_enroll_date,c.end_enroll_date,c.close_date,ct.name course_type')
            ->where($where)
            ->order('c.start_enroll_date desc')
            ->paginate($pagesize)
            ->toArray();
        
        $data = $res['data'];
        $list = [];
        foreach($data as $k => $v ) {
            $list[] = [
                'id'=> $v['id'],
                'course_type'=> $v['course_type'],
                'image'=> $v['image'],
                'name'=> $v['name'],
                'teachername'=> $v['teachername'],
                'crowd'=> $v['crowd'],
                'unused_quota'=> ($v['quota'] - $v['used_quota']),
                'enroll_date'=> date('Y.m.d H:i', strtotime($v['start_enroll_date'])),
                'status_str'=> (time() > strtotime($v['close_date'] .' 23:59:59') ? '已结束' : (time() < strtotime($v['start_enroll_date']) ? '未开课' : '已开课')),
            ];
        }
        $res['data'] = $list;
        return show(200, '成功', $res);
    }

    //我任教的课程列表
    public function myCourse(Request $request)
    {
        $pagesize = $request->param('pagesize',10);

        $where['c.teacher_id'] = request()->teacher()['id'];
        $res = Course::alias('c')
            ->join('fa_course_type ct', 'ct.id=c.type_id', 'left')
            ->field('c.id,c.image,c.name,c.teachername,c.crowd,c.quota,c.used_quota,c.start_enroll_date,c.end_enroll_date,c.close_date,ct.name course_type')
            ->where($where)
            ->order('c.start_enroll_date desc')
            ->paginate($pagesize)
            ->toArray();
        
        $data = $res['data'];
        $list = [];
        foreach($data as $k => $v ) {
            $list[] = [
                'id'=> $v['id'],
                'course_type'=> $v['course_type'],
                'image'=> $v['image'],
                'name'=> $v['name'],
                'teachername'=> $v['teachername'],
                'crowd'=> $v['crowd'],
                'unused_quota'=> ($v['quota'] - $v['used_quota']),
                'enroll_date'=> date('Y.m.d H:i', strtotime($v['start_enroll_date'])),
                'status_str'=> (time() > strtotime($v['close_date'] .' 23:59:59') ? '已结束' : (time() < strtotime($v['start_enroll_date']) ? '未开课' : '已开课')),
            ];
        }
        $res['data'] = $list;
        return show(200, '成功', $res);
    }

    //课程详情
    public function read($id)
    {
        $course = Course::get($id);
        $lesson = CourseLesson::where('course_id', $id)
            ->order('sort asc')
            ->select();
        $enroll = CourseEnroll::alias('ce')
            ->join('fa_user u', 'ce.user_id=u.id', 'left')
            ->field('u.id,u.username,u.avatar')
            ->where(['ce.course_id'=> $id])
            ->limit(5)
            ->select();

        $res = [
            'id'=> $course['id'],
            'image'=> $course['image'],
            'name'=> $course['name'],
            'teachername'=> $course['teachername'],
            'detail'=> $course['detail'],
            'crowd'=> $course['crowd'],
            'periods_number'=> $course['periods_number'],
            'begin_date'=> $course['begin_date'],
            'close_date'=> $course['close_date'],
            'class_time'=> $course['class_time'],
            'class_number'=> $course['class_number'],
            'class_location'=> $course['class_location'],
            'quota'=> $course['quota'],
            'used_quota'=> $course['used_quota'],
            'enroll_date'=> date('Y.m.d H:i', strtotime($course['start_enroll_date'])) .'-'. date('Y.m.d H:i', strtotime($course['end_enroll_date'])),
            'wx_qrcode'=> $course['wx_qrcode'],

        ];
        $lesson_data = [];
        foreach($lesson as $k => $v) {
            $lesson_data[] = [
                'id'=> $v['id'],
                'sort'=> $v['sort'],
                'lesson_no'=> $v['lesson_no'],
                'lesson_name'=> $v['lesson_name'],
                'time'=> date('Y.m.d H:i', strtotime($v['start_time'])) .'-'. date('H:i', strtotime($v['end_time'])),
                'status_str'=> (time() > strtotime($v['start_time']) ? '查看签到' : '未签到'),
            ];
        }
        $res['lesson'] = $lesson_data;
        $res['enroll'] = $enroll;
        return show(200, '成功', $res);
    }
}
