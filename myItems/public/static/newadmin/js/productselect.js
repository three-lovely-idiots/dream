layui.config({
	base : "../../js/"
}).use(['flow','form','layer','upload'],function(){
    var flow = layui.flow,
        form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        upload = layui.upload,
        $ = layui.jquery;

    //流加载图片

    function flowLoad($category_id){
        var imgNums = 15;  //单页显示图片数量
        flow.load({
            elem: '#Images', //流加载容器
            done: function(page, next){ //加载下一页
                $.post("mySelect", {"category_id":$category_id},function(res){
                    //插入
                    var imgList = [],data = res;
                    var maxPage = imgNums*page < data.length ? imgNums*page : data.length;
                    setTimeout(function(){
                        for(var i=imgNums*(page-1); i<maxPage; i++){
                            imgList.push('<li><img layer-src="'+data[i].img.url+'"  src="'+data[i].img.url+'"  alt="'+data[i].id+'"><div class="operate"><div class="check"><input type="radio" name="img" value="'+data[i].id+'" lay-filter="choose" lay-skin="primary" title="'+data[i].name+'"></div><i class="layui-icon img_del">&#xe640;</i></div></li>');
                        }
                        next(imgList.join(''), page < (data.length/imgNums));
                        form.render();
                    }, 500);
                });
            }
        });
    }

    flowLoad();

    //设置图片的高度
    $(window).resize(function(){
        $("#Images li img").height($("#Images li img").width());
    })

    form.on("select(product_type)",function(data){
        var category_id = data.value;
        $("#Images").empty();
        flowLoad(category_id);
    });
    //多图片上传
    upload.render({
        elem: '.uploadNewImg',
        url: '../../json/userface.json',
        multiple: true,
        before: function(obj){
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#Images').prepend('<li><img layer-src="'+ result +'" src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img"><div class="operate"><div class="check"><input type="checkbox" name="belle" lay-filter="choose" lay-skin="primary" title="'+file.name+'"></div><i class="layui-icon img_del">&#xe640;</i></div></li>')
                //设置图片的高度
                $("#Images li img").height($("#Images li img").width());
                form.render("checkbox");
            });
        },
        done: function(res){
            //上传完毕
        }
    });


})