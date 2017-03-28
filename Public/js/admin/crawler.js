var crawler = {
    sina : function(time){
        var url = ROOT+'/index.php/Admin/Crawler/sinaNews';
        return fetchLoading(url,{ time : time }).then( function( result ) { return result.text() }).then(function(result){
            return crawlerCallBack.sina(result,time);
        });
    },
    _163 : function(time){
        var url = ROOT+'/index.php/Admin/Crawler/news163';
        return fetchLoading(url,{}).then( function( result ) { return result.text() } ).then(function(result){
            return crawlerCallBack._163(result,time);
        });
    },
    crawlerRun : function(data,beforeSend) {
        var map = {
            'sina' : this.sina,
            '_163' : this._163
        };
        window.ajax_loading = true;
        if( beforeSend ) beforeSend();
        return Promise.all(function(){
            var promise = [];
            data.forEach(function(item){
                var crawlerFun = map[item.from];
                promise.push( crawlerFun(item.time) )
            })
            return promise;
        }()).then(function (results) {
            var result = [];
            results.forEach(function(item){ //结果连接
                result = result.concat.apply(result,item)
            });
            result.sort(function(a,b){ //按时间排序
                return b._time - a._time;
            });
            var html = template('crawler-news',{list:result});
            $("#crawler-table").html(html);
            window.ajax_loading = false;
            return result;
        });
    },
    crawlerIn : function( params ){
        var formData = new FormData();
        for(var key in params){
            formData.append(key, params[key]);
        }
        return fetch( ROOT+'/index.php/Admin/Crawler/crawlerIn', {
                credentials: 'include',
                method:"post",
                body: formData,
            }).then(function(res){
                var result ;
                try{
                    result = res.json()
                }catch(e){
                    result = {
                        success : false,
                        code : 500,
                        message : '写入失败'
                    }
                }
                return result;
            });
    }

};
Date.prototype.format = function(){
    var month = this.getMonth()+1;
    month = month < 10 ? '0'+ month : month;
    var day =  this.getDate();
    day = day < 10 ? '0' + day : day;
    var hour =  this.getHours();
    hour = hour < 10 ? '0' + hour : hour;
    var minutes = this.getMinutes();
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var seconeds = this.getSeconds();
    seconeds = seconeds < 10 ? '0' + seconeds : seconeds;
    return this.getFullYear() +'-' + month + '-' + day + ' ' + hour + ':' + minutes + ':' + seconeds;
};
Date.prototype.format2 = function(){
    var month = this.getMonth()+1;
    month = month < 10 ? '0'+ month : month;
    var day =  this.getDate();
    day = day < 10 ? '0' + day : day;
    var hour =  this.getHours();
    hour = hour < 10 ? '0' + hour : hour;
    var minutes = this.getMinutes();
    minutes = minutes < 10 ? '0' + minutes : minutes;
    return this.getFullYear() +'-' + month + '-' + day + ' ' + hour + ':' + minutes;
};
Date.prototype.oneDayAgo = function() {
    return new Date(this.getTime() - 1000 * 60 * 60 * 24 ); 
}

var crawlerCallBack = {
    sina : function(result,time){
        eval(result);
        var data = jsonData; //抓取数据变量jsonData
        var list = data.list;
        var result = [];
        list.forEach(function(crawler_item){
            var item = {};
            var _time = crawler_item.time*1000;
            if( _time > time){
                item.from = '新浪';
                item.title = crawler_item.title;
                item.type = crawler_item.channel.title;
                item.time = new Date(crawler_item.time*1000).format();
                item._time = _time;
                item.url = crawler_item.url;
                var url_split = item.url.split('/');
                item.from_id = url_split[url_split.length-1].split('.')[0];
                result.push(item);
            }

        });
        return result;
    },
    _163: function(result,time) {
        eval(result);
        var category = data.category;
        var news = data.news;
        var result = [];
        for( var i = 0 ; i <= 5 ; i++ ) { // 6为图片 7位视频
            var category_news = news[i];
            var type = category[i].n;
            category_news.forEach(function(crawler_item){
                var item = { type : type };
                var _time = new Date(crawler_item.p).getTime();
                if( _time > time){
                    item.from = '网易';
                    item.title = crawler_item.t;
                    item.time = crawler_item.p;
                    item._time = new Date(crawler_item.p).getTime();
                    item.url = crawler_item.l;
                    var url_split = item.url.split('/');
                    item.from_id = url_split[url_split.length-1].split('.')[0];
                    result.push(item);
                }
            })
        }
        return result;
    }
}

var fetchLoading = function(url,params,beforeSend){
    var params = Object.keys(params).map(function(key){return key+'='+params[key]}).join('&');
    if(beforeSend){beforeSend();}
    return fetch(url+'?'+params, {credentials: "include"}).then(function(res){return res;});
};



$(function(){
    var crawlerTable = $('#crawler-table');
    $('[data-toggle="tooltip"]').tooltip();
    $(document).scroll(function(){

    });
    $('#time').datetimepicker({
        format: 'yyyy-mm-dd hh:ii'
    });
    $("#crawler").click(function(){
        var _self = $(this),
            runParams = [],
            timeString = $("#time").val() + ':00',
            time = new Date(timeString).getTime();
        $(".from:checked").each(function () {
            runParams.push({from:this.value,time:time});
        });
        crawler.crawlerRun(runParams,function(){
            _self.button('loading');
        }).then(function(data){
            $('#crawler-table [data-toggle="tooltip"]').tooltip();
            _self.html('抓取已完成');
        });
    });
    crawlerTable.on('click','#crawler-reset',function(){
        $("#crawler").button('reset');
        $('#crawler-in-begin').addClass('disabled');
    }).on('click','.crawler-delete',function(){

        var _self = $(this);
        _self.parents('tr').remove();

    }).on('click','#crawler-in-begin',function(){
        var _self = $(this);
        _self.button('loading');
        $('#crawler-reset').addClass('disabled');
        crawlerTable.find('.operate').empty().html('等待中...'); 

        var crawlerItemIn = function( trRow ) {
            trRow.children('.operate').html('<i class="fa fa-spinner rotate"> </i> 写入中...')
            var params = {
                url : trRow.children('.title').children('a').first().attr('href'),
                time : trRow.children('.time').text(),
                from : trRow.children('.from').text(),
                from_id : trRow.children('.from_id').text(),
                type : trRow.children('.type').text()
            };

            return crawler.crawlerIn( params );
            
        }

        var aysncFun = async function ( trList ) {
            for( var i = 0 ; i< trList.length ; i++ ){
                var trItem = trList.eq(i);
                trItem.addClass('active');
                var result = await crawlerItemIn( trItem );
                var optrateTd = trItem.removeClass('active').children('.operate');
                if ( result.success && result.code == 200 ) {
                    trItem.addClass('success');
                    optrateTd.html('<a class="fa fa-check" data-toggle="tooltip" data-placement="top" title="'+result.message+'" href="javascript:;"></a>')
                } else {
                    if( result.code == 500 ) {
                        trItem.addClass('danger');
                        optrateTd.html('<a class="fa fa-times" data-toggle="tooltip" data-placement="top" title="'+result.message+'" href="javascript:;"></a>')
                    } else if ( result.code == 300) {
                        trItem.addClass('info');
                        optrateTd.html('<a class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="'+result.message+'" href="javascript:;"></a>')
                    }
                }
                $('[data-toggle="tooltip"]',trItem).tooltip();
            }
            _self.html('队列已完成');
            $("#crawler-reset").removeClass('disabled');
        }
        aysncFun( crawlerTable.find('tbody').children('tr'));
        

    })

})



