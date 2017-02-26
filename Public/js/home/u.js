function followcallback(result,obj){
    if(result.Code =='202'){
        $.toaster({ priority : 'danger', title : '通知', message : result.Message});
    }
    else{
        if(result.Code =='200'){
            $.toaster({ priority : 'success',title:'<span class="glyphicon glyphicon-ok"></span>', message : result.Message});
            obj.addClass('active').html('已关注');
        }else if(result.Code == '201'){
            $.toaster({ priority : 'success', title : '<span class="glyphicon glyphicon-ok"></span>', message : result.Message});
            obj.removeClass('active').html('加关注');
        }
    }
}
function content_loading(url,params,beforeSend){
    var params = Object.keys(params).map(function(key){return `${key}=${params[key]}`}).join('&');
    if(beforeSend){beforeSend();}
    window.ajax_loading = true;
    return fetch(url+'?'+params, {credentials: "include"}).then(function(res){window.ajax_loading = false;return res.json()});
}



$(function(){
    $("#follow-btn").click(function(){
        var _self = $(this);
        var user_id = _self.attr('data-id');
        followOrFansAction(user_id,follow_action_url).then(function(result){
            if(result.success){
                if(result.is_follow == '1'){
                    if(result.is_fans == '1'){
                        _self.html('<i class="glyphicon glyphicon-transfer"></i> 互相关注');
                    }else{
                        _self.html('<i class="glyphicon glyphicon-ok"></i> 已关注');
                    }
                }else{
                    _self.html('<i class="glyphicon glyphicon-plus"></i> 关注');
                }
                fansContent.fansList.forEach(function(item){
                    if(item.user_id == user_id){
                        item.is_fans = result.is_fans;
                        item.is_follow = result.is_follow;
                    }
                });
                followContent.followList.forEach(function(item){
                    if(item.user_id == user_id){
                        item.is_fans = result.is_fans;
                        item.is_follow = result.is_follow;
                    }
                });
            }else{
                $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : result.message});
            }
        });
    });
    $(document).on('click','.follow-btn',function(){
        var _self = $(this);
        var user_id = _self.attr('data-id');
        var type = _self.attr('data-type');
        var url = type == 'fans' ? fans_action_url : follow_action_url;

        followOrFansAction(user_id,url).then(function(result){
            if(result.success){
                if(result.is_follow == '1'){
                    if(result.is_fans == '1'){
                        _self.html('<i class="glyphicon glyphicon-transfer"></i> 互相关注');
                    }else{
                        _self.html('<i class="glyphicon glyphicon-ok"></i> 已关注');
                    }
                }else{
                    _self.html('<i class="glyphicon glyphicon-plus"></i> 关注');
                }
                fansContent.fansList.forEach(function(item){
                    if(item.user_id == user_id){
                        item.is_fans = result.is_fans;
                        item.is_follow = result.is_follow;
                    }
                });
                followContent.followList.forEach(function(item){
                    if(item.user_id == user_id){
                        item.is_fans = result.is_fans;
                        item.is_follow = result.is_follow;
                    }
                });
            }else{
                $.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : result.message});
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
                    }else{
                        var dynamics_id = _self.attr('data-dynamics-id');
                        dynamicsContent.dynamicsList.forEach(function(item){
                            if(item.id == dynamics_id){
                                item.content.zan_count = parseInt(item.content.zan_count) + 1;
                                item.content.is_zan = '1';
                            }
                        })
                    }
                })
                .fail(function() {
                    alert('请求失败')
                })
        }
    });




});

function followOrFansAction(id,url){
    const formData = new FormData();
    formData.append('id', id);
    return fetch(url,{
        credentials: 'include',
        method: 'post',
        body:formData
    }).then(function(res){return res.json()})
}


$(function(){
    window.location.hash = window.location.hash?window.location.hash:"#dynamics";

    $("#header-menu").children('a').click(function(event) {
        var _self = $(this);
        _self.addClass('active').siblings('a').removeClass('active');
    });

    $("#dynamics-menu-item").click(function(){
        $('#user-container').empty();
        $(this).children('span').remove();
        var params = {page: dynamicsPage, id: user_id, is_one_user: false};
        Loading.dynamicsLoading(params,false,false);
    });
    $("#fans-menu-item").click(function(){
        $('#user-container').empty();
        $(this).children('span').remove();
        var params = {page: fansPage, user_id: user_id};
        Loading.fansLoading(params);
    });
    $("#follow-menu-item").click(function(){
        $('#user-container').empty();
        var params = {page: followPage, user_id: user_id};
        Loading.followLoading(params);
    });
    $("#message-menu-item").click(function(){
        $('#user-container').empty();
        $(this).children('span').remove();
        var params = {page: messagePage, user_id: user_id};
        Loading.messageLoading(params);
    });

    $("#userInfo-menu-item").click(function(){
        $('#user-container').empty();
        var params = {id: user_id};
        Loading.userInfoLoading(params,!userInfoContent.isLoaded);
    });



    var hash = window.location.hash;
    $(hash+'-menu-item').click();
    $(document).scroll(function(){
        var documentHeight = $(document).height();
        var scrollTop = $(document).scrollTop();

        if(documentHeight-$("footer").height()<scrollTop+$(window).height()){
            if(window.location.hash == "#dynamics"){
                if(!window.ajax_loading && dynamicsContent.hasMore){
                    Loading.dynamicsLoading({page: ++dynamicsPage, id: user_id, is_one_user: false},true);
                }
            }else if(window.location.hash == "#my-dynamics"){
                if(!window.ajax_loading && dynamicsContent.hasMore){
                    var params = {page: ++dynamicsPage, id: user_id, is_one_user: true};
                    Loading.dynamicsLoading(params,true,true);
                }
            }else if(window.location.hash == "#fans"){
                if(!window.ajax_loading && fansContent.hasMore){
                    Loading.fansLoading({page:++fansPage,user_id:user_id},true);
                }
            }
            else if(window.location.hash == "#follow"){
                if(!window.ajax_loading && followContent.hasMore){
                    Loading.followLoading({page:++followPage,user_id:user_id},true);
                }
            }else if(window.location.hash == "#message"){
                if(!window.ajax_loading && messageContent.hasMore){
                    Loading.messageLoading({page:++messagePage},true);
                }
            }
        }
    })

});

