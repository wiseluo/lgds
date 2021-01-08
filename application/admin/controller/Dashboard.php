<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {   
        $seventtime = \fast\Date::unixtime('day', -7);
        $paylist = $createlist = [];
        for ($i = 0; $i < 7; $i++)
        {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $createlist[$day] = mt_rand(20, 200);
            $paylist[$day] = mt_rand(1, mt_rand(1, $createlist[$day]));
        }
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
        $this->view->assign([
            'totaluser'        => 35200,
            'totalviews'       => 219390,
            'totalorder'       => 32143,
            'totalorderamount' => 174800,
            'todayuserlogin'   => 321,
            'todayusersignup'  => 430,
            'todayorder'       => 2324,
            'unsettleorder'    => 132,
            'sevendnu'         => '80%',
            'sevendau'         => '32%',
            'paylist'          => $paylist,
            'createlist'       => $createlist,
            'addonversion'       => $addonVersion,
            'uploadmode'       => $uploadmode
        ]);

        return $this->view->fetch();
    }
    public function orderstatistics(){

        $get = $this->request->get();
        $start_time = $get['start_time'];
        $end_time   = $get['end_time'] .' 23:59:59';

        $today = date("Y-m-d");
        $end_today = date("Y-m-d 23:59:59");
        $today_order_count = db()->table('zk_order')->where('addtime','BETWEEN',[$today,$end_today])->where('isdel','0')->field('count(id)')->find();
        $order_count   = db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where('isdel','0')->field('count(id),sum(money)')->find();
        $message_count = db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['buy_score'=>2,'sell_score'=>2])->field('count(id)')->find();


        $this->view->assign([
            'end_today'        => $today_order_count['count(id)'],
            'today_order_count'       => $order_count['count(id)'],
            'order_count'       => $order_count['sum(money)'],
            'message_count' => $message_count['count(id)'],
        ]);
        
        return $this->view->fetch('dashboard/orderstatistics');
    }
    public function userstatistics(){

        $get = $this->request->get();
        $start_time = $get['start_time'];
        $end_time   = $get['end_time'] .' 23:59:59';

        $today = date("Y-m-d");
        $end_today = date("Y-m-d 23:59:59");
        $today_order_count = db()->table('zk_order')->where('addtime','BETWEEN',[$today,$end_today])->where('isdel','0')->field('count(id)')->find();
        $order_count   = db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where('isdel','0')->field('count(id),sum(money)')->find();
        $message_count = db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['buy_score'=>2,'sell_score'=>2])->field('count(id)')->find();


        $this->view->assign([
            'end_today'        => $today_order_count['count(id)'],
            'today_order_count'       => $order_count['count(id)'],
            'order_count'       => $order_count['sum(money)'],
            'message_count' => $message_count['count(id)'],
        ]);
        
        return $this->view->fetch('dashboard/userstatistics');
    }
    public function testreport(){
        return [['value'=>'10','name'=>'123'],['value'=>'1','name'=>'123'],['value'=>'2','name'=>'123'],['value'=>'5','name'=>'123'],['value'=>'15','name'=>'123'],['value'=>'13','name'=>'123']]; 
    }

    public function report_one(){
        $get = $this->request->get();

        $start_time = $time123  = $get['start_time'];
        // $start_time = $time123  = '2019-07-20';
        $data = [];
        $date_list = [];
        for ($i=0; $i < 7; $i++) {
            array_push($date_list,$time123);
            $starttime[$i] = $time123;
            $endtime[$i]  = $time123.' 23:59:59';
            $order_info = db()->table('zk_order')->where('addtime','BETWEEN',[$starttime[$i],$endtime[$i]])
            ->where(['isdel'=>0])->field('count(id) as num,sum(money) as total_money,addtime')->select();
            $data[$time123] = $order_info;
            $time123 = date("Y-m-d",strtotime("+1 day",strtotime($time123)));

        }
        // {
        //     name:'邮件营销',
        //     type:'line',
        //     stack: '总量',
        //     data:[120, 132, 101, 134, 90, 230, 210]
        // },
        // {
        //     name:'联盟广告',
        //     type:'line',
        //     stack: '总量',
        //     data:[220, 182, 191, 234, 290, 330, 310]
        // },
        $new_arr = [['name'=>'总数量','type'=>'line','stack'=>'总量','data'=>[]],['name'=>'总金额','type'=>'line','stack'=>'总量','data'=>[]]];
        foreach($data as $key => $value){
            array_push($new_arr[0]['data'],(float)$value[0]['num']);
            array_push($new_arr[1]['data'],(float)$value[0]['total_money']);
        }
        $new_data = [];
        $new_data['data'] = $new_arr;
        $new_data['date'] = $date_list;

        return $new_data;


    }
    public function report_two(){
        $get = $this->request->get();
        $start_time = $get['start_time'];
        $end_time   = $get['end_time'] .' 23:59:59';
        // $start_time = '2019-07-01';
        // $end_time   = '2019-07-29 23:59:59';

        $dan_count  =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'to_company_id'=>['=',0]])->field('count(id) as count')->find();
        $shuang_count  =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'to_company_id'=>['>',0],'from_company_id'=>['>',0]])->field('count(id) as count')->find();
        $data = [['value'=>$dan_count['count'],'name'=>"单方订单"],['value'=>$shuang_count['count'],'name'=>"双方订单"]];
        return $data;

    }
    public function report_three(){
        $get = $this->request->get();
        $start_time = $get['start_time'];
        $end_time   = $get['end_time'] .' 23:59:59';

        // $start_time = '2019-07-01';
        // $end_time   = '2019-07-29 23:59:59';

        $no_send_count  =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0],['issend','<>',2])->field('count(id) as count')->find();
        $send_count     =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'issend'=>2])->field('count(id) as count')->find();
        $quxiao_count   =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'status'=>6])->field('count(id) as count')->find();
        $data = [['value'=>$no_send_count['count'],'name'=>"未收货"],['value'=>$send_count['count'],'name'=>"已收货"],['value'=>$quxiao_count['count'],'name'=>"取消订单"]];
        return $data;

    }
    public function report_four(){
        $get = $this->request->get();
        $start_time = $get['start_time'];
        $end_time   = $get['end_time'] .' 23:59:59';

        // $start_time = '2019-07-01';
        // $end_time   = '2019-07-29 23:59:59';

        $list  =  db()->table('zk_order o')
            ->join('zk_company c','o.from_company_id = c.id')
            ->where('o.addtime','BETWEEN',[$start_time,$end_time])
            ->where(['o.isdel'=>0])
            ->group('c.type')
            ->field('count(o.id) as num,c.type')
            ->select();
        $data = []; 
        foreach($list as $key => $value){
            if($value['type'] == 1){
                $data[$key]['name'] = '中国国际批发市场';
            }elseif($value['type'] == 2){
                $data[$key]['name'] = '中国库存市场';
            }elseif($value['type'] == 3){
                $data[$key]['name'] = '境内企业';
            }elseif($value['type'] == 4){
                $data[$key]['name'] = '境外企业';
            }elseif($value['type'] ==5){
                $data[$key]['name'] = '在华外(合)资企业';
            }
            $data[$key]['value'] = $value['num'];

        }

        return $data;

    }
    public function report_five(){
        $get = $this->request->get();
        $start_time = $get['start_time'];
        $end_time   = $get['end_time'] .' 23:59:59';

        // $start_time = '2019-07-01';
        // $end_time   = '2019-07-29 23:59:59';

        $order_count  =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0])->field('count(id) as count')->find();
        $pick_count   =  db()->table('zk_pick')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0])->field('count(id) as count')->find();
        $stock_count  =  db()->table('zk_stock')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0])->field('count(id) as count')->find();

        $upload_pick_count  =  db()->table('zk_pick')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'pics'=>['<>','null']])->field('count(id) as count')->find();

        return [['value'=>$order_count['count'],'name'=>'创建订单'],['value'=>$pick_count['count'],'name'=>'创建提单'],['value'=>$stock_count['count'],'name'=>'创建仓库单'],['value'=>$upload_pick_count['count'],'name'=>'上传提单']];

    }
    public function report_six(){
        $get = $this->request->get();
        $start_time = $get['start_time'];
        $end_time   = $get['end_time'] .' 23:59:59';

        // $start_time = '2019-07-01';
        // $end_time   = '2019-07-29 23:59:59';

        $buy_count  =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'buy_sell'=>1])->field('count(id) as count')->find();
        $sell_count  =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'buy_sell'=>2])->field('count(id) as count')->find();
        $data = [['value'=>$buy_count['count'],'name'=>"买方订单"],['value'=>$sell_count['count'],'name'=>"卖方订单"]];
        return $data;

    }
    public function report_seven(){
        $get = $this->request->get();
        $start_time = $get['start_time'];
        $end_time   = $get['end_time'] .' 23:59:59';

        // $start_time = '2019-07-01';
        // $end_time   = '2019-07-29 23:59:59';

        //已经评价
        $yi_count  =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'buy_score'=>2,'sell_score'=>2])->field('count(id) as count')->find();
        //未评价
        $wei_count  =  db()->table('zk_order')->where('addtime','BETWEEN',[$start_time,$end_time])->where(['isdel'=>0,'buy_score'=>['<>',2],'sell_score'=>['<>',2]])->field('count(id) as count')->find();
        $data = [['value'=>$yi_count['count'],'name'=>"已评价"],['value'=>$wei_count['count'],'name'=>"未评价"]];
        return $data;

    }


}
