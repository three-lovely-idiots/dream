layui.use(['form','layer','inputTags','upload'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        inputTags = layui.inputTags,
        upload = layui.upload;
    var inputTagsTitle = [];
    if(self_styles.length > 0){
        for(var i in self_styles){
            inputTagsTitle.push(self_styles[i]);
        }
    }

    var other_tags = inputTagsTitle.length > 0 ? inputTagsTitle:[];
    inputTags.render({
        elem:'#inputTags',
        content: inputTagsTitle,
        aldaBtn: true,
        done: function(value){
            other_tags.push(value);
        }
    })

    $('#choose').on('click',function () {
        var that = this;
        Fast.api.open('/index.php/admin/attachment/select', '选择图片', {
            callback: function (data) {
                var button = $("#" + $(that).attr("id"));
                var input_id = $(button).data("input-id") ? $(button).data("input-id") : "";
                console.log(input_id);
                $("#" + input_id).val(data.url).trigger("change");
                $("#image_id").val(data.id).trigger("change");
                $(".demo2").attr('src',data.url);
            }
        })
        return false;
    });

    form.on('checkbox(tag_data)', function(data){
        $(data.elem).attr('checked',true);
        form.render();
    });


    form.on("submit(addSelectedImage)",function(data){
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var arr = [];
        var arr_tag_title = [];
        $("input:checkbox[name='tag_data']:checked").each(function(i){
            arr[i] = $(this).val();
            arr_tag_title[i] = $(this)[0].title;
        });
        var tag_data = arr.join(",");//将数组合并成字符串这里组装的是styles id
        var default_tag_title =  arr_tag_title.join(",");
        var other_tags = [];
        $("#tags span em").each(function(index,elem){
            other_tags.push($(this).text())
        })

        if(other_tags.length > 0){
            other_tags = other_tags.join(",");
        }

        if(other_tags.length == 1){
            other_tags = other_tags.trim(",");
        }
        // 实际使用时的提交信息
        $.post("add",{
            id:$("input[name=id]").val(),
            name:$("input[name=name]").val(),
            img_id:$("input[name=image_id]").val(),
            default_tags_ids:tag_data,
            default_tag_title:default_tag_title,
            other_tags:other_tags
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


    var uploadInst = upload.render({
        elem: '#test2'
        ,url: '/index.php/admin/ajax/upload/'
        ,before: function(obj){
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('.demo2').attr('src', result); ///图片链接（base64）
            });
        }
        ,done: function(res){
            //如果上传失败
            if(res.code < 0){
                return layer.msg('上传失败');
            }else{
               var image_url = this.elem.parent().siblings('#image_url');
               var image_id = this.elem.parent().siblings('#image_id');
                image_url.val(res.msg.url);
                image_id.val(res.msg.id);
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