<?php

namespace app\admin\controller\course;

use app\common\controller\Backend;
use app\admin\model\course\Course as CourseModel;
use app\admin\model\course\CourseLesson as CourseLessonModel;
use app\common\behavior\QrcodeTool;

/**
 * 课程的课时管理
 *
 * @icon fa fa-circle-o
 */
class Courselesson extends Backend
{
    
    /**
     * CourseLesson模型对象
     * @var \app\admin\model\CourseLesson
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\course\CourseLesson;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 课程关联课时列表
    */
    public function index($ids)
    {
        if ($this->request->isAjax()) {
            $where['course_id'] = $ids;
            $list = CourseLessonModel::where($where)
                ->order('sort asc')
                ->select();
            $list = collection($list)->toArray();

            $result = array("total" => 0, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
    */
    public function add($course_id)
    {
        if ($this->request->isPost()) {
            $param = $this->request->post("row/a");
            $validate = validate('Courselesson', 'validate\course');
            if(!$validate->scene('add')->check($param)) {
                return show(401, $validate->getError());
            }
            $data = [
                'course_id' => $param['course_id'],
                'sort' => $param['sort'],
                'lesson_no' => $param['lesson_no'],
                'lesson_name' => $param['lesson_name'],
                'start_time' => $param['start_time'],
                'end_time' => $param['end_time'],
            ];
            $course_lesson = new CourseLessonModel($data);
            $res = $course_lesson->allowField(true)->save();
            if($res) {
                $qrcode = $this->makeQrcode($course_lesson->id);
                $lesson = new CourseLessonModel();
                $lesson->allowField(true)->save(['qrcode'=> $qrcode], ['id'=> $course_lesson->id]);
                $this->success();
            }else{
                $this->error('添加失败');
            }
        }
        $this->view->assign("course_id", $course_id);
        return $this->view->fetch();
    }

    /**
     * 编辑
    */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $param = $this->request->post("row/a");
            $validate = validate('Courselesson', 'validate\course');
            if(!$validate->scene('edit')->check($param)) {
                return show(401, $validate->getError());
            }
            $data = [
                'sort' => $param['sort'],
                'lesson_no' => $param['lesson_no'],
                'lesson_name' => $param['lesson_name'],
                'start_time' => $param['start_time'],
                'end_time' => $param['end_time'],
                'qrcode' => $this->makeQrcode($ids), //to do 注释
            ];
            $course_lesson = new CourseLessonModel();
            $res = $course_lesson->allowField(true)->save($data, ['id'=> $ids]);
            if($res) {
                $this->success();
            }else{
                $this->error('编辑失败');
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    private function makeQrcode($id)
    {
        $qrcodeTool = new QrcodeTool();
        return $qrcodeTool->qrcode('/user/courselesson/qrcode_sign?lesson_id='. $id);
    }
}
