define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'course/courseenroll/index' + location.search,
                    table: 'course_enroll',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'periods_number', title: __('Periods_number'), operate: 'LIKE'},
                        {field: 'course_name', title: __('Course_name'), operate: 'LIKE'},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'gender', title: __('Gender'),operate:false, searchList: {"0":__('Gender 0'),"1":__('Gender 1'),"2":__('Gender 2')}, formatter: Table.api.formatter.normal},
                        {field: 'work_unit', title: __('Work_unit'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm",data:'autocomplete="off"'},
                        {field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm",data:'autocomplete="off"'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'cancelenroll',
                                    text: __('Cancelenroll'),
                                    title: __('Cancelenroll'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'course/courseenroll/cancelenroll',
                                    confirm: '确认取消报名吗？',
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
                                        return true;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'confirmpayment',
                                    text: __('Confirmpayment'),
                                    title: __('Confirmpayment'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'course/courseenroll/confirmpayment',
                                    confirm: '确认已缴吗？',
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
                                },
                                {
                                    name: 'confirmunpaid',
                                    text: __('Confirmunpaid'),
                                    title: __('Confirmunpaid'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'course/courseenroll/confirmunpaid',
                                    confirm: '确认未缴吗？',
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
                                    name: 'confirmreturn',
                                    text: __('Confirmreturn'),
                                    title: __('Confirmreturn'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'course/courseenroll/confirmreturn',
                                    confirm: '确认退还吗？',
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
                                    name: 'confirmnotreturn',
                                    text: __('Confirmnotreturn'),
                                    title: __('Confirmnotreturn'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'course/courseenroll/confirmnotreturn',
                                    confirm: '确认不退吗？',
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
                url: 'course/courseenroll/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'username', title: __('Username')},
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
                                    url: 'course/courseenroll/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'course/courseenroll/destroy',
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});