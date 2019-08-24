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




    form.on("submit(addProduct)",function(data){
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:2000,shade:0.8});

        // 实际使用时的提交信息
        $.post("add",{data:data.field},function(res){
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
                console.log(data)
                var arr = [];
                var dataset = $(that)[0].dataset
                var main_img_url = $('#'+dataset.inputUrl)
                var img_id = $('#'+dataset.inputId)
                var img_url = $('#'+dataset.image)
                arr = data.url.split('images')
                main_img_url.val(arr[1]).trigger("change");
                img_id.val(data.id).trigger("change");
                img_url.attr('src',data.url);
            }
        })
        return false;
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

    form.on("select(cats_type)",function(data){
        data.othis.val(data.value);
        form.render();
    });
})
