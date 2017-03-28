    var LoadingFun = {
        contentLoading : function(url,params,beforeSend){
            var params = Object.keys(params).map(function(key){return key+'='+params[key]}).join('&');
            if(beforeSend){beforeSend();}
            window.ajax_loading = true;
            return fetch(url+'?'+params, {credentials: "include"}).then(function(res){window.ajax_loading = false;return res.json()});
        },
        morrisContentLoading :function(url,news_id,startTime,endTime){
            this.contentLoading(url,{startTime:startTime,endTime:endTime,news_id:news_id},function(){
                $("#index-container").html("玩命加载中...");
            }).then(function(data){
                $("#index-container").empty();
                var uvList = data.uv.slice(0),
                    pvList = data.pv.slice(0);
                var result = [];
                var start = new Date(startTime).getTime();
                var end  = new Date(endTime).getTime();
                var difference = 1000 * 60 * 60 * 24 ;
                while( start <= end ){
                    var item = { date: new Date(start).format2(),uv:"0",pv:"0"}
                    uvList.forEach(function(uv_item){
                        if( uv_item.date == item.date){
                            item.uv = uv_item.count;
                        }
                    });
                    pvList.forEach(function(pv_item){
                        if( pv_item.date == item.date){
                            item.pv = pv_item.count;
                        }
                    });
                    start += difference;
                    result.push(item);
                }

                Morris.Line({
                    element: 'index-container',
                    data: result,
                    xkey: 'date',
                    ykeys: ['pv','uv'],
                    labels: ['总浏览数','访客数']
                });
            })
        },
        commentLoading :function(url,news_id,page,order){
            this.contentLoading(url,{page:page,id:news_id,order:order},function(){
                $("#content-loading-btn").button('loading');
            }).then(function(result){
                if(result.success){
                    var html = '';
                    result.attr.forEach(function(_item){
                        _item.DATAPATH = DATAPATH;
                        _item.ROOT = ROOT;
                        html  += template('comment', _item);
                    });
                    $("#comment-container").append(html);
                    $("#content-loading-btn").button('reset');
                    if(result.attr.length < 5 || result.attr.length == 0) {
                        $("#content-loading-btn").hide();
                        $("#comment-container").append('<div class="text-center m-t-sm m-b-sm">暂无更多内容</div>');
                    }
                }else{
                    $.confirm({
                        title: '消息提示',
                        content: '加载失败',
                        draggable: true,
                    });
                }

            })
        },
        commentManageLoading : function(url,order,page){
            this.contentLoading(url,{page:page,order:order},function(){
                $("#loading-img").show();
            }).then(function(result){
                $("#loading-img").hide();
                var html = '';
                result.forEach(function(_item){
                    console.log(_item);

                    _item.DATAPATH = DATAPATH;
                    _item.ROOT = ROOT;
                    html  += template('comment-manage', _item);
                });
                $("#comment-manage-container").append(html);


            })
        }
    }
    Date.prototype.aWeekAgo = function(){
        return new Date(this.getTime() - 1000 * 60 * 60 * 24 *6 );
    };
    Date.prototype.format = function(){
        return this.getFullYear()+'/'+(this.getMonth()+1)+'/'+this.getDate();
    };
    Date.prototype.format2 = function(){
        var month = this.getMonth()+1;
        month = month < 10 ? '0'+ month : month;
        var day =  this.getDate();
        day = day < 10 ? '0' + day : day;
        return this.getFullYear()+'-'+month+'-'+day;
    };
