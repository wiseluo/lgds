<?php

namespace app\admin\controller\course;

use think\Db;
use app\common\controller\Backend;
use app\common\model\Course as CourseModel;
use app\common\model\CourseEnroll as CourseEnrollModel;
use app\common\model\CourseLesson as CourseLessonModel;
use app\common\model\CourseLessonSign as CourseLessonSignModel;

/**
 * 课程管理
 *
 * @icon fa fa-circle-o
 */
class Course extends Backend
{
    
    /**
     * Course模型对象
     * @var \app\admin\model\Course
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\course\Course;
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
                if($filter['status'] != 2) {
                    $where['c.status'] = $filter['status'];
                }
            }else{
                $where['c.status'] = 0;
            }
            if(isset($filter['name'])) {
                $where['c.name'] = ["LIKE", "%". $filter['name'] ."%"];
            }
            if(isset($filter['teachername'])) {
                $where['c.teachername'] = ["LIKE", "%". $filter['teachername'] ."%"];
            }
            if(isset($filter['periods_number'])) {
                $where['c.periods_number'] = ["LIKE", "%". $filter['periods_number'] ."%"];
            }

            $total = $this->model->alias('c')
                    ->where($where)
                    ->count();
            $list = $this->model->alias('c')
                    ->join('fa_course_type ct', 'ct.id=c.type_id', 'left')
                    ->field('c.id,c.periods_number,ct.name type_name,c.name,c.teachername,c.crowd,c.begin_date,c.class_number,c.class_time,c.class_location,c.start_enroll_date,c.quota,c.used_quota,c.status')
                    ->where($where)
                    ->limit($offset, $limit)
                    ->order('c.periods_number desc, c.type_id asc, c.name asc')
                    ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function detail($ids = null)
    {
        $course = CourseModel::with('courseType')
            ->where('id',$ids)
            ->find();
        $courselesson = CourseLessonModel::where('course_id',$ids)->order('sort asc')->select();

        $this->view->assign("ids", $ids);
        $this->view->assign("course", $course);
        $this->view->assign("courselesson", $courselesson);
        return $this->view->fetch();
    }

    /**
     * 上架
     */
    public function confirmonshelf($ids = null)
    {
        $course = CourseModel::get($ids);
        $result = $course->allowField(true)->save(['status'=> 0]);
        if ($result !== false) {
            $this->success();
        } else {
            $this->error(__('未更改'));
        }
    }

    /**
     * 下架
     */
    public function confirmoffshelf($ids = null)
    {
        $course = CourseModel::get($ids);
        $result = $course->allowField(true)->save(['status'=> 1]);
        if ($result !== false) {
            $this->success();
        } else {
            $this->error(__('未更改'));
        }
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            Db::startTrans();
            try {
                $course_res = CourseModel::destroy(['id'=> $ids]);
                if($course_res) {
                    CourseEnrollModel::destroy(['course_id'=> $ids]);
                    CourseLessonModel::destroy(['course_id'=> $ids]);
                    CourseLessonSignModel::destroy(['course_id'=> $ids]);

                    Db::commit();
                    $this->success();
                }else{
                    Db::rollback();
                    $this->error('删除课程失败');
                }
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
}