var Loading = {
    dynamicsLoading:function(params,is_loading,is_my_dynamics_page){
        var all_dynamics_url = is_my_dynamics_page?ROOT+'/u/'+params.id+'#dynamics':'javascript:;';
        var all_dynamics_class = is_my_dynamics_page ? 'tc-gray9 m-r-md':'tc-black m-r-md';
        var my_dynamics_class = is_my_dynamics_page ? 'tc-black':'tc-gray9';

        var titleStr = user_id == login_id ? '<a href="'+all_dynamics_url+'" class="'+all_dynamics_class+'">关注动态</a> <a class="'+my_dynamics_class+'" href="'+ROOT+'/User/myDynamics#my-dynamics">我的动态</a>':'<span class="tc-black">动态</span> ';
        $('#user-title').html(titleStr);
        if((dynamicsContent.dynamicsList.length == 0 || is_loading ) && dynamicsContent.hasMore){
            content_loading(dynamics_loading_url, params, function () {
                $("#loading-img").show();
            }).then(function (result) {
                if(result.length == 0){
                    dynamicsContent.hasMore = false;
                    $('#user-container').append('<div class="text-center p-l-md p-r-md p-b-md font-smoothing">暂无更多内容</div>');
                    $("#loading-img").hide();
                }else{
                    var html = '';
                    result.forEach(function (_item) {
                        dynamicsContent.dynamicsList.push(_item);
                        _item.DATAPATH = DATAPATH;
                        _item.ROOT = ROOT;
                        var type = 'dynamics' + _item.type;
                        html += template(type, _item);
                    });
                    $('#user-container').append(html);
                    $("#loading-img").hide();
                }
            })
        }else{
            var html = '';
            dynamicsContent.dynamicsList.forEach(function(_item){
                _item.DATAPATH = DATAPATH;
                _item.ROOT = ROOT;
                var type = 'dynamics' + _item.type;
                html += template(type, _item);
            });
            $('#user-container').html(html);
            if(!dynamicsContent.hasMore){
                $('#user-container').append('<div class="text-center p-l-md p-r-md p-b-md font-smoothing">暂无更多内容</div>');
            }
            $("#loading-img").hide();
        }

    },
    fansLoading:function(params,is_loading){
        $('#user-title').html('粉丝');
        if((fansContent.fansList.length == 0 || is_loading ) && fansContent.hasMore ){
            content_loading(fans_loading_url,params,function(){
                $("#loading-img").show();
            }).then(function(result){
                console.log(result);
                if(result.length == 0){
                    fansContent.hasMore = false;
                    $('#user-container').append('<div class="text-center p-l-md p-r-md p-b-md font-smoothing">暂无更多内容</div>');
                    $("#loading-img").hide();
                }else {
                    var html = '';
                    result.forEach(function (_item) {
                        fansContent.fansList.push(_item);
                        _item.DATAPATH = DATAPATH;
                        _item.loadingType = 'fans';
                        _item.ROOT = ROOT;
                        html += template('fans_or_follow', _item);
                    });
                    $('#user-container').append(html);
                    $("#loading-img").hide();
                }
            })
        }else{
            var html = '';
            fansContent.fansList.forEach(function(_item){
                _item.DATAPATH = DATAPATH;
                _item.ROOT = ROOT;
                html += template('fans_or_follow',_item);
            });
            $('#user-container').html(html);
            if(!fansContent.hasMore){
                $('#user-container').append('<div class="text-center p-l-md p-r-md p-b-md font-smoothing">暂无更多内容</div>');
            }
            $("#loading-img").hide();
        }

    },
    followLoading:function(params,is_loading){
        $('#user-title').html('关注');
        if((followContent.followList.length == 0 || is_loading) && followContent.hasMore){
            content_loading(follow_loading_url,params,function(){
                $("#loading-img").show();
            }).then(function(result){
                if(result.length == 0){
                    followContent.hasMore = false;
                    $('#user-container').append('<div class="text-center p-l-md p-r-md p-b-md font-smoothing">暂无更多内容</div>');
                    $("#loading-img").hide();
                }else {
                    var html = '';
                    result.forEach(function (_item) {
                        followContent.followList.push(_item);
                        _item.DATAPATH = DATAPATH;
                        _item.ROOT = ROOT;
                        _item.loadingType = 'follow';
                        html += template('fans_or_follow', _item);
                    });
                    $('#user-container').append(html);
                    $("#loading-img").hide();
                }
            })
        }else{
            var html = '';
            followContent.followList.forEach(function(_item){
                _item.DATAPATH = DATAPATH;
                _item.ROOT = ROOT;
                html += template('fans_or_follow',_item);
            });
            $('#user-container').html(html);
            if(!followContent.hasMore){
                $('#user-container').append('<div class="text-center p-l-md p-r-md p-b-md font-smoothing">暂无更多内容</div>');
            }
            $("#loading-img").hide();
        }

    },

    messageLoading : function(params,is_loading){
        $('#user-title').html('我的消息');
        if((messageContent.messageList.length == 0 || is_loading ) && messageContent.hasMore){
            content_loading(message_loading_url, params, function () {
                $("#loading-img").show();
            }).then(function (result) {
                console.log(result);
                if(result.length == 0){
                    messageContent.hasMore = false;
                    $('#user-container').append('<div class="text-center p-l-md p-r-md p-b-md font-smoothing">暂无更多内容</div>');
                    $("#loading-img").hide();
                }else{
                    var html = '';
                    result.forEach(function (_item) {
                        messageContent.messageList.push(_item);
                        _item.DATAPATH = DATAPATH;
                        _item.ROOT = ROOT;
                        var type = 'message' + _item.type;
                        html += template(type, _item);
                    });
                    $('#user-container').append(html);
                    $("#loading-img").hide();
                }
            })
        }else{
            var html = '';
            messageContent.messageList.forEach(function(_item){
                _item.DATAPATH = DATAPATH;
                _item.ROOT = ROOT;
                var type = 'message' + _item.type;
                html += template(type, _item);
            });
            $('#user-container').html(html);
            if(!messageContent.hasMore){
                $('#user-container').append('<div class="text-center p-l-md p-r-md p-b-md font-smoothing">暂无更多内容</div>');
            }
            $("#loading-img").hide();
        }
    },


    userInfoLoading : function(params,is_loading){
        $('#user-title').html('个人资料');
        if(is_loading || userInfoContent.userInfo == null){
            content_loading(user_info_loading_url,params,function(){
                $("#loading-img").show();
            }).then(function(result){
                console.log(result);
                userInfoContent.isLoaded = true;
                result.DATAPATH = DATAPATH;
                result.ROOT = ROOT;
                userInfoContent.userInfo = result;
                $('#user-container').html(template('userinfo',result));
                $("#loading-img").hide();
            })
        }else{
            $('#user-container').html(template('userinfo',userInfoContent.userInfo));
        }
    }
}



