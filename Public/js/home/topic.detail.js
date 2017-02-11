
var agt = navigator.userAgent.toLowerCase();
var is_op = (agt.indexOf("opera") != -1);
var is_ie = (agt.indexOf("msie") != -1) && document.all && !is_op;
function ResizeTextarea(a,row){
    if(!a){return}
    if(!row)
        row=5;
    var b=a.value.split("\n");
    var c=is_ie?1:0;
    c+=b.length;
    var d=a.cols;
    if(d<=20){d=40}
    for(var e=0;e<b.length;e++){
        if(b[e].length>=d){
            c+=Math.ceil(b[e].length/d)
        }
    }
    c=Math.max(c,row);
    if(c!=a.rows){
        a.rows=c;
    }
}
$(document).on('focus','.to_input',function(e) {
    if(!login_state)//未登录
        window.location.href = login_url+'?url='+this_url;
    else
        $(this).siblings('.comment-op').fadeIn('fast');
});


$(function(){



	$('.to_input').focus(function(e) {
		ResizeTextarea(this,4);
	});
	$('.to_input').keyup(function(e) {
		ResizeTextarea(this,4);
	});
    $('.to_input').keydown(function(e) {
        if(e.keyCode==8){
            var _self = $(this);
            var text = _self.val();
            var pos = _self.getpos();
            if(pos>=9){
                var prestr1 = text.substring(pos-1,pos);
                var prestr2 = text.substring(pos-2,pos-1);
                var prestr3 = text.substring(pos-8,pos-7);
                var prestr4 = text.substring(pos-9,pos-8);
                if(prestr1==']' &&prestr2 ==':'&&prestr3==':'&&prestr4=='['){
                    var prestr = text.substring(0,pos-8);
                    var sufstr = text.substring(pos);
                    _self.val(prestr+sufstr);
                }
            }
        }
            
    });


    $(".c-submit,.submit-gj").click(function(){
        loading_toggle();
        var _self = $(this);
        _self.button('loading');
        var _to_input = _self.parents(".comment-op").siblings('.to_input');
        var content = $.trim(_to_input.val());
        content = content.replaceAll("\n","<br/>"); 
        var comment_id = _self.attr('data-cid');
        var _form  = $('#submit-form');
        if(content==''){
            loading_toggle();
            _self.button('reset');
            $.toaster({ priority : 'danger', title : '通知', message : '请输入内容'});
        }
        else{
            var img_container = _self.parents(".comment-op").find('.upload-img-container');
            var Base64ImgArray = img_container.find('div.up-img');
            if(Base64ImgArray.length!=0){
                ajax_upload_img(Base64ImgArray.eq(0),img_uploap_url);
                submit_sign = setInterval(function(){
                    if(sign == 1){
                      clearInterval(submit_sign);
                      comment_data(comment_id,content,imgStr);
                      _form.submit();
                    }
                },500);
            }else{
                comment_data(comment_id,content,imgStr);
                _form.submit();
            }
        }
    })

    //收藏点击事件绑定
    $('#collection').click(function(e) {
        var _self = $(this);
        collection(topic_id,2,collect_url,_self);
    });
    
})


function followcallback(result,obj){
    if(result.Code =='200'){
        $.toaster({ priority : 'success',title:'<span class="glyphicon glyphicon-ok"></span>', message : result.Message});
        obj.addClass('topic-type').html('取消关注');
    }else if(result.Code == '201'){
        $.toaster({ priority : 'success', title : '<span class="glyphicon glyphicon-ok"></span>', message : result.Message});
        obj.removeClass('topic-type').html('<span class="glyphicon glyphicon-plus"></span> 关注');
    }else
        $.toaster({ priority : 'danger', title : '通知', message : result.Message});
    
}

function collection(collect_id,type,url,obj){
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'html',
        data: {collect_id: collect_id,type:type},
        success:function(data){
          var result = $.parseJSON(data);
          if(result.Code =='202')
            $.toaster({ priority : 'danger', title : '通知', message : result.Message});
          else{
            if(result.Code =='200')
                obj.addClass('btn-default').removeClass('btn-info').html('取消收藏');
            else if(result.Code == '201')
                obj.addClass('btn-info').removeClass('btn-default').html('收藏');
          }
        },
        error:function(xhr){
          alert('请求失败')
        }
    });
}


function comment_data(comment_id,content,imgStr){
    var _form  = $('#submit-form');
    _form.find('input[name="comment_id"]').val(comment_id);
    _form.find('input[name="content"]').val(content);
    _form.find('input[name="imgStr"]').val(imgStr);
}
String.prototype.replaceAll = function(s1,s2){ 
return this.replace(new RegExp(s1,"gm"),s2); 
} 


$(function () {
      var hash = window.location.hash;

      if(hash!=''){
        var c = hash.substr(1);
        var o = $('.'+c);
        setTimeout(function(){
          $("html,body").animate({scrollTop:o.offset().top-100},200);
        },300);
        setTimeout(function(){
          o.addClass('bg-main-20');
        },600);
        setTimeout(function(){
          o.removeClass('bg-main-20');
        },1000);

      }
      
      $('.pic_thumb,.u_icon,.u_c_icon').scrollLoading();
      $('[data-toggle="tooltip"]').tooltip();
        var options = {
        // inline: true,
        toolbar:true,
        title:false,
        maxZoomRatio:1,
        url: 'data-original',
      };
      $('.images').viewer(options);

      

      $(document).on('click',".c_close",function(e) {
        // 评论处理回复
        var _self = $(this);
        var obj = _self.parents(".replay-input");
        var text = obj.find('.to_input').val();
        $(this).parents('.comment-op').fadeOut('fast');
        obj.find('.to_input').val('');
        _self.parents('.comment-op').siblings('.to_input').attr('rows',1);
        // 添加评论处理
        var c_obj = $('.add-comment');
        c_obj.fadeOut('fast').find('.comment-op').fadeOut('fast');
      });

      $("#a_c_p").click(function(e) {
        $(".add-comment").fadeToggle('fast');
      });

      $(document).on('click',".replay-to",function(e) {
        var _self = $(this);
        var _user = _self.attr('data-user');
        var reply_to_name = _self.siblings(".a_user").html();
        var obj = _self.parents(".replay-container").siblings('.replay-input');
        var input = obj.find(".to_input");
        var text = input.val();
        var reply_text = "@["+_user+"]"+reply_to_name+":";
        input.val(reply_text+'');
        obj.fadeIn('fast');
        $("html,body").animate({scrollTop:obj.offset().top-100},200);
        input.focus();
      });
    });



