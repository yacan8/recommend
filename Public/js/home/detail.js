// 文章详情Js
;$(function(){
        //提交收藏
        $("#concern").click(function(event) {
            concern(id);
        });

        $(document).on('click','.zan-btn',function(){
            var _self = $(this);

            if(!_self.hasClass('active')){
                _self.addClass('active');
                var zan_container = _self.children('.zan');
                var zan_count = zan_container.text();
                zan_container.html(parseInt(zan_count)+1);
                var comment_id = _self.attr('data-comment-id');
                $.ajax({
                    url: zan_url,
                    type: 'post',
                    dataType: 'json',
                    data: {comment_id: comment_id}
                })
                .done(function(data) {
                    if(!data.success){
                        zan_container.html(zan_count);
                        _self.removeClass('active');
                        $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : data.message});
                    }
                })
                .fail(function() {
                    alert('请求失败')
                })
            }
        })

        $("#loading").click(function(event) {
            var _self= $(this);
            ajax_load_comment(page,id,_self);
            page++;
        });

        $(".comment-area").focus(function(event) {
            $(this).animate({height:'120px'}, 500);
            $(this).siblings('.comment-btn-container').children("#comment").show();
        });
        $("#comment").click(function(event) {
            var content = $('#comment-area').val();
            if(content==''){
                $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '请输入内容'});
                return false;
            }else
                comment(id,content,'');
        });


        $(document).on("click",".reply",function(event) {
            var title = $(this).parent().parent().siblings('.media-heading').children(".sender_id").text();
            $("#ModalTitle").html('回复：'+title);
            receiver_tel = $(this).attr('data-sender');
            $("#reply").modal("show");
        });


        $(".comment_btn").click(function(event) {
            var content = $("#comment_content").val();
            comment(id,content,receiver_tel);
        });

        //ajax加载评价
        function ajax_load_comment(page,id,object){
            $.ajax({
                url: loading_url,
                data:{id:id,page:page},
                type: 'get',
                dataType: 'text',
                beforeSend:function(XMLHttpRequest){
                    object.html("加载中 <img src='"+PUBLIC+"/img/loading.gif'>");
                },
                success:function(data,textStatus){
                    var dataObj = $.parseJSON(data);
                    console.log(dataObj);
                    var str ='';
                    if(data=='[]'||data==null||dataObj==null){
                        $(".comment-containter").append("<div class='text-center' style='font-size:12px;'>暂无更多</div>");
                        object.html('加载更多');
                        object.fadeOut();
                    }
                    else{
                        object.html('加载更多');
                        for(var i=0;i<dataObj.length;i++){
                            str = str+g_comment(dataObj[i]);
                        }
                        $(".comment-containter").append(str);
                    }
                },
                error:function() {
                    alert("请求失败");
                }
            });
        }

        //生成评价字符串
        function g_comment(data){
            var str = '<div class="media m-b-md">'+
                        '<div class="media-left">'+
                            '<a href="javascript:void(0)"><img class="media-object img-circle" alt="" src="'+DATA+'/login_thumb/'+data.senderinfo.icon+'" data-holder-rendered="true"></a>'+
                        '</div>'+
                        '<div class="media-body">'+
                        '<h5 class="media-heading"><span class="sender_id">'+data.senderinfo.nickname+"</span>";
                            if(data.receiverinfo!=null)
                                str = str+' 回复：'+data.receiverinfo.nickname;
                str = str+'</h5>'+data.content+
                        '<div class="m-t-sm" style="font-size:12px;">'+
                            '<a class="fa tc-gray9"><span class="m-r-sm">'+data.time+'</span></a> <a href="javascript:void(0)"><span class="m-r-sm reply" data-sender="'+data.sender+'">回复</span></a>'+
                        '</div></div></div>';
            return str;
        }

        //回复
        function comment(id,content,reply){
            $.ajax({
                url: comment_url,
                data:{news_id:id,content:$.trim(content),reply:reply},
                type: 'post',
                dataType: 'json',
                success:function(data,textStatus){
                    if( data.success ){
                        if( data.code = 200 ) {
                            location.reload();
                        }else if( data.code == 199 ) {
                            $("#reply").modal("hide");
                            $("#comment_content").val("");
                            $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '你还没有登录。'});
                            setTimeout(function(){
                                self.location = login_url;
                            },1000)
                        }else if( data.code == 300 ){
                            $("#reply").modal("hide");
                            $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '不能回复自己'});
                        }
                    }else{
                        $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : data.message});
                    }
                },
                error:function() {
                    alert("请求失败");
                }
            });
        }
        //收藏
        function concern(id){
            $.ajax({
                url: concern_url,
                data:{collected:id},
                type: 'post',
                dataType: 'text',
                success:function(data,textStatus){
                    if(data=='1'){
                        alert('收藏成功');
                    }else if(data =='2'){
                        alert('你还没有登录');
                        self.location = login_url;
                    }else if(data == '3'){
                        alert('你已收藏过本条新闻');
                    }else{
                        alert(data);
                    }
                },
                error:function() {
                    alert("请求失败");
                }
            });
        }

    });
