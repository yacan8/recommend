$(function(){
    LoadingFun.contentLoading(content_loading_url,{page:page},function(){
        $("#loading-img").show();
    }).then(function(data){
        $("#loading-img").hide();
        data.forEach(function (_item) {
            _item.DATAPATH = DATAPATH;
            _item.ROOT = ROOT;
            var newsItem = template('news-item', _item);
            var $newsItem = $(newsItem);
            $('#content-container').append($newsItem);
            $newsItem.first().after('<div class="p-l-sm"><a class="uv-pv font-12" href="javascript:;" data-id="'+_item.id+'">查看浏览指数</a> · <a href="javascript:;" class="font-12 comment-loading" data-id="'+_item.id+'">查看评论信息</a> ·  <a class="font-12 content-delete" href="javascript:;"  data-id="'+_item.id+'">删除</a></div>');
            $('.list-img>img').scrollLoading();
        });
    });
    //评论加载点击事件
    $(document).on('click','.comment-loading',function(){
        $("#comment-content").modal('show');
        var _self = $(this);
        var news_item = _self.parent().prev('.news-item').html();
        $('#comment-news-info').html(news_item);
        var news_id = _self.attr('data-id');
        $("#content-loading-btn").show().attr('data-id',news_id).button('loading');
        $("#comment-container").empty();
        var order = $('.comment-order.active').first().attr('data-order');
        commentPage = 1;
        LoadingFun.commentLoading(comment_loading_url,news_id,commentPage,order);
        commentPage++;

    });
    //评论排序点击事件
    $('.comment-order').click(function(){
        var _self = $(this);
        var news_id = $("#content-loading-btn").attr('data-id');
        if(!_self.hasClass('active')){
            $('.comment-order').removeClass('active');
            _self.addClass('active');
            var order = _self.attr('data-order');
            $("#content-loading-btn").show().button('loading');
            $("#comment-container").empty();
            commentPage = 1;
            LoadingFun.commentLoading(comment_loading_url,news_id,commentPage,order);
            commentPage++;
        }
    });

    //内容加载点击事件
    $("#content-loading-btn").click(function(){
        var news_id = $(this).button('loading').attr('data-id');
        var order = $('.comment-order.active').first().attr('data-order');
        LoadingFun.commentLoading(comment_loading_url,news_id,commentPage,order);
        commentPage++;
    });

    //评论删除点击事件
    $(document).on('click','.comment-delete',function(){
        var _self = $(this);
        $.confirm({
            theme:'material',
            title: '提示',
            content: '确认删除吗？',
            buttons: {
                confirm: {
                    text: '确认',
                    btnClass: 'btn-danger',
                    action: function(){
                        $.confirm({
                            content: function(){
                                var self = this;
                                return $.ajax({
                                    url: comment_delete_url,
                                    data:{comment_id:_self.attr('data-id')},
                                    dataType: 'json',
                                    method: 'post'
                                }).done(function (result) {
                                    if(result.success){
                                        self.setContentAppend(result.message);
                                        _self.parents('.media').remove();
                                    }else{
                                        self.setContentAppend(result.message);
                                    }
                                }).fail(function(){
                                    self.setContentAppend('请求失败');
                                });
                            },
                            title:'消息提示',
                            buttons:{
                                somethingElse:{
                                    text:'OK',
                                    btnClass:'btn-default'
                                }
                            }
                        });
                    }
                },
                cancel: {
                    text: '取消',
                    btnClass: 'btn-default'
                }

            }
        });
    });

    //Uv pv查看点击事件
    $(document).on('click','.uv-pv',function(){
        $('#uv-pv').modal('show');
        var news_item = $(this).parent().prev('.news-item').html();
        $('#uv-pv-news-info').html(news_item);
        var news_id = $(this).attr('data-id');
        $('#time').attr('data-id',news_id);
        $("#time").html(new Date().aWeekAgo().format()+' - '+new Date().format());
        setTimeout(function(){
            LoadingFun.morrisContentLoading(contentIndexLoadingUrl,news_id,new Date().aWeekAgo().format(),new Date().format());
        },1000)
    })

    //内容删除点击事件
    $(document).on('click','.content-delete',function(){
        var _self = $(this);
        $.confirm({
            theme:'material',
            title: '提示',
            content: '确认删除吗？',
            buttons: {
                confirm: {
                    text: '确认',
                    btnClass: 'btn-danger',
                    action: function(){
                        $.confirm({
                            content: function(){
                                var self = this;
                                return $.ajax({
                                    url: contentDeleteUrl,
                                    data:{news_id:_self.attr('data-id')},
                                    dataType: 'json',
                                    method: 'post'
                                }).done(function (result) {
                                    if(result.success){
                                        self.setContentAppend(result.message);
                                        var parent = _self.parent();
                                        parent.next().remove();
                                        parent.prev().remove();
                                        parent.remove();
                                    }else{
                                        self.setContentAppend(result.message);
                                    }
                                }).fail(function(){
                                    self.setContentAppend('请求失败');
                                });
                            },
                            title:'消息提示',
                            buttons:{
                                somethingElse:{
                                    text:'OK',
                                    btnClass:'btn-default'
                                }
                            }
                        });
                    }
                },
                cancel: {
                    text: '取消',
                    btnClass: 'btn-default'
                }

            }
        });
    });

    //UV-PV时间段选择
    $("#time").html(new Date().aWeekAgo().format()+' - '+new Date().format()).daterangepicker({
        locale: {format: 'YYYY/MM/DD'},
        language:  'zh-CN',
        startDate : new Date().aWeekAgo().format(),
        endDate: new Date().format()
    },function(start, end) {
        var _self = $("#time");
        _self.html(start.format('YYYY/MM/DD')+'-'+end.format('YYYY/MM/DD'));
        LoadingFun.morrisContentLoading(contentIndexLoadingUrl,_self.attr('data-id'),start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
    });



});