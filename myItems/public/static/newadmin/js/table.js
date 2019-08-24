var Table = {
    defaults:{
        method: "get",  //使用get请求到服务器获取数据
        toolbar: ".toolbar", //工具栏
        search: true, //是否启用快速搜索
        showToggle: true,
        showColumns: true,
        striped: true,  //表格显示条纹
        pagination: true, //启动分页
        pageSize: 10,  //每页显示的记录数
        pageNumber:1, //当前第几页
        pageList: [5, 10, 15, 20, 25],  //记录数可选列表
        sidePagination: "server", //表示服务端请求
        paginationFirstText: "首页",
        paginationPreText: "上一页",
        paginationNextText: "下一页",
        paginationLastText: "尾页",
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            var param = {
                pageNumber: params.pageNumber,
                pageSize: params.pageSize,
                // selectedValue:$('#banner_id').val()
            };
            return param;
        },
        onLoadSuccess: function(res){  //加载成功时执行
            if(res.code === -1 ){
                layer.msg(res.msg.msg, {time : 1000});
            }else{
                layer.msg('加载成功', {time : 1000});
            }

        },
        onLoadError: function(){  //加载失败时执行
            layer.msg("加载数据失败");
        },
        responseHandler: function(res) {
            return {
                "total": res.msg.total,//总页数
                "rows": res.msg.rows   //数据
            };
        },
    },
    events: {
        operate: {
            'click .view': function (e, value, row, index) {
                info = JSON.stringify(row);
                swal('You click view icon, row: ', info);
            },
            'click .edit': function (e, value, row, index) {
                e.stopPropagation();
                e.preventDefault();
                var table = $(this).closest('table');
                var options = table.bootstrapTable('getOptions');
                var url = options.extend.edit_url;
                url = url+'/?ids='+row['id'];
                Fast.api.open(url, '编辑', $(this).data() || {});
            },
            'click .remove': function (e, value, row, index) {
                e.stopPropagation();
                e.preventDefault();
                var that = this;
                var top = $(that).offset().top - $(window).scrollTop();
                var left = $(that).offset().left - $(window).scrollLeft() - 260;
                if (top + 154 > $(window).height()) {
                    top = top - 154;
                }
                if ($(window).width() < 480) {
                    top = left = undefined;
                }
                Layer.confirm(
                    '你确定要删除此项?',
                    {icon: 3, title: '警告', offset: [top, left], shadeClose: true},
                    function (index) {
                        var table = $(that).closest('table');
                        // var options = table.bootstrapTable('getOptions');
                        Table.multi("delete", row['id'], table, that);
                        Layer.close(index);
                    }
                );
            }
        },
        operate2: {
            'click .remove': function (e, value, row, index) {
                console.log(row);
                e.stopPropagation();
                e.preventDefault();
                var that = this;
                var top = $(that).offset().top - $(window).scrollTop();
                var left = $(that).offset().left - $(window).scrollLeft() - 260;
                if (top + 154 > $(window).height()) {
                    top = top - 154;
                }
                if ($(window).width() < 480) {
                    top = left = undefined;
                }
                Layer.confirm(
                    '你确定要删除此项?',
                    {icon: 3, title: '警告', offset: [top, left], shadeClose: true},
                    function (index) {
                        var table = $(that).closest('table');
                        // var options = table.bootstrapTable('getOptions');
                        Table.multi("delete", row['id'], table, that);
                        Layer.close(index);
                    }
                );
            }
        }
    },
    multi: function (action, ids, table, element) {
        var options = table.bootstrapTable('getOptions');
        var ids = ($.isArray(ids) ? ids.join(",") : ids);
        var url = action == "delete" ? options.extend.del_url : options.extend.multi_url;
        $.ajax({
            type:'post',
            url:url,
            data:{'params':ids},
            success:function(res){
                if(res.code == 1){
                    layer.msg(res.msg, {time : 1000});
                    table.bootstrapTable('refresh');
                }else{
                    layer.msg(res.msg, {time : 1000});
                    table.bootstrapTable('refresh');
                }
            },
            error:function(res){
                layer.msg(非法操作, {time : 1000});
                table.bootstrapTable('refresh');
            }
        });
    },
    formatter:{
        set_value:function(value, row, index){
            if(($.fn.bootstrapTable.defaults.ids) && $.inArray(row.id,$.fn.bootstrapTable.defaults.ids) != -1){
                    return {
                        checked: true
                    };
            }
        },
        right_thumb:function(value, row, index){
            if(value == null){
                return '<a href="" target="_blank"><img src="" alt="" style="max-height:90px;max-width:120px"></a>';
            }else{
                return '<a href="' + value.url + '" target="_blank"><img src="' + value.url + '" alt="" style="max-height:90px;max-width:120px"></a>';
            }
        },
        status:function(value, row, index){
            return (value == 1)?'启用':'禁用'
        },
        theme_type:function(value, row, index){
            //如果主题选项多的话这里 就会出问题了
            return (1==value)?"美图":"产品"
        },
        img_category:function(value, row, index){
            if(!value){
                return "无分类";
            }
            return value.name
        },
        //属性结构菜单 用于图片分类
        tree_menu: function(value, row, index){
            console.log(row)
            var str = '';
            for(var i = 0; i < row.deep; i++) {
                //do somethingstr
                str += '--';
            }
            str += value;
            return str;
        },
        thumb: function (value, row, index) {

            return '<a href="' + row.url + '" target="_blank"><img src="' + row.url + '" alt="" style="max-height:90px;max-width:120px"></a>';
        },
        product_thumb:function(value, row, index){
            if(value == null){
                return '<a href="" target="_blank"><img src="" alt="" style="max-height:90px;max-width:120px"></a>';
            }else{
                return '<a href="' + value + '" target="_blank"><img src="' + value + '" alt="" style="max-height:90px;max-width:120px"></a>';
            }
        },
        product_category:function(value, row, index){
            if(!value[1]){
                return '没有分类';
            }else{
                return value[1];
            }
        },
        appoint_thumb:function(value, row, index){
            console.log(value);
                if(value == null){
                    return '<a href="" target="_blank"><img src="" alt="" style="max-height:90px;max-width:120px"></a>';
                }else{
                    return '<a href="' + value.url + '" target="_blank"><img src="' + value.url + '" alt="" style="max-height:90px;max-width:120px"></a>';
                }

            },
        url: function (value, row, index) {
            return '<a href="' + row.fullurl + '" target="_blank" class="label bg-green">' + value + '</a>';
        },
        operate: function (value, row, index) {
            return [
                '<a rel="tooltip" title="View" class="btn btn-simple btn-info btn-icon table-action view" href="javascript:void(0)">',
                '<i class="fa fa-institution"></i> 查看',
                '</a>',
                '<a rel="tooltip" title="Edit" data-id = "'+row.id+'" class="btn btn-simple btn-warning btn-icon table-action edit" href="javascript:void(0)">',
                '<i class="fa fa-edit"></i> 编辑',
                '</a>',
                '<a rel="tooltip" title="Remove" class="btn btn-simple btn-danger btn-icon table-action remove" href="javascript:void(0)">',
                '<i class="fa fa-trash-o"></i> 删除',
                '</a>'
            ].join('');
        },
        operate1: function (value, row, index) {
            return [
                '<a rel="tooltip" title="View" class="btn btn-simple btn-info btn-icon table-action btn-chooseone view" href="javascript:void(0)">',
                '<i class="fa fa-institution"></i> 选择',
                '</a>',
            ].join('');
        }
    },
    config: {
        toolbar: '.toolbar',
        refreshbtn: '.btn-refresh',
        addbtn: '.btn-add',
        editbtn: '.btn-edit',
        delbtn: '.btn-del',
        importbtn: '.btn-import',
        multibtn: '.btn-multi',
        disabledbtn: '.btn-disabled',
        editonebtn: '.btn-editone',
        dragsortfield: 'weigh',
    },
    init: function (defaults, columnDefaults, locales) {
        defaults = defaults ? defaults : {};
        columnDefaults = columnDefaults ? columnDefaults : {};
        locales = locales ? locales : {};
        // 如果是iOS设备则启用卡片视图
        if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
            Table.defaults.cardView = true;
        }
        // 写入bootstrap-table默认配置
        $.extend(true, $.fn.bootstrapTable.defaults, Table.defaults, defaults);
        // 写入bootstrap-table column配置
        $.extend($.fn.bootstrapTable.columnDefaults, Table.columnDefaults, columnDefaults);
    },
    bindEvent:function(table){
        //Bootstrap-table的父元素,包含table,toolbar,pagnation
        var parenttable = table.closest('.bootstrap-table');
        //Bootstrap-table配置
        var options = table.bootstrapTable('getOptions');
        //Bootstrap操作区
        var toolbar = $(options.toolbar, parenttable);

        table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function () {
            var ids = Table.selectedids(table);
            $(Table.config.disabledbtn, toolbar).toggleClass('disabled', !ids.length);
        });
        //绑定刷新事件
        $(toolbar).on('click',Table.config.refreshbtn,function(){
            table.bootstrapTable('refresh');
        });
        //绑定添加事件
        $(toolbar).on('click', Table.config.addbtn, function () {

            var ids = Table.selectedids(table);
            var url = options.extend.add_url;
            if (url.indexOf("{ids}") !== -1) {
                url = Table.api.replaceurl(url, {ids: ids.length > 0 ? ids.join(",") : 0}, table);
            }
            console.log(url);
            Fast.api.open(url, '添加', $(this).data() || {});
        });

        // 批量删除按钮事件
        $(toolbar).on('click', Table.config.delbtn, function () {
            var that = this;
            var ids = Table.selectedids(table);
            Layer.confirm(
                '你确定要删除这'+ids.length+'个条目',
                {icon: 3, title: '警告', offset: 0, shadeClose: true},
                function (index) {
                    Table.multi("delete", ids, table, that);
                    Layer.close(index);
                }
            );
        });
    },
    selectedids: function (table) {
        var options = table.bootstrapTable('getOptions');
        if (options.templateView) {
            return $.map($("input[data-id][name='checkbox']:checked"), function (dom) {
                return $(dom).data("id");
            });
        } else {
            return $.map(table.bootstrapTable('getSelections'), function (row) {
                return row['id'];
            });
        }
    },
};