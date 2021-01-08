define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/rymuser/index',
                    add_url: 'user/rymuser/add',
                    edit_url: 'user/rymuser/edit',
                    del_url: 'user/rymuser/del',
                    table: 'rymuser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true},
                        {field: 'company.name', title: __('公司'),operate:false},
                        {field: 'company_id', title: __('公司'),searchList: $.getJSON("company/companys/get_list"),visible: false},
                        {field: 'name', title: __('name'), operate: 'LIKE'},
                        {field: 'email', title:  __('邮箱'), operate: 'LIKE'},
                        {field: 'phone', title:  __('电话'), operate: 'LIKE'},
                        {field: 'sex', title: __('性别'), visible: false, searchList: {1: __('男'), 0: __('女')}},
                        {field: 'sex_name', title: __('性别'),operate:false},
                        {field: 'country.country_name', title:  __('国家'),operate:false},
                        {field: 'country', title: __('国家'),searchList: $.getJSON("company/companys/get_country_list"),visible: false},
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
        }
    };
    return Controller;
});