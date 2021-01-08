define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/studentsign/index',
                    table: 'studentsign',
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
                exportOptions: {
                    fileName: '学员签到课程_' + Moment().format("YYYY-MM-DD"),
                    ignoreColumn: ['operate'] //默认不导出第一列(checkbox)与操作(operate)列
                },
                columns: [
                    [
                        {field: 'user_id', visible:false,operate:false},
                        {field: 'periods_number', title: __('Periods_number'),operate: 'LIKE'},
                        {field: 'name', title: __('Name'),operate: 'LIKE'},
                        {field: 'username', title: __('Username'),operate: 'LIKE'},
                        {field: 'phone', title: __('Phone')},
                        {field: 'class_number', title: __('Class_number'),operate:false},
                        {field: 'signed_number', title: __('Signed_number'),operate:false},
                        {field: 'unsigned_number', title: __('Unsigned_number'),operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'studentsignlesson',
                                    text: __('Courselession'),
                                    title: __('Courselession'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-angellist',
                                    //url: 'statistics/studentsignlesson/index',
                                    url: function (row){
                                        // row 为行数据
                                        return 'statistics/studentsignlesson/index?course_id='+row.id+'&user_id='+row.user_id;
                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return true;
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});