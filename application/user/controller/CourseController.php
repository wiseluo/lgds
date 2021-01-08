<?php
namespace app\user\controller;

use think\Request;
use app\common\model\Course;
use app\common\model\CourseLesson;
use app\common\model\CourseEnroll;
use app\common\model\CourseLessonSign;

class CourseController extends BaseController
{
    //课程列表
    public function index(Request $request)
    {
        $pagesize = $request->param('pagesize',10);

        $where = [];
        $where['c.status'] = 0; //上架
        $where['end_enroll_date'] = ['>= time', date('Y-m-d H:i:s')];

        $res = Course::alias('c')
            ->join('fa_course_type ct', 'ct.id=c.type_id', 'left')
            ->field('c.id,c.image,c.name,c.teachername,c.crowd,c.quota,c.used_quota,c.start_enroll_date,c.end_enroll_date,ct.name course_type')
            ->where($where)
            ->order('c.start_enroll_date desc')
            ->paginate($pagesize)
            ->toArray();
        
        $data = $res['data'];
        $list = [];
        foreach($data as $k => $v ) {
            $enroll = CourseEnroll::where(['course_id'=> $v['id'], 'user_id'=> request()->user()['id']])->find();
            $status_str = '';
            if($enroll == null) {
                $status_str = '可报名';
                if($v['quota'] == $v['used_quota']) {
                    $status_str = '已满员';
                }
            }else if($enroll['status'] == 0) {
                $status_str = '待审核';
            }else{
                $status_str = '已成功';
            }
            $list[] = [
                'id'=> $v['id'],
                'course_type'=> $v['course_type'],
                'image'=> $v['image'],
                'name'=> $v['name'],
                'teachername'=> $v['teachername'],
                'crowd'=> $v['crowd'],
                'unused_quota'=> ($v['quota'] - $v['used_quota']),
                'enroll_date'=> date('Y.m.d H:i', strtotime($v['start_enroll_date'])),
                'status_str'=> $status_str,
            ];
        }
        $res['data'] = $list;
        return show(200, '成功', $res);
    }

    //我报名的课程列表
    public function myCourse(Request $request)
    {
        $pagesize = $request->param('pagesize',10);

        $where = [];
        $where['ce.user_id'] = request()->user()['id'];

        $res = CourseEnroll::alias('ce')
            ->join('fa_course c', 'ce.course_id=c.id', 'left')
            ->join('fa_course_type ct', 'ct.id=c.type_id', 'left')
            ->field('c.id,ce.status,c.image,c.name,c.teachername,c.crowd,c.quota,c.used_quota,c.start_enroll_date,c.end_enroll_date,c.close_date,ct.name course_type')
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
                'status_str'=> (time() > strtotime($v['close_date'] .' 23:59:59') ? '已结束' : ($v['status'] == 0 ? '待审核' : '已成功')),
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
            ->field('ce.id,ce.user_id,ce.status,u.username,u.avatar')
            ->where('ce.course_id', $id)
            ->where(['ce.course_id'=> $id])
            ->limit(5)
            ->select();

        $user_id = request()->user()['id'];
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
            'enroll_status_str'=> '未报名',
            'wx_qrcode'=> $course['wx_qrcode'],
        ];
        $lesson_data = [];
        foreach($lesson as $k => $v) {
            $sign = CourseLessonSign::where(['lesson_id'=> $v['id'], 'user_id'=> $user_id])->find();
            $lesson_data[] = [
                'id'=> $v['id'],
                'sort'=> $v['sort'],
                'lesson_no'=> $v['lesson_no'],
                'lesson_name'=> $v['lesson_name'],
                'time'=> date('Y.m.d H:i', strtotime($v['start_time'])) .'-'. date('H:i', strtotime($v['end_time'])),
                'sign_status_str'=> ($sign == false ? '未签到' : '已签到'),
            ];
        }
        $enroll_data = [];
        foreach($enroll as $i => $j) {
            if($j['user_id'] == $user_id) {
                $res['enroll_status_str'] = ($j['status'] == 0 ? '待审核' : '已成功');
            }
            $enroll_data[] = [
                'id'=> $j['id'],
                'user_id'=> $j['user_id'],
                'username'=> $j['username'],
                'avatar'=> $j['avatar'],
            ];
        }
        $res['lesson'] = $lesson_data;
        $res['enroll'] = $enroll_data;
        return show(200, '成功', $res);
    }
}
