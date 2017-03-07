
function indexLoading(url,startTime,endTime,target_id){
    LoadingFun.contentLoading(url,{startTime:startTime,endTime:endTime},function(){
        $('#'+target_id).html("玩命加载中...");
    }).then(function(data){
        $('#'+target_id).empty();
        var result = [];
        var start = new Date(startTime).getTime();
        var end  = new Date(endTime).getTime();
        var difference = 1000 * 60 * 60 * 24 ;
        while( start <= end ){
            var item = { date: new Date(start).format2(),count:"0"};
            data.forEach(function(count_item){
                if( count_item.date == item.date){
                    item.count = count_item.count;
                }
            });
            start += difference;
            result.push(item);
        }
        Morris.Line({
            element: target_id,
            data: result,
            xkey: 'date',
            ykeys: ['count'],
            labels: ['数量']
        });
    })
}
$(function(){
    indexLoading(fans_index_url,new Date().aWeekAgo().format2(),new Date().format2(),'fans-index');
    indexLoading(cancel_follow_index_url,new Date().aWeekAgo().format2(),new Date().format2(),'cancel-follow-index');

    $("#fans_time").html(new Date().aWeekAgo().format()+' - '+new Date().format()).daterangepicker({
        locale: {format: 'YYYY/MM/DD'},
        language:  'zh-CN',
        startDate : new Date().aWeekAgo().format(),
        endDate: new Date().format()
    },function(start, end) {
        var _self = $("#fans_time");
        _self.html(start.format('YYYY/MM/DD')+'-'+end.format('YYYY/MM/DD'));
        indexLoading(fans_index_url,start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),'fans-index');
    });

    $("#cancel_follow_time").html(new Date().aWeekAgo().format()+' - '+new Date().format()).daterangepicker({
        locale: {format: 'YYYY/MM/DD'},
        language:  'zh-CN',
        startDate : new Date().aWeekAgo().format(),
        endDate: new Date().format()
    },function(start, end) {
        var _self = $("#cancel_follow_time");
        _self.html(start.format('YYYY/MM/DD')+'-'+end.format('YYYY/MM/DD'));
        indexLoading(cancel_follow_index_url,start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),'cancel-follow-index');
    });


})