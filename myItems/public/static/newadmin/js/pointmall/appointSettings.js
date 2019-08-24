layui.use(['form','layer'],function(){
    var form = layui.form
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery;


    form.on('radio', function(data){
        // $("input[name=notify_type][value=]]")data.elem.checked = true;
        $(data.elem).siblings("input").attr('checked',false);
        $(data.elem).attr('checked',true);
        form.render();
    });

    form.verify({
        limit_time: function(value, item){ //value：表单的值、item：表单的DOM对象
            return false;
        },
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


    form.on("submit(addBanneritem)",function(data){
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        // 实际使用时的提交信息
        $.post("settings",{
               id:$("input[name=id]").val(),
               limit_time:$("#limit_time").val(),
               before_time:$("#before_time").val(),
               notify_type:$("input[name=notify_type][checked]").val(),
               notify_cs_type:$("input[name=notify_cs_type][checked]").val()
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



})