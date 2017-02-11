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