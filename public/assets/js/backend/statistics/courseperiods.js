define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/courseperiods/index',
                    table: 'courseperiods',
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
                    fileName: '课程期数统计_' + Moment().format("YYYY-MM-DD"),
                    ignoreColumn: ['operate'] //默认不导出第一列(checkbox)与操作(operate)列
                },
                columns: [
                    [
                        {field: 'periods_number', title: __('Periods_number'),operate: 'BETWEEN', addclass:'datetimepicker',data:'autocomplete="off" data-date-format="YYYYMM"'},
                        {field: 'course_count', title: __('Course_count'),operate:false},
                        {field: 'quota_sum', title: __('Quota_sum'),operate:false},
                        {field: 'used_quota_sum', title: __('Used_quota_sum'),operate:false},
                        {field: 'enroll_rate', title: __('Enroll_rate'),operate:false},
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