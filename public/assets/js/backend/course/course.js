define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'course/course/index' + location.search,
                    add_url: 'course/course/add',
                    edit_url: 'course/course/edit',
                    del_url: 'course/course/del',
                    table: 'course',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false, //禁用默认搜索
                commonSearch: true, //启用普通表单搜索
                searchFormVisible: true, //可以控制是否默认显示搜索单表,false则隐藏,默认为false
                columns: [
                    [
                        {checkbox: true},
                        {field: 'periods_number', title: __('Periods_number')},
                        {field: 'type_name', title: __('Type_name'),operate:false},
                        {field: 'name', title: __('Name')},
                        {field: 'teachername', title: __('Teachername')},
                        {field: 'crowd', title: __('Crowd'),operate:false},
                        {field: 'begin_date', title: __('begin_date'),operate:false},
                        {field: 'class_number', title: __('Class_number'),operate:false},
                        {field: 'class_time', title: __('class_time'),operate:false},
                        {field: 'class_location', title: __('class_location'),operate:false},
                        {field: 'start_enroll_date', title: __('Start_enroll_date'),operate:false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"},
                        {field: 'quota', title: __('Quota'),operate:false},
                        {field: 'used_quota', title: __('Used_quota'),operate:false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'detail',
                                    text: __('Detail'),
                                    title: __('Detail'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-angellist',
                                    url: 'course/course/detail',
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return true;
                                    }
                                },
                                {
                                    name: 'courselesson',
                                    text: __('Courselesson'),
                                    title: __('Courselesson'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-angellist',
                                    url: 'course/courselesson/index',
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return true;
                                    }
                                },
                                {
                                    name: 'confirmonshelf',
                                    text: __('Confirmonshelf'),
                                    title: __('Confirmonshelf'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'course/course/confirmonshelf',
                                    confirm: '确认上架吗？',
                                    success: function (data, ret) {
                                        //Layer.alert(ret.msg + ",返回数据：" + JSON.stringify(data));
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                        //刷新当前表格
                                        table.bootstrapTable('refresh', {});
                                        return true;
                                    },
                                    visible: function (row) {
                                       //返回true时按钮显示,返回false隐藏
                                       console.log(row.status)
                                       if (row.status == 1){
                                          return true;
                                       }
                                       return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'confirmoffshelf',
                                    text: __('Confirmoffshelf'),
                                    title: __('Confirmoffshelf'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'course/course/confirmoffshelf',
                                    confirm: '确认下架吗？',
                                    success: function (data, ret) {
                                        //Layer.alert(ret.msg + ",返回数据：" + JSON.stringify(data));
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                        //刷新当前表格
                                        table.bootstrapTable('refresh', {});
                                        return true;
                                    },
                                    visible: function (row) {
                                       //返回true时按钮显示,返回false隐藏
                                       console.log(row.status)
                                       if (row.status == 0){
                                          return true;
                                       }
                                       return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                }
                            ]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'course/course/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'course/course/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'course/course/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        detail: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'course/courselesson/index/ids/' + Fast.api.query('ids'),
                pk: 'id',
                sortName: 'id',
                search: false,
                pagination: false,
                columns: [
                    [
                        {field: 'sort', title: __('Sort')},
                        {field: 'lesson_no', title: __('Lesson_no'), operate: 'LIKE'},
                        {field: 'lesson_name', title: __('Lesson_name'), operate: 'LIKE'},
                        {field: 'start_time', title: __('Start_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"},
                        {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"}                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };

    //添加界面-教师列表弹窗
    $(document).on('click', '#add-form #c-teachername', function () {
        var url = 'teacher/teacherselect'
        Fast.api.open(url, __('Selectteacher'), {
            callback:function(data){
                //在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传数据
                $("#c-teachername").val(data[1]);
                $("#c-teacherid").val(data[0]);
            }
        }); //数据在teacher.js中的teacherselect获取
    });
    //编辑界面-教师列表弹窗
    $(document).on('click', '#edit-form #c-teachername', function () {
        var url = 'teacher/teacherselect'
        Fast.api.open(url, __('Selectteacher'), {
            callback:function(data){
                $("#c-teachername").val(data[1]);
                $("#c-teacherid").val(data[0]);
            }
        });
    });

    return Controller;
});