    var LoadingFun = {
        contentLoading : function(url,params,beforeSend){
            var params = Object.keys(params).map(function(key){return key+'='+params[key]}).join('&');
            if(beforeSend){beforeSend();}
            window.ajax_loading = true;
            return fetch(url+'?'+params, {credentials: "include"}).then(function(res){window.ajax_loading = false;return res.json()});
        }
    }
;$(function(){
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
                $newsItem.first().after('<div class="m-t-sm p-l-sm"><a class="uv-pv font-12" href="javascript:;" data-id="'+_item.id+'">查看浏览指数</a> · <a class="font-12 content-delete" href="javascript:;"  data-id="'+_item.id+'">删除</a></div>');
                $('.list-img>img').scrollLoading();
            });
        })

        $(document).on('click','.content-delete',function(){
            var _self = $(this);
            $.confirm({
                theme:'material',
                title: '提示',
                content: '确认删除吗？',
                buttons: {
                    confirm: {
                        text: '确认',
                        btnClass: 'btn-blue',
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
        $(document).on('click','.uv-pv',function(){
            $('#uv-pv').modal('show');
            var news_id = $(this).attr('data-id');
            $('#time').attr('data-id',news_id);
            $("#time").html(new Date().aWeekAgo().format()+' - '+new Date().format())
            setTimeout(function(){
                morrisContentLoading(contentIndexLoadingUrl,news_id,new Date().aWeekAgo().format(),new Date().format());
            },1000)
        })


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

        function morrisContentLoading(url,news_id,startTime,endTime){
            LoadingFun.contentLoading(url,{startTime:startTime,endTime:endTime,news_id:news_id},function(){
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
        }

        $("#time").html(new Date().aWeekAgo().format()+' - '+new Date().format()).daterangepicker({
            locale: {format: 'YYYY/MM/DD'},
            language:  'zh-CN',
            startDate : new Date().aWeekAgo().format(),
            endDate: new Date().format()
        },function(start, end) {
            var _self = $("#time");
            _self.html(start.format('YYYY/MM/DD')+'-'+end.format('YYYY/MM/DD'));
            morrisContentLoading(contentIndexLoadingUrl,_self.attr('data-id'),start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
        });



});