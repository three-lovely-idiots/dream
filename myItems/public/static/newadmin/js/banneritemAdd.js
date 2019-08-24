layui.use(['form','layer'],function(){
    var form = layui.form
        layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery;

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
    function mySele($data){
        var main_type = $data.currentTarget.dataset.type;
        var type = $(".banneritem_type").val();
        var title,content;
        if( main_type == "item_img")
        {
            title = "选择图片";
            content = "/index.php/admin/image/myselect";
        }else{
            if(type == 1){
                title = "选择产品";
                content = "/index.php/admin/product/myselect";
            }else if(type==2){
                title = "选择主题";
                content = "/index.php/admin/theme/myselect";
            }
        }
        layer.open({
            title : title,
            type : 2,
            area : ["450px","385px"],
            content : content,
            success : function(layero, index){
                var body = $($(".layui-layer-iframe",parent.document).find("iframe")[0].contentWindow.document.body);
            },
            btn: '确定',
            yes:function(index, layero){
                var body = $($(layero).find("iframe")[0].contentWindow.document.body);
                var radioed = body.find(".layui-form-radioed");
                var img_obj;
                var img_id = '';
                var src = '';
                if(radioed.length){
                    img_obj = radioed.parent().parent().siblings("img");
                    input = radioed.siblings()[0].title;
                    console.log(input);
                    src = img_obj.attr("src");
                    id = img_obj.attr("alt");
                    if(!(src && id)){
                        layer.open({
                            type: 1,
                            content: '没有找到src或者img_id' //这里content是一个普通的String
                        })
                        layer.close(index);
                    }

                   if(main_type == "item_img") {
                       $('.linkLogoImg').attr('src', src);
                       $('#img_id').val(id);
                   }else{
                        $('.product_img').attr('src',src);
                        $('#product_id').val(id);
                        $('.product_name').html(input);
                   }
                    layer.close(index);
                }

            }
        })
    }
    $(".item_img").on("click",mySele);
    $(".product_theme").on("click",mySele);

    form.on("submit(addBanneritem)",function(data){
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        // 实际使用时的提交信息
        $.post("add",{
            id:$("#banneritem_id").val(),
            img_id:$("#img_id").val(),
            type:$(".banneritem_type").val(),
            key_word:$("#product_id").val(),
            banner_id:$(".banner_id").val()
        },function(res){
            if(res.code == 5000001){
                top.layer.msg(res.msg);
            }else{
                setTimeout(function(){
                    top.layer.close(index);
                    top.layer.msg(res.msg);
                    layer.closeAll("iframe");
                    //刷新父页面
                    window.location.reload();
                },2000);
            }
            return false;
        })

        return false;
    })

    form.on("select(banneritem_type)",function(data){
        data.othis.val(data.value);
    });

    //格式化时间
    function filterTime(val){
        if(val < 10){
            return "0" + val;
        }else{
            return val;
        }
    }
    //定时发布
    var time = new Date();
    var submitTime = time.getFullYear()+'-'+filterTime(time.getMonth()+1)+'-'+filterTime(time.getDate())+' '+filterTime(time.getHours())+':'+filterTime(time.getMinutes())+':'+filterTime(time.getSeconds());

})