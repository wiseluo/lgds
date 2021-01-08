define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    multi_url: 'user/user/multi',
                    assignteacher_url: 'user/user/assignteacher',
                    table: 'user',
                }
            });

            //设为教师
            $(document).on('click', '#toolbar .btn-assignteacher', function () {
                var that = this;
                var ids = Table.api.selectedids(table).join(",");
                var url = $.fn.bootstrapTable.defaults.extend.assignteacher_url + '?user_ids='+ids
                Fast.api.open(url, __('Assignsalesman'), $(that).data() || {});
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'user.id',
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false, sortable: true},
                        {field: 'nickname', title: __('Nickname'), operate: 'LIKE'},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
						{field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'gender', title: __('Gender'), searchList: {"0":__('Gender 0'),"1":__('Gender 1'),"2":__('Gender 2')}, formatter: Table.api.formatter.normal},
                        {field: 'avatar', title: __('Avatar'), formatter: Table.api.formatter.image, operate: false},
                        //{field: 'level', title: __('Level'), operate: 'BETWEEN', sortable: true},
                        //{field: 'gender', title: __('Gender'), visible: false, searchList: {1: __('Male'), 0: __('Female')}},
                        //{field: 'score', title: __('Score'), operate: 'BETWEEN', sortable: true},
                        //{field: 'successions', title: __('Successions'), visible: false, operate: 'BETWEEN', sortable: true},
                        //{field: 'maxsuccessions', title: __('Maxsuccessions'), visible: false, operate: 'BETWEEN', sortable: true},
                        //{field: 'logintime', title: __('Logintime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        //{field: 'loginip', title: __('Loginip'), formatter: Table.api.formatter.search},
                        {field: 'work_unit', title: __('Work_unit'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Jointime'), operate: false, formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD HH:mm", addclass: 'datetimerange', sortable: true},
                        //{field: 'joinip', title: __('Joinip'), formatter: Table.api.formatter.search},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'assignteacher',
                                    text: __('assignteacher'),
                                    title: __('assignteacher'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'user/user/assignteacher',
                                    confirm: '确认将该用户设为教师吗？',
                                    success: function (data, ret) {
                                        //Layer.alert(ret.msg + ",返回数据：" + JSON.stringify(data));
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
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
        userselect: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/userselect',
                    table: 'userselect',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                singleSelect:true,
                search: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'nickname', title: __('Nickname'), operate: false},
                        {field: 'username', title: __('username'), operate: 'LIKE'},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                    ]
                ],
                search: false,
                pagination: false
            });
            //选择学员
            $(document).on('click', '#select_user_submit_btn', function () {
                let user_id = ''
                let username = ''
                if($('.select_user_table tbody tr.selected').length != 1) {
                    alert('请选择一个学员')
                    return false
                } else {
                    user_id = $('.select_user_table tbody tr.selected').find('td').eq(1).text();
                    username = $('.select_user_table tbody tr.selected').find('td').eq(3).text();
                    phone = $('.select_user_table tbody tr.selected').find('td').eq(4).text();
                }
                //回传数据
                Fast.api.close([user_id, username,phone]);
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
    };
    return Controller;
});