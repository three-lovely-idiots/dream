layui.use(['form','layer','laydate','table','laytpl','layedit','upload'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
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
    form.on("submit(addStyle)",function(data){
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        // 实际使用时的提交信息
        $.post("addstyle",{
            id:$("input[name=id]").val(),
            title:$("input[name=title]").val(),
            main_id:$("#main_id").val(),
            img_id:$("#image_id").val()
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

    form.on("submit(addMainStyle)",function(data){
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        // 实际使用时的提交信息
        $.post("addmain",{
            id:$("input[name=id]").val(),
            title:$("input[name=title]").val(),
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

    form.on("select(style_type)",function(data){
        data.othis.val(data.value);
        form.render();
    });
})
