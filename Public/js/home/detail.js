// 文章详情Js
;$(function(){
        //提交收藏
        $("#collection").click(function(event) {
            var _self = $(this);
            $.ajax({
                url: collection_url,
                data:{collection_id:_self.attr('data-id')},
                type: 'post',
                dataType: 'json',
                success:function(data){
                    if(data.success){
                        if( data.code == 201 ){
                            _self.attr('title','收藏').html('<i class="glyphicon glyphicon-star-empty"></i> 收藏')
                        }else{
                            _self.attr('title','取消收藏').html('<i class="glyphicon glyphicon-star"></i> 取消收藏')
                        }
                    }else{
                        $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : data.message});
                    }
                },
                error:function() {
                    alert("请求失败");
                }
            });
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
        function loadingBind(){
            $("#loading").bind('click',function(event) {
                var _self= $(this);
                ajax_load_comment(page,id,_self);
                page++;
            });
        }
        loadingBind();
        $("#order").children('li').click(function(event) {
            var _self = $(this);
            page = 1;
            $.ajax({
                url: loading_url,
                type: 'get',
                dataType: 'json',
                data: {id:id,page:page,order:_self.attr('data-order')},
            })
            .done(function(data) {
                var object = $('#loading');
                _self.siblings('li').removeClass('active');
                _self.addClass('active');
                if(data.success){
                    if(!data.attr){
                        object.html('暂无更多评论').addClass('no-content');
                        object.unbind('click');
                    } else{
                        if(data.has_more){
                            loadingBind();
                            object.html('查看更多评论').removeClass('no-content');
                        }else{
                            object.html('暂无更多评论').addClass('no-content');
                            object.unbind('click');
                        }
                        $(".comment-containter").html( g_comment(data.attr));
                    }
                    page++;
                }else{
                    alert("加载错误");
                }
            })
            .fail(function() {
                alert('请求失败');
            })

        });

        $(".comment-area").focus(function(event) {
            $(this).animate({height:'120px'}, 500);
            $(this).siblings('.comment-btn-container').children("#comment").show();
        });
        $("#comment").click(function(event) {
            var content = $('#comment-area').val();
            if( $.trim(content) == ''){
                $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '请输入内容'});
                return false;
            }else{
                $(this).button("loading");
                comment(id,content,'',$(this));
            }

        });


        $(document).on("click",".reply",function(event) {
            var title = $(this).parent().parent().siblings('.media-heading').children(".sender_id").text();
            $("#ModalTitle").html('回复：'+title);
            receiver_tel = $(this).attr('data-sender');
            $("#reply").modal("show");
        });


        $(".comment_btn").click(function(event) {
            var content = $("#comment_content").val();
            comment(id,content,receiver_tel,$(this));
        });

        //ajax加载评价
        function ajax_load_comment(page,id,object){
            $.ajax({
                url: loading_url,
                data:{id:id,page:page,order:$('#order').children('li.active').attr('data-order')},
                type: 'get',
                dataType: 'json',
                beforeSend:function(XMLHttpRequest){
                    object.html("加载中 <img src='"+PUBLIC+"/img/loading.gif'>");
                },
                success:function(data){
                    if(data.success){
                        if(!data.attr){
                            object.html('暂无更多评论').addClass('no-content');
                            object.unbind('click');
                        } else{
                            if(data.has_more){
                                object.html('查看更多评论');
                            }else{
                                object.html('暂无更多评论').addClass('no-content');
                                object.unbind('click');
                            }
                            $(".comment-containter").append( g_comment(data.attr));
                        }
                    }else{
                        alert("加载错误");
                    }
                },
                error:function() {
                    alert("请求失败");
                }
            });
        }

        //生成评价字符串
        function g_comment(dataObj){
            var result = '';
            for(var i=0;i<dataObj.length;i++){
                var data = dataObj[i];
                data.user.icon = data.user.icon || 'default.jpg';
                result += '<div class="media m-b-md"> ' +
                    '<div class="media-left"> ' +
                    '<a href="'+data.user_url+'"><img class="media-object img-circle"  alt="" src="'+DATA+'/login_thumb/'+data.user.icon+'" data-holder-rendered="true"></a>'+
                    '</div> ' +
                    '<div class="media-body"> ' +
                    '<h5 class="media-heading"> ' +
                    '<a href="'+data.user_url+'" class="sender_id tc-main">'+data.user.nickname+'</a>';
                if(author == data.user_id)
                    result += '<span class="label label-info">作者</span> ';
                result += ' · <span class="tc-gray9 font-smoothing">'+data.time+'</span></h5> ' +
                    '<div class="font-14">' + data.content;
                if(data.reply_count) {
                    result += '<a class="tc-main" href="'+data.reply_count.reply_user_url+'">@data.reply_content.nickname</a>：data.reply_content.content';
                }
                result += '</div> ' +
                    '<div class="m-t-xs" style="font-size:12px;"> ' +
                    '<a href="javascript:void(0)" class="font-smoothing"><span class="m-r-sm reply" data-sender="'+data.id+'">回复</a>';
                if(data.is_zan){
                    result += '<a class="tc-gray9 zan-btn" href="javascript:;" data-comment-id="'+data.id+'"> <span class="glyphicon glyphicon-thumbs-up"></span> <span class="zan">'+data.zan_count+'</span> </a>';
                }else{
                    result += '<a class="tc-gray9 zan-btn active" href="javascript:;" data-comment-id="'+data.id+'"> <span class="glyphicon glyphicon-thumbs-up"></span> <span class="zan">'+data.zan_count+'</span> </a>';
                }
                result+= '</div></div></div>';
            }

            return result;
        }

        //回复
        function comment(id,content,reply,button){
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
                            if(button) button.button('reset');
                            $("#reply").modal("hide");
                            $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '不能回复自己'});
                        }
                    }else{
                        if(button) button.button('reset');
                        $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : data.message});
                    }
                },
                error:function() {
                    alert("请求失败");
                }
            });
        }


    });
