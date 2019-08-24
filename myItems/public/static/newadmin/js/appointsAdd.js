Date.prototype.format = function(fmt) {
    var o = {
        "M+" : this.getMonth()+1,                 //月份
        "d+" : this.getDate(),                    //日
        "h+" : this.getHours(),                   //小时
        "m+" : this.getMinutes(),                 //分
        "s+" : this.getSeconds(),                 //秒
        "q+" : Math.floor((this.getMonth()+3)/3), //季度
        "S"  : this.getMilliseconds()             //毫秒
    };
    if(/(y+)/.test(fmt)) {
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
    for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
    return fmt;
}

layui.use(['form','layer','laydate','table','laytpl','layedit','upload'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laydate2 = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table,
        upload = layui.upload;

    form.verify({
        before_time: function(value, item){ //value：表单的值、item：表单的DOM对象
            return "hahahh";
        }
        //我们既支持上述函数式的方式，也支持下述数组的形式
        //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
        // ,pass: [
        //     /^[\S]{6,12}$/
        //     ,'密码必须6到12位，且不能出现空格'
        // ]
    });

    var layedit = layui.layedit;
    var edit_index = layedit.build('desc'); //建立编辑器
    //layedit.setContent(edit_index,$("#desc").val());

    form.on("submit(addAppoint)",function(data){
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var exclude_date = [];
        $(".add-exclude-date ").each(function(index,elem){
            exclude_date.push($(elem).val());
        });
        if(exclude_date.length > 0){//如果存在排除日期那就提取出来
            exclude_date = exclude_date.join(",");
        }
        if(exclude_date.length==1){
            exclude = exclude_date.replace(',','');
        }
        // 实际使用时的提交信息
        $.post("add",{
            id:$("input[name=id]").val(),
            title:$("input[name=title]").val(),
            image_id:$("#image_id").val(),
            description:layedit.getText(edit_index),
            appoint_time_list:$("input[name=srvtime]").val(),
            exclude_date:exclude_date,
            // notify_cs_type:$("input[name=notify_cs_type][checked]").val(),
            begin_time:$("#begin_time").val(),
            end_time:$("#end_time").val(),
            appoint_days:$("#appoint_days").val(),
            notify_email:$("#notify_email").val(),
            cs_templateid:$("#cs_templateid").val(),
            fans_templateid:$("#fans_templateid").val(),
            pre_total:$("#pre_total").val(),
            day_total:$("#day_total").val(),
            edit:$("input[name=edit][checked='checked']").val(),
            code:$("input[name=code][checked='checked']").val(),
            follow:$("input[name=follow][checked='checked']").val(),
            isshow:$("input[name=isshow][checked='checked']").val()
        },function(res){
            if(res.code == 5000003){
                setTimeout(function(){
                    top.layer.msg(res.msg);
                    top.layer.close(index);
                    layer.closeAll("iframe");
                    //刷新父页面
                    window.location.reload();
                },2000);
            }else if(res.code == 1){
                setTimeout(function(){
                    top.layer.msg("参数添加成功！");
                    top.layer.close(index);
                    layer.closeAll("iframe");
                    //刷新父页面
                    window.location.reload();
                },2000);
            }else{
                top.layer.close(index);
                top.layer.msg("更新失败");
            }
            return false;
        })
        return false;
    })
    $('#choose').on('click',function () {
            var that = this;
            Fast.api.open('/index.php/admin/attachment/select', '选择图片', {
                callback: function (data) {
                    var button = $("#" + $(that).attr("id"));
                    var input_id = $(button).data("input-id") ? $(button).data("input-id") : "";
                    $("#" + input_id).val(data.url).trigger("change");
                    $("#image_id").val(data.id).trigger("change");
                    $(".demo2").attr('src',data.url);
                    }
                })
            return false;
    });

    laydate.render({
        elem: '#end_time'
    });

    laydate.render({
        elem: '#begin_time'
    });

    //搜索【此功能需要后台配合，所以暂时没有动态效果演示】
    $(".search_btn").on("click",function(){
        if($(".searchVal").val() != ''){
            table.reload("newsListTable",{
                page: {
                    curr: 1 //重新从第 1 页开始
                },
                where: {
                    key: $(".searchVal").val()  //搜索的关键字
                }
            })
        }else{
            layer.msg("请输入搜索的内容");
        }
    });

    function TimeInit(input){
        this.input=input;
        this.value=this.unserialize(input.value);
        this.box =$('<div class="layui-col-md12 layui-col-xs12"><label class="layui-form-label">预约时间</label>' +
            '<div class="layui-input-block layui-form-pane weekset"></div>' +
            '<div class="layui-input-block layui-form-pane timelist" style="margin-top:10px;"></div>' +
            '<button class="layui-input-block layui-btn layui-btn-sm btn-add" style="margin-top:10px;"><i class="layui-icon"></i></button>' +
            '</div>');
        $(this.input).before(this.box);
        this.weekset=this.box.find('.weekset');
        this.listbox=this.box.find('.timelist');
        this.addbtn=this.box.find('.btn-add');
        this.init();
    }

    TimeInit.prototype.init=function(){
        var self=this;
        //添加新的一行
        this.addbtn.click(function(){
            self.addrow();
            return false
        });
        //设置周
        $(this.weekset).append('<div class="layui-input-inline" >');
        $(this.weekset).append('<input type="checkbox" lay-filter="checkweek" value="1" title="周一"/> ');
        $(this.weekset).append('<input type="checkbox" lay-filter="checkweek" value="2" title="周二"/> ');
        $(this.weekset).append('<input type="checkbox" lay-filter="checkweek" value="3" title="周三"/> ');
        $(this.weekset).append('<input type="checkbox" lay-filter="checkweek" value="4" title="周四"/> ');
        $(this.weekset).append('<input type="checkbox" lay-filter="checkweek" value="5" title="周五"/> ');
        $(this.weekset).append('<input type="checkbox" lay-filter="checkweek" value="6" title="周六"/> ');
        $(this.weekset).append('<input type="checkbox" lay-filter="checkweek" value="0" title="周日"/> ');
        $(this.weekset).append('</div>');

        //渲染的作用
        for(var i=0;i<this.value.weekset.length;i++){
            $(this.weekset).find('[value='+this.value.weekset[i]+']').attr('checked',true);
        }

        //事件监听必须改用layui的机制老的作废
        form.on('checkbox(checkweek)', function(data){
            var input = data.othis.prev('input');
            var checked = input.attr('checked');
            if(checked == undefined){//未选中状态
                input.attr('checked',true);
            }else{
                input.attr('checked',false);
            }
            self.setValue();
        });

        //设置时间段
        for(var i=0;i<this.value.times.length;i++){
            this.addrow(this.value.times[i]);
        }

        //编辑的时候设置排除日期
        if(typeof exclude_date != undefined){
            exclude_date = exclude_date.split(",");
            var index = $('.add-exclude-date').length;
            var exclude = "";
            for(var i in exclude_date){
                exclude += '<div class="layui-input-block" style="margin-top:10px"><input readonly="readonly" type="text" value = "'+exclude_date[i]+'" name="exclude_date[]" style="width:50%" data-index = "'+index+'" id="add-exclude-'+index+'" class="layui-input add-exclude-date layui-input-inline">' +
                    '<span style="float:right;"><button class="layui-btn layui-btn-danger remove-exclude">删除</button></span></div>'
            }
            $('.add-exclude').before(exclude);
        }


        form.render()
    }

    TimeInit.prototype.addrow=function(val){
        var row =$('<li class="input-group" style="margin:10px 0">' +
            '<span class="input-group-addon">时段</span>' +
            '<input type="text" placeholder="0:00" class="form-control starttime" value="0:00" style="background-color:#fff;" readonly/>' +
            '<span class="input-group-addon">-</span>' +
            '<input type="text" placeholder="1:00" class="form-control endtime" value="1:00" style="background-color:#fff;" readonly/>' +
            '<span class="input-group-addon">标题</span>' +
            '<input type="text" placeholder="时间段标题" class="form-control item" value=""/>' +
            '<span class="input-group-addon">可预约</span>' +
            '<input type="number" placeholder="5" class="form-control number" value="5"/>' +
            '<span class="input-group-addon">人</span>' +
            '<span class="input-group-btn">' +
            '<button class="btn btn-danger remove" type="button">删除</button>' +
            '</span>' +
            '</li>');
        this.listbox.append(row);
        var self=this;
        row.find('.remove').click(function(){
            if(confirm('确定删除该时段?')){
                row.remove();
                self.setValue();
            }
        })
        row.find('focus').blur(function(){
            $(this).data('oldvalue',this.value);
        })
        row.find('input').blur(function(){
            if(this.value != this.oldvalue)self.setValue();
        })
        if(val){
            row.find('.starttime').val(val.start);
            row.find('.endtime').val(val.end);
            row.find('.item').val(val.item);
            row.find('.number').val(val.number);
        }

            $('.starttime').clockpicker({autoclose: true});
            $('.endtime').clockpicker({autoclose: true});

    }

    TimeInit.prototype.setValue=function(){
        var value={weekset:[],times:[]};
        this.weekset.find('input:checked').each(function(){
            value.weekset.push(this.value);
        });

        this.listbox.find('li').each(function(){
            var row={};
            row.start=$(this).find('.starttime').val();
            row.end=$(this).find('.endtime').val();
            row.item=$(this).find('.item').val();
            row.number=$(this).find('.number').val();
            if(row.start && row.end && row.number){
                value.times.push(row);
            }
        });
         this.value=value;
        $(this.input).val(this.serialize(value));
    }

    TimeInit.prototype.unserialize=function(val){
        if(val){
            var data=eval('('+val+')');
            if(data)return data;
        }
        //默认值这里的数据如果是edit的话要从数据库里面取出s

        data = {
            weekset:[1,2,3,4,5],
            times:[
                {start:'8:00',end:'9:00',number:1},
                {start:'9:00',end:'10:00',number:1},
                {start:'10:00',end:'11:00',number:1},
                {start:'11:00',end:'12:00',number:1},
                {start:'13:30',end:'14:30',number:1},
                {start:'14:30',end:'15:30',number:1},
                {start:'15:30',end:'16:30',number:1},
                {start:'16:30',end:'17:30',number:1}
            ]
        };
        if(typeof time_list == undefined){
            data = JSON.parse(time_list)
        }
        this.input.value=this.serialize(data);
        return data;
    }

    TimeInit.prototype.serialize=function(val){
        return JSON.stringify(val);
    }
    new TimeInit($('.srvtime')[0]);

    $('.add-exclude').on('click',function(data){
        var index = $('.add-exclude-date').length;
        exclude = $('<div class="layui-input-block" style="margin-top:10px"><input readonly="readonly" value="" type="text" name="exclude_date[]" style="width:50%" data-index = "'+index+'" id="add-exclude-'+index+'" class="layui-input add-exclude-date layui-input-inline">' +
            '<span style="float:right;"><button class="layui-btn layui-btn-danger remove-exclude">删除</button></span></div>')

        $(this).before(exclude);

        exclude.find('.remove-exclude').click(function(){
            $(this).parent().parent().remove()
            return false
        })

        exclude.find('.add-exclude-date').on('click',function(){
            //获取这个元素的index
            var index = $(this)[0].dataset['index']
            showtime(index)
            return false
        })

        form.render();
        return false;
    })

    function showtime(id) {
        var option = {
            lang: "zh",
            step: 5,
            timepicker: false,
            closeOnDateSelect: true,
            format: "Y-m-d",
            onChangeDateTime: function(dp, $input){
                var date = new Date(dp).format("yyyy-MM-dd");
                $input.val(date).trigger('change')
                form.render();
            }
        };
        $("#add-exclude-" + id).datetimepicker(option);
        form.render();
    }

    form.on('radio', function(data){
        // $("input[name=notify_type][value=]]")data.elem.checked = true;
        $(data.elem).siblings("input").attr('checked',false);
        $(data.elem).attr('checked',true);
        form.render();
    });

    var uploadInst = upload.render({
        elem: '#test2'
        ,url: '/index.php/admin/ajax/upload/'
        ,before: function(obj){
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('.demo2').attr('src', result); //图片链接（base64）
            });
        }
        ,done: function(res){
            //如果上传失败
            if(res.code < 0){
                return layer.msg('上传失败');
            }else{
                return layer.msg('上传成功');
            }
            //上传成功
        }
        ,error: function(){
            //演示失败状态，并实现重传
            var demoText = $('#demoText');
            demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
            demoText.find('.demo-reload').on('click', function(){
                uploadInst.upload();
            });
        }
    });


})