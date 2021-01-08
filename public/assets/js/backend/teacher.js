define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'teacher/index' + location.search,
                    add_url: 'teacher/add',
                    edit_url: 'teacher/edit',
                    del_url: 'teacher/del',
                    multi_url: 'teacher/multi',
                    table: 'teacher',
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
                        {field: 'phone', title: __('Phone')},
                        {field: 'nickname', title: __('Nickname'), operate: 'LIKE'},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'avatar', title: __('Avatar'),operate:false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'gender', title: __('Gender'), searchList: {"0":__('Gender 0'),"1":__('Gender 1'),"2":__('Gender 2')}, formatter: Table.api.formatter.normal},
                        {field: 'work_unit', title: __('Work_unit'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm"},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
        },
        //每个控制器都对应一个JS模块，控制器名称和JS中模块名称是一一对应的
        teacherselect: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'teacher/teacherselect',
                    table: 'teacherselect',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                singleSelect:true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'username', title: __('username')}
                    ]
                ],
                search: false,
                pagination: false
            });
            //选择教师
            $(document).on('click', '#select_teacher_submit_btn', function () {
                let teacher_id = ''
                let teacher_name = ''
                if($('.select_teacher_table tbody tr.selected').length != 1) {
                    alert('请选择一个教师')
                    return false
                } else {
                    teacher_id = $('.select_teacher_table tbody tr.selected').find('td').eq(1).text();
                    teacher_name = $('.select_teacher_table tbody tr.selected').find('td').eq(2).text();
                }
                //回传数据
                Fast.api.close([teacher_id, teacher_name]);
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
    };
    return Controller;
});