// 焦点图js
;$(function(){
    //上一张事件
    $(".focus-pre").click(function() {
        focus_op('pre');
    });
    //下一张事件
    $(".focus-next").click(function() {
        focus_op('next');
    });
    window.setInterval(function(){
        focus_op('next');
    },5000);
})
function focus_op(op){
    var index = $(".focus-container").find(".focus-item.active").index();
    if(op=='next'){
        if(index==5)
            index=0;
    }else{
        if(index==1)
            index=4;
        else
            index=index-2;
    }
    $(".focus-container").find(".focus-item").removeClass('active').eq(index).addClass('active');
}