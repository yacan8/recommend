var fetchLoading = function(url,params,beforeSend){
    var params = Object.keys(params).map(function(key){return key+'='+params[key]}).join('&');
    if(beforeSend){beforeSend();}
    return fetch(url+'?'+params, {credentials: "include"}).then(function(res){return res.json();});
};

var newsSimilarityCalculate = function( params ){
    var formData = new FormData();
    for(var key in params){
        formData.append(key, params[key]);
    }
    return fetch( ROOT+'/index.php/Home/Similarity/calculateNewsSimilarityByUserId', {
        credentials: 'include',
        method:"post",
        body: formData,
    }).then(function(res){
        var result ;
        try{
            result = res.json();
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

function html_encode(str) {
    var s = "";
    if (str.length == 0) return "";
    s = str.replace(/</g, "&lt;");
    s = s.replace(/>/g, "&gt;");
    return s;
}
template.helper("percent",function(float){
    var percent = float * 100 +'';
    percent = percent.substr(0,5) + '%';
    return percent;
});
$(function(){
    var dataTable = $('#data-table');
    $('#time').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    dataTable.on('click','.crawler-delete',function(){
        var _self = $(this);
        _self.parents('tr').remove();
    }).on('click','#calculate-in-begin',function(){
        var _self = $(this);
        _self.button('loading');
        dataTable.find('.operate').empty().html('等待中...');

        var similarityCalculateItem = function( trRow ) {
            trRow.children('.operate').html('<i class="fa fa-spinner rotate"> </i> 计算中...')
            var params = {
                news_id : trRow.children('.id').first().text(),
                showData : true
            };
            return newsSimilarityCalculate( params );
        };

        var aysncFun = async function ( trList ) {
            for( var i = 0 ; i< trList.length ; i++ ){
                var trItem = trList.eq(i);
                trItem.addClass('active');
                var result = await similarityCalculateItem( trItem );
                var optrateTd = trItem.removeClass('active').children('.operate');
                if ( result.success  ) {
                    if ( result.code == 200){
                        trItem.addClass('success');
                        optrateTd.html('<a class="fa fa-check" data-toggle="tooltip" data-placement="top" title="'+result.message+'" href="javascript:;"></a> <a class="btn btn-xs btn-success m-l-xs similarity-view">查看</a> '+"  <input type='hidden' class='data' value='"+html_encode(JSON.stringify(result.attr))+"' />")

                    } else if ( result.code == 300) {
                        trItem.addClass('info');
                        optrateTd.html('<a class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="'+result.message+'" href="javascript:;"></a> <a class="btn btn-xs btn-success m-l-xs similarity-view">查看</a> '+"  <input type='hidden' class='data' value='"+html_encode(JSON.stringify(result.attr))+"' />")
                    }
                } else {
                    trItem.addClass('danger');
                    optrateTd.html('<a class="fa fa-times" data-toggle="tooltip" data-placement="top" title="'+result.message+'" href="javascript:;"></a>')
                }
                $('[data-toggle="tooltip"]',trItem).tooltip();
            }
            _self.html('队列已完成');
        }
        aysncFun( dataTable.find('tbody').children('tr'));
    }).on('click','.similarity-view',function(){
        var _self = $(this),
            data = JSON.parse(_self.siblings('.data').val()),
            html = template('similarity-data',{list:data,ROOT:ROOT}),
            title = _self.parent().siblings('.title').text(),
            similarityModel = $('#similarity-modal');
        similarityModel.find('.modal-body').html(html);
        similarityModel.find('.modal-title').html(title);
        similarityModel.modal('show');
    });

    $("#news-get").click(function(){
        var _self = $(this);
        var url = ROOT + '/index.php/Admin/News/getCalculateSimilarityNews';
        var date = $('#time').val();
        fetchLoading(url,{date:date},function(){
            _self.button('loading');
        }).then(function(data){
            _self.html('获取完成');
            if ( data.success ) {
                var html = template('news-list',{list:data.attr,ROOT:ROOT});
                dataTable.html(html);
            } else {
                $.toaster({ priority : 'danger', title : '提示', message : data.message});
            }
            $('[data-toggle="tooltip"]').tooltip();
        })

    })



});