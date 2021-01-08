<?php
namespace app\teacher\controller;

use think\Request;
use app\common\model\Course;
use app\common\model\CourseEnroll;

class CourseEnrollController extends BaseController
{
    //课程报名人员列表
    public function index(Request $request)
    {
        $course_id = $request->param('course_id', 0);
        if($course_id == 0) {
            return show(401, '课程id必填');
        }

        $enroll = CourseEnroll::alias('ce')
            ->join('fa_user u', 'ce.user_id=u.id', 'left')
            ->field('ce.id,u.username,u.avatar,ce.createtime')
            ->where(['ce.course_id'=> $course_id])
            ->select();
        $list = [];
        foreach($enroll as $k => $v) {
            $list[] = [
                'id'=> $v['id'],
                'username'=> $v['username'],
                'avatar'=> $v['avatar'],
                'enroll_date'=> date('Y.m.d H:i:s', $v['createtime']),
            ];
        }
        $res['list'] = $list;
        $res['total'] = count($list);
        return show(200, '成功', $res);
    }
    
    //我任教的课程中有学员退课的课程列表
    public function dropCourseList(Request $request)
    {
        $where['c.teacher_id'] = request()->teacher()['id'];
        $res = CourseEnroll::withTrashed()->alias('ce')
            ->join('fa_course c', 'c.id=ce.course_id', 'left')
            ->join('fa_course_type ct', 'ct.id=c.type_id', 'left')
            ->field('c.id,c.image,c.name,c.teachername,c.crowd,c.quota,c.used_quota,c.start_enroll_date,c.end_enroll_date,ct.name course_type')
            ->whereNotNull('ce.deletetime')
            ->where($where)
            ->group('c.id')
            ->select();
        
        $list = [];
        foreach($res as $k => $v ) {
            $list[] = [
                'id'=> $v['id'],
                'course_type'=> $v['course_type'],
                'image'=> $v['image'],
                'name'=> $v['name'],
                'teachername'=> $v['teachername'],
                'crowd'=> $v['crowd'],
                'unused_quota'=> ($v['quota'] - $v['used_quota']),
                'enroll_date'=> date('Y.m.d H:i', strtotime($v['start_enroll_date'])),
            ];
        }
        return show(200, '成功', $list);
    }

    //某门课的退课人员列表
    public function dropEnrollList(Request $request)
    {
        $param = $request->param();
        $validate = validate('CourseEnrollValidate');
        if(!$validate->scene('dropEnrollList')->check($param)) {
            return json(['code'=> 401, 'type'=> 'dropEnrollList', 'msg'=> $validate->getError()]);
        }
        $course = Course::get($param['course_id']);
        if($course == false) {
            return show(401, '课程不存在');
        }
        $data = CourseEnroll::withTrashed()->alias('ce')
            ->join('fa_user u', 'u.id=ce.user_id', 'left')
            ->field('ce.id,ce.username,u.avatar')
            ->where('ce.course_id', $param['course_id'])
            ->whereNotNull('ce.deletetime')
            ->group('ce.user_id')
            ->select();
        $res = [
            'id'=> $course['id'],
            'name'=> $course['name'],
            'image'=> $course['image'],
            'list'=> $data,
        ];
        return show(200, '成功', $res);
    }

}
