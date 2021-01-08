define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/coursesignstudent/index?lesson_id='+ Fast.api.query('lesson_id'),
                    table: 'coursesignstudent',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                pagination: false,
                commonSearch: false,
                exportOptions: {
                    fileName: '课时签到学员_' + Moment().format("YYYY-MM-DD"),
                    ignoreColumn: ['operate'] //默认不导出第一列(checkbox)与操作(operate)列
                },
                columns: [
                    [
                        {field: 'username', title: __('Username')},
                        {field: 'phone', title: __('phone'),operate:false},
                        {field: 'gender', title: __('Gender'), searchList: {"0":__('Gender 0'),"1":__('Gender 1'),"2":__('Gender 2')}, formatter: Table.api.formatter.normal},
                        {field: 'work_unit', title: __('Work_unit'),operate:false},
                        {field: 'nickname', title: __('Nickname'),operate:false},
                        {field: 'avatar', title: __('Avatar'), formatter: Table.api.formatter.image, operate: false},
                        
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