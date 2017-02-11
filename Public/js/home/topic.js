// $(document).ajaxStart(function () {alert("处理函数1：");});
var jiathis_config = {
    boldNum:0,
    siteNum:7,
    summary:' ',
    url:"",
    title:"",
    pic:"",
    data_track_clickback:true,
}

$('.pic_thumb,.u_icon,.u_c_icon').scrollLoading();


$(function () {

  $('[data-toggle="tooltip"]').tooltip();
  $("#topic-type-menu").click(function(event) {
    var ob = $(".topic-type-container");
    ob.toggleClass('active');
  });
    var options = {

    title:false,
    maxZoomRatio:1,
    url: 'data-original',
  };
  $('.images').viewer(options);


  $('.publish-topic').click(function(event) {
    var _self = $(this);
    var count = _self.siblings('a.active').length;
    if(count<=3)
      _self.toggleClass('active');
    else
      $.toaster({ priority : 'danger', title : '通知', message : '话题类型数量不得超过4个。'});
  });

  $(".share_click").click(function(event) {
      var host = window.location.host;
      var _self = $(this);
      var media_body = _self.parents('.media-body');
      var url = 'http://'+host+media_body.find('.media-heading').attr('href');
      var title = media_body.find('.media-heading').html();
      var _img_container = media_body.find('.images.media-picture');
      if(_img_container.length!=0){
        var pic = 'http://'+host+_img_container.find('img').eq(0).attr('data-original');
        jiathis_config.pic = pic;
      }else{
        jiathis_config.pic = '';
      }
      jiathis_config.url = url;
      jiathis_config.title = title;
      
  });



   



   $('.publish-submit').click(function(e){
      loading_toggle();
      $("#myModal").find('.modal-body .alert-container').html('');
      var $topic_type = $('input[name="topic_type"]');
      $topic_type.val('');
      var _self = $(this);
      _self.button('loading');
      if(checkImgSise()){//如果大小未溢出
        var topicTypeArray = $('.publish-topic.active');
        var str = '';
        var typeLength = topicTypeArray.length;
        if(typeLength!=0){
          for(var i = 0 ;i<typeLength;i++){
            var type = topicTypeArray.eq(i).attr('data-type');
            if(i!=typeLength-1)
              str = str + type.toString() + ',';
            else
              str = str + type.toString();
          }
          $topic_type.val(str);
        }else{
          $topic_type.val('');
        }

        var base64Img = $(".upload-img-container").find('.up-img');
        var form_obj = $("#topic-publish");
        if(base64Img.length==0){
          sign = 1;
          if(check_form(form_obj)){
            form_obj.submit();
          }
        }else{
          if(check_form(form_obj)){
              ajax_upload_img(base64Img.eq(0),img_uploap_url,_self);
              submit_sign = setInterval(function(){
                if(sign == 1){
                  clearInterval(submit_sign);
                  form_obj.append('<input type="hidden" name="ImgStr" value="'+imgStr+'" />');
                  form_obj.submit();
                }
              },500);
          }
        }
      }
      
      
      
   })
})
//判断表单提交内容是否为空
function check_form(form){
  var container = $("#myModal").find('.modal-body .alert-container');
  if(form.find('textarea.form-control[name="title"]').val()==''){
    loading_toggle();
    $('.publish-submit').button('reset');
    form.find('textarea.form-control[name="title"]').focus();
    container.html(g_danger_alert('请输入标题。'));
    return false;
  }else if(form.find('textarea.form-control[name="content"]').val()==''){
    loading_toggle();
    $('.publish-submit').button('reset');
    form.find('textarea.form-control[name="content"]').focus();
    container.html(g_danger_alert('请输入内容。'));
    return false;
  }else if(form.find('input[name="topic_type"]').val()==''){
    loading_toggle();
    $('.publish-submit').button('reset');
    container.html(g_danger_alert('请选择类型。'));
    return false
  }else
    return true;
}
//生成警告框
function g_danger_alert(text){
  var str = '<div class="alert alert-danger alert-dismissible fade in" role="alert">'+
            '<button type="button" class="close " data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            '<strong>警告</strong>：'+text+'</div>';
  return str;
}



// 输入框获得焦点事件
$(document).on('focus','.to_input',function(e) {
  $(this).siblings('.comment-op').fadeIn('fast');
});
// 回复|评论关闭按钮事件，关闭输入框
$(document).on('click',".c_close",function(e) {
  // 评论处理回复
  var _self = $(this);
  var obj = _self.parents(".replay-input");
  var text = obj.find('.to_input').val();
  obj.fadeOut('fast').find('.to_input').val('');
  // 添加评论处理
  var c_obj = $('.add-comment');
  c_obj.fadeOut('fast').find('.comment-op').fadeOut('fast');
});



// 评论按钮事件，评论内容框打开
$(document).on('click',".i_reply",function(e) {
  var _self = $(this);
  var obj = _self.parents(".media-container").find('.replay-input');
  _self.toggleClass('active');
  _self.parents(".media-container.list").find(".replay-container").fadeToggle('fast');
  
});

// 回复按钮事件，回复输入框打开
$(document).on('click',".replay-to",function(e) {
  $(".media.media-container").find('.media-body .replay-input').fadeOut('fast');
  var _self = $(this);
  var reply_to_name = _self.siblings(".a_user").html();
  var obj = _self.parents(".replay-container").siblings('.replay-input');
  var input = obj.find(".to_input");
  var text = input.val();
  var reply_text = text+"@"+reply_to_name+":";
  input.val(reply_text+'');
  obj.fadeIn('fast');
  $("html,body").animate({scrollTop:obj.offset().top-150},300);
  input.focus();
});
$(document).on('click',".uninterested",function(e) {
  var _self = $(this);
  _self.parents(".m-sm.p-b-sm.border-b").fadeOut();

});




//判断图片大小是否溢出
function checkImgSise(){
  var obj = $('.upload-img-container').find('.up-img');
  for(var i = 0 ; i<obj.length;i++){
    var l = obj.eq(i).find('.up-img-item').attr('src');
    if(l.length>1400000)
      obj.eq(i).addClass('active').addClass('border-main');
  }
  if($('.upload-img-container').find('.up-img.active').length!=0){
    $.toaster({ priority : 'danger', title : '通知', message : '文件大小超过1M'});
    loading_toggle();
    return false;
  }
  return true;

}
