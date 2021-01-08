<?php

namespace app\admin\controller\course;

use think\Db;
use app\common\controller\Backend;
use app\common\model\Course as CourseModel;
use app\common\model\CourseEnroll as CourseEnrollModel;

/**
 * 课程报名管理
 *
 * @icon fa fa-circle-o
 */
class Courseenroll extends Backend
{
    
    /**
     * Courseenroll模型对象
     * @var \app\admin\model\CourseEnroll
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\course\CourseEnroll;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 0);
            $search = $this->request->get("search", '');
            $filter = $this->request->get("filter", '');
            $filter = (array)json_decode($filter, true);
            $filter = $filter ? $filter : [];
            
            $where = [];
            if(isset($filter['status'])) {
                $where['ce.status'] = $filter['status'];
            }
            if(isset($filter['periods_number'])) {
                $where['c.periods_number'] = ["LIKE", "%". $filter['periods_number'] ."%"];
            }
            if(isset($filter['course_name'])) {
                $where['ce.course_name'] = ["LIKE", "%". $filter['course_name'] ."%"];
            }
            if(isset($filter['username'])) {
                $where['ce.username'] = ["LIKE", "%". $filter['username'] ."%"];
            }
            if(isset($filter['phone'])) {
                $where['u.phone'] = ["LIKE", "%". $filter['phone'] ."%"];
            }
            if(isset($filter['work_unit'])) {
                $where['u.work_unit'] = ["LIKE", "%". $filter['work_unit'] ."%"];
            }
            if(isset($filter['createtime'])) {
                $createtime = str_replace(' - ', ',', $filter['createtime']);
                $arr = array_slice(explode(',', $createtime), 0, 2); //在数组中从0开始取出两段值
                $where['ce.createtime'] = ['between time', $arr];
            }
            if(isset($filter['updatetime'])) {
                $createtime = str_replace(' - ', ',', $filter['updatetime']);
                $arr = array_slice(explode(',', $createtime), 0, 2); //在数组中从0开始取出两段值
                $where['ce.updatetime'] = ['between time', $arr];
            }

            $total = $this->model->alias('ce')
                    ->join('fa_user u', 'ce.user_id=u.id', 'left')
                    ->where($where)
                    ->count();
            $list = $this->model->alias('ce')
                    ->join('fa_course c', 'ce.course_id=c.id', 'left')
                    ->join('fa_user u', 'ce.user_id=u.id', 'left')
                    ->field('ce.id,ce.course_id,c.name course_name,c.periods_number,ce.username,u.phone,u.gender,u.work_unit,ce.status,ce.createtime,ce.updatetime')
                    ->where($where)
                    ->order('c.periods_number desc,c.name asc,ce.createtime asc')
                    ->limit($offset, $limit)
                    ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 取消报名
     */
    public function cancelenroll($ids = null)
    {
        Db::startTrans();
        $enroll = CourseEnrollModel::get($ids);
        $res = CourseEnrollModel::destroy(['id'=> $ids]);
        if ($res) {
            $course_res = CourseModel::where('id', $enroll['course_id'])->setDec('used_quota');
            if($course_res) {
                Db::commit();
                $this->success('取消成功');
            }else{
                Db::rollback();
                $this->error(__('修改已用名额失败'));
            }
        }else{
            Db::rollback();
            $this->error(__('取消失败'));
        }
    }

    /**
     * 确认已缴
     */
    public function confirmpayment($ids = null)
    {
        Db::startTrans();
        $enroll = CourseEnrollModel::get($ids);
        if (!$enroll) {
            $this->error(__('报名记录不存在'));
        }else if($enroll->status != 0){
            $this->error(__('不在未缴费状态，不能确认已缴'));
        }
        $result = $enroll->allowField(true)->save(['status'=> 1]);
        if ($result !== false) {
            //确认已缴则删除退课记录
            CourseEnrollModel::withTrashed()->where(['course_id'=> $enroll->course_id, 'user_id'=> $enroll->user_id])->whereNotNull('deletetime')->delete();
            //增加确认名额
            $course_res = CourseModel::where('id', $enroll->course_id)->setInc('confirm_quota');
            if($course_res) {
                Db::commit();
                $this->success();
            }else{
                Db::rollback();
                $this->error(__('修改确认名额失败'));
            }
        } else {
            Db::rollback();
            $this->error(__('未更改'));
        }
    }

    /**
     * 确认未缴
     */
    public function confirmunpaid($ids = null)
    {
        Db::startTrans();
        $enroll = CourseEnrollModel::get($ids);
        if (!$enroll) {
            $this->error(__('报名记录不存在'));
        }else if($enroll->status != 1){
            $this->error(__('不在已缴费状态，不能确认未缴'));
        }
        $result = $enroll->allowField(true)->save(['status'=> 0]);
        if ($result !== false) {
            //减少确认名额
            $course_res = CourseModel::where('id', $enroll->course_id)->setDec('confirm_quota');
            if($course_res) {
                Db::commit();
                $this->success();
            }else{
                Db::rollback();
                $this->error(__('修改确认名额失败'));
            }
        } else {
            Db::rollback();
            $this->error(__('未更改'));
        }
    }
    
    /**
     * 确认退还
     */
    public function confirmreturn($ids = null)
    {
        $enroll = CourseEnrollModel::get($ids);
        if (!$enroll) {
            $this->error(__('报名记录不存在'));
        }else if($enroll->status != 1){
            $this->error(__('不在已缴费状态，不能确认退还'));
        }
        $result = $enroll->allowField(true)->save(['status'=> 2]);
        if ($result !== false) {
            $this->success();
        } else {
            $this->error(__('未更改'));
        }
    }

    /**
     * 确认不退
     */
    public function confirmnotreturn($ids = null)
    {
        $enroll = CourseEnrollModel::get($ids);
        if (!$enroll) {
            $this->error(__('报名记录不存在'));
        }else if($enroll->status != 1){
            $this->error(__('不在已缴费状态，不能确认不退'));
        }
        $result = $enroll->allowField(true)->save(['status'=> 3]);
        if ($result !== false) {
            $this->success();
        } else {
            $this->error(__('未更改'));
        }
    }
}
