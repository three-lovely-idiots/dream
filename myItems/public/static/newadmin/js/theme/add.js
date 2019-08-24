layui.use(['form','layer','laydate','table','laytpl','layedit','upload'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        upload = layui.upload;

    var layedit = layui.layedit;
    var edit_index = layedit.build('desc'); //建立编辑器
    layedit.setContent(edit_index,$("#desc").val());

    // function sleep(delay) {
    //     var start = (new Date()).getTime();
    //     while ((new Date()).getTime() - start < delay) {
    //         continue;
    //     }
    // }

    form.on("submit(addTheme)",function(data){
        var post_data = data.field
        delete post_data.file;
        post_data.description = layedit.getText(edit_index)
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8})
        // 实际使用时的提交信息
        $.post("add",post_data,function(res){
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

    $('.choose').on('click',function () {
        var that = this;
        Fast.api.open('/index.php/admin/attachment/select', '选择图片', {
            callback: function (data) {
                var dataset = $(that)[0].dataset
                var input_url = $('#'+dataset.inputUrl)
                var input_id = $('#'+dataset.inputId)
                var image_src = $('#'+dataset.image)
                input_url.val(data.url).trigger("change");
                input_id.val(data.id).trigger("change");
                image_src.attr('src',data.url);
            }
        })
        return false;
    });

    $('.related').on('click',function () {
        //先检查你的分类
        if($('#type_id').val() == "")
        {
            top.layer.msg("您的主题分类还没有选择呢");
            return false
        }

        var url =  ($('#type_id').val() == 1) ? '/index.php/admin/SelectedImage/select' : '/index.php/admin/product/select'
        var title  = ($('#type_id').val() == 1) ? '选择图片' : '选择商品'
        var that = this;
        $related_ids = $('#related_ids').val() ? $('#related_ids').val() : ''
        localStorage.setItem('related_ids',$related_ids)
        Fast.api.open(url, title, {
            callback: function (data) {
                if(data){
                    $('#'+$(that)[0].dataset.related).val(JSON.stringify(data))
                }
            }
        })
        return false;
    });

    var uploadInst = upload.render({
        elem: '.upload'
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

    form.on("select(select_cat)",function(data){
        $('#related_ids').attr('value','')
        form.render();
    });
})
