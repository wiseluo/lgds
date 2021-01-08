define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/studentsignlesson/index?course_id='+ Fast.api.query('course_id') +'&user_id='+ Fast.api.query('user_id'),
                    table: 'lessonsign',
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
                    fileName: '学员签到课时_' + Moment().format("YYYY-MM-DD"),
                    ignoreColumn: ['operate'] //默认不导出第一列(checkbox)与操作(operate)列
                },
                columns: [
                    [
                        {field: 'sort', title: __('Sort')},
                        {field: 'lesson_no', title: __('Lesson_no')},
                        {field: 'lesson_name', title: __('Lesson_name')},
                        {field: 'start_time', title: __('Start_time'), operate:false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"},
                        {field: 'end_time', title: __('End_time'), operate:false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"},
                        {field: 'signed_status', title: __('Signed_status')},
                        {field: 'signed_time', title: __('Signed_time'), operate:false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"},
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