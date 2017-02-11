//关注js

function follow(follow_id,url,obj,callback){
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'html',
        data: {follow_id: follow_id},
        success:function(data){
          var result = $.parseJSON(data);
          callback(result,obj);
        },
        error:function(xhr){
          alert('请求失败')
        }
    });
}

$(function(){
    //关注点击事件
    $(".follow").click(function(e){
        var _self = $(this);
        var follow_id = _self.attr('data-id');
        //followcallback为不同页面的回调函数
        follow(follow_id,follow_url,_self,followcallback);
    })
})