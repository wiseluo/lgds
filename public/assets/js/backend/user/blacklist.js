define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/blacklist/index',
                    add_url: 'user/blacklist/add',
                    cancel_url: 'user/blacklist/cancel',
                    table: 'user',
                }
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
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
						{field: 'blacklist_date', title: __('Blacklist_date'), operate: false},
                        {field: 'blacklist_remark', title: __('Blacklist_remark'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'cancelblacklist',
                                    text: __('Cancelblacklist'),
                                    title: __('Cancelblacklist'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-angellist',
                                    url: 'user/blacklist/cancel',
                                    confirm: '确认将该用户撤销黑名单吗？',
                                    success: function (data, ret) {
                                        //Layer.alert(ret.msg + ",返回数据：" + JSON.stringify(data));
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                        //刷新当前表格
                                        table.bootstrapTable('refresh', {});
                                        return true;
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
    };

    //添加界面-学员列表弹窗
    $(document).on('click', '#add-form #c-username', function () {
        var url = 'user/user/userselect'
        Fast.api.open(url, __('Selectuser'), {
            callback:function(data){
                //在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传数据
                $("#c-userid").val(data[0]);
                $("#c-username").val(data[1]);
                $("#c-phone").val(data[2]);
            }
        }); //数据在user.js中的userselect获取
    });
    return Controller;
});