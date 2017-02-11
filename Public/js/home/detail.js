// 文章详情Js
;$(function(){

        //提交收藏
        $("#concern").click(function(event) {
            concern(id);
        });

        $("#loading").click(function(event) {
            var _self= $(this);
            ajax_load_comment(page,id,_self);
            page++;
        });


        $("#comment").click(function(event) {
            var content = $('#comment-area').val();
            if(content==''){
                alert('请输入内容');
                return false;
            }else
                comment(id,content,'');
        });


        $(document).on("click",".reply",function(event) {
            var title = $(this).parents("div.m-t-sm").siblings('h5.media-heading').find(".sender_id").html();
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
        function comment(id,content,receiver){
            $.ajax({
                url: comment_url,
                data:{other_id:id,content:content,receiver:receiver},
                type: 'post',
                dataType: 'text',
                success:function(data,textStatus){
                    if(data=='1'){
                        location.reload();
                    }else if(data =='2'){
                        $("#reply").modal("hide");
                        $("#comment_content").val("");
                        self.location = login_url;
                        alert('你还没有登录');
                    }else if(data =='3'){
                        $("#reply").modal("hide");
                        alert('不能评价自己');
                    }else{
                        alert(data);
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