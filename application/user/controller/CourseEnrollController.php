<?php
namespace app\user\controller;

use think\Db;
use think\Request;
use app\user\model\User;
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

    public function read($id)
    {
        $data = CourseEnroll::where('id', $id)->find();
        if ($data) {
            return show(200, '成功', $data);
        }else{
            return show(401, '课程未报名');
        }
    }

    public function save(Request $request)
    {
        $param = $request->param();
        $validate = validate('CourseEnrollValidate');
        if(!$validate->scene('save')->check($param)) {
            return json(['code'=> 401, 'type'=> 'save', 'msg'=> $validate->getError()]);
        }
        $course = Course::get($param['course_id']);
        if($course == false) {
            return show(401, '课程不存在');
        }
        if($course['used_quota'] >= $course['quota']) {
            return show(401, '课程已满员');
        }
        if(time() < strtotime($course['start_enroll_date'])) {
            return show(401, '报名未开始');
        }
        if(time() > strtotime($course['end_enroll_date'])) {
            return show(401, '报名已结束');
        }
        $user_id = request()->user()['id'];
        $user = User::get($user_id);
        if($user['status'] == 0) {
            return show(201, '您还未完善个人信息，请先完善个人信息');
        }else if($user['blacklist'] == 1) {
            return show(401, '您的账号处于黑名单状态，请联系义乌市总工会！');
        }
        // $course_enroll = CourseEnroll::where(['course_id'=> $param['course_id'], 'user_id'=> $user_id])->find();
        // if($course_enroll) {
        //     return show(401, '已报名该课程');
        // }

        //一年同时只能报一次课程
        $closed_course_enroll = CourseEnroll::where(['user_id'=> $user_id])->order('id desc')->find();
        if($closed_course_enroll) {
            $closed_course = Course::get($closed_course_enroll['course_id']);
            //判断期数年是否相同，不同可继续报名
            if(date('Y', strtotime($course['periods_number'])) == date('Y', strtotime($closed_course['periods_number']))) {
                return show(401, '每年课程只限报一次！');
            }
        }
        $enroll_data = [
            'course_id' => $param['course_id'],
            'course_name' => $course['name'],
            'user_id' => $user_id,
            'username' => $user['username'],
        ];

        Db::startTrans();
        $enroll = new CourseEnroll($enroll_data);
        $res = $enroll->allowField(true)->save();
        if ($res) {
            //删除旧的取消报名记录
            CourseEnroll::withTrashed()->where(['course_id'=> $param['course_id'], 'user_id'=> $user_id])->whereNotNull('deletetime')->delete();
            $course_res = Course::where('id', $param['course_id'])->setInc('used_quota');
            if($course_res) {
                Db::commit();
                return show(200, '报名成功');
            }else{
                Db::rollback();
                return show(401, '修改已用名额失败');
            }
        }else{
            Db::rollback();
            return show(401, '报名失败');
        }
    }

    /* 取消报名
     * param  id：课程id
     */
    // public function delete($id)
    // {
    //     $enroll = CourseEnroll::where(['course_id'=> $id, 'user_id'=> request()->user()['id']])->find();
    //     if($enroll == false) {
    //         return show(401, '课程未报名');
    //     }else if($enroll['status'] != 0) {
    //         return show(401, '报名已审核，不能取消');
    //     }else if($enroll['honest'] == 1){
    //         return show(401, '诚信报名，不能取消');
    //     }
    //     Db::startTrans();
    //     $res = CourseEnroll::destroy(['id'=> $enroll['id']]);
    //     if ($res) {
    //         $course_res = Course::where('id', $enroll['course_id'])->setDec('used_quota');
    //         if($course_res) {
    //             Db::commit();
    //             return show(200, '取消成功');
    //         }else{
    //             Db::rollback();
    //             return show(401, '修改已用名额失败');
    //         }
    //     }else{
    //         Db::rollback();
    //         return show(401, '取消失败');
    //     }
    // }

    //我有退课的课程列表
    public function dropCourseList(Request $request)
    {
        $res = CourseEnroll::withTrashed()->alias('ce')
            ->join('fa_course c', 'c.id=ce.course_id', 'left')
            ->join('fa_course_type ct', 'ct.id=c.type_id', 'left')
            ->field('c.id,c.image,c.name,c.teachername,c.crowd,c.quota,c.used_quota,c.start_enroll_date,c.end_enroll_date,ct.name course_type')
            ->where('ce.user_id', request()->user()['id'])
            ->whereNotNull('ce.deletetime')
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
}
