<?php

namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use app\common\model\Course;

/**
 * 课程年度统计
 *
 */
class Courseannual extends Backend
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
            
            if(isset($filter['annual'])) {
                $annual = str_replace(' - ', ',', $filter['annual']);
                $arr = array_slice(explode(',', $annual), 0, 2); //在数组中从0开始取出两段值
                if($arr[0] && $arr[1]) {
                    $where['periods_number'] = ['between', [$arr[0] .'01', $arr[1] .'12']];
                }else if($arr[0]) {
                    $where['periods_number'] = ['>=', $arr[0] .'01'];
                }else if($arr[1]) {
                    $where['periods_number'] = ['<=', $arr[1] .'12'];
                }
            }

            $total = Course::field('left(periods_number,4) annual')
                    ->where($where)
                    ->group('annual')
                    ->count();
            $res = Course::field('left(periods_number,4) annual,count(id) course_count,sum(quota) quota_sum,sum(used_quota) used_quota_sum')
                    ->where($where)
                    ->group('annual')
                    ->order('annual asc')
                    ->limit($offset, $limit)
                    ->select();

            $list = [];
            foreach($res as $k => $v ) {
                $list[] = [
                    'annual'=> $v['annual'],
                    'course_count'=> $v['course_count'],
                    'quota_sum'=> $v['quota_sum'],
                    'used_quota_sum'=> $v['used_quota_sum'],
                    'enroll_rate'=> bcmul(bcdiv($v['used_quota_sum'], $v['quota_sum'], 4), 100, 2) .'%',
                ];
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
}
