var imgStr = '';//图片上传字符串
var sign = 0;//提交标识
$(function(){
  $('.input[name="img[]"]').change(function(e) {
      var _img_up_container = $(this).siblings(".upload-img-container").find('.images.media-picture.over-h');
      var imgLength = _img_up_container.find('.up-img').length;
      var filesList = $(this).get(0).files;
      var imgUrlList = new Array();
      var allowLength ;
      if(imgLength+filesList.length>9)
        allowLength = 9-imgLength;
      else
        allowLength = filesList.length;
      for(var i=0;i<allowLength;i++){
        imgUrlList[i] = getObjectURL(filesList[i]);
      }
      
      for(var i=0;i<imgUrlList.length;i++){
        var img_item = '<div class="up-img"><img class="up-img-item" src="'+PUBLIC+'/img/imgloading.gif" alt="pictrue"> <a href="javascript:void(0)" class="up-img-delete">删除</a></div>';
        _img_up_container.append(img_item);
      }
      var j = 0;

      for(var k=0;k<allowLength;k++){
        fileChange(filesList[k], function(base64Img){
          _img_up_container.find('.up-img').eq(imgLength+j).find('img.up-img-item').attr('src',base64Img);
          j++;
        })
      }
   });
  //上传图片事件
  $('.img_uploap').click(function(e) {
    var _self = $(this);
    var _img_up_container = _self.parents('.comment-op').siblings(".upload-img-container").find('.images.media-picture.over-h');
    console.log(_img_up_container);
    var imgLength = _img_up_container.find('.up-img').length;
    if(imgLength<9)
      _self.parents('.comment-op').find('.input[name="img[]"]').click();
    else
      $.toaster({ priority : 'danger', title : '通知', message : '图片个数不得超过9'});
  });

  //点赞事件
  $('.i-zan').click(function(e) {
    var _self = $(this);
    var zan_id = _self.attr('data-id');
    var type = _self.attr('data-type');
    zan(zan_id,type,zan_url,_self);
  });
})
// 评论按钮事件，评论内容框打开
$(document).on('click',".i_reply",function(e) {
  var _self = $(this);
  var obj = _self.parents(".media-container").find('.replay-input');
  _self.toggleClass('active');
  _self.parents(".media-container.list").find(".replay-container").fadeToggle('fast');
  
});



function zan(zan_id,type,url,obj){
  var count = obj.find('.count').html();
  var IntCount = parseInt(count);
  if(obj.hasClass('active')){
    obj.find('.count').html(IntCount-1);
    obj.removeClass('active');
  }else{
    obj.find('.count').html(IntCount+1);
    obj.addClass('active');
  }
  $.ajax({
    url: url,
    type: 'post',
    dataType: 'html',
    data: {zan_id: zan_id,type:type},
    success:function(data){
      var result = $.parseJSON(data);
      if(result.Code !='200'){
        $.toaster({ priority : 'danger', title : '通知', message : result.Message});
        count = obj.find('.count').html();
        IntCount = parseInt(count);
        if(obj.hasClass('active')){
          obj.find('.count').html(IntCount-1);
          obj.removeClass('active');
        }else{
          obj.find('.count').html(IntCount+1);
          obj.addClass('active');
        }
      }
        
      
    },
    error:function(xhr){
      alert('请求失败')
    }
  });
  
}
//建立一個可存取到該file的url
function getObjectURL(file) {
    var url = null ;
    if (window.createObjectURL!=undefined) { // basic
        url = window.createObjectURL(file) ;
    } else if (window.URL!=undefined) { // mozilla(firefox)
        url = window.URL.createObjectURL(file) ;
    } else if (window.webkitURL!=undefined) { // webkit or chrome
        url = window.webkitURL.createObjectURL(file) ;
    }
    return url ;
}
//将图片文件转换成base64编码
function fileChange(f,callback) {
    var FR = new FileReader();
    var type = f.type;

    var orientation;
    //EXIF js 可以读取图片的元信息 https://github.com/exif-js/exif-js
    EXIF.getData(f,function(){
        orientation=EXIF.getTag(this,'Orientation');
    });
    if(type != 'image/gif')
      FR.onload = function(f) {
          compressImg(this.result,type,orientation,function(data){
            callback.call(this,data);
          });
      };
    else
      FR.onload = function(f) {
        callback.call(this,this.result);
      };
      FR.readAsDataURL(f); //先注册onload，再读取文件内容，否则读取内容是空的
}
//canvas压缩图片
function compressImg(imgData, type,dir, onCompress) {
    if (!imgData) return;
    
    onCompress = onCompress || function() {};
    var canvas = document.createElement('canvas');
    var img = new Image();
    img.onload = function() {
      var width = img.width;
      var height = img.height;
      //如果图片大于四百万像素，计算压缩比并将大小压至400万以下
      var ratio;
      if ( (3000000/(width*height)) <1) {
        ratio = (3000000 / (width*height)) ;
      }else{
        ratio = 1;
      }
      
      //重置canvans宽高 canvas.width = img.width; canvas.height = img.height;
      
      var u = navigator.userAgent;
      var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
      if(isiOS && typeof(dir) != "undefined"){
        var degree = 0;//旋转角度
        var drawWidth ;
        var drawHeight ; 
        switch(dir){
          case 1:
            drawWidth = width * ratio;
            drawHeight = height * ratio;
            canvas.width = Math.abs(drawWidth);
            canvas.height = Math.abs(drawHeight);
            degree=0;
            break;
          //iphone横屏拍摄，此时home键在左侧
          case 3:
            drawWidth = width * ratio;
            drawHeight = -height * ratio;
            canvas.width = Math.abs(drawWidth);
            canvas.height = Math.abs(drawHeight);
            degree=180;
            break;
          //iphone竖屏拍摄，此时home键在下方(正常拿手机的方向)
          case 6:
            drawWidth =  width * ratio;
            drawHeight = - height * ratio;
            canvas.width = Math.abs(drawHeight);
            canvas.height = Math.abs(drawWidth);
            degree=90;
            break;
          //iphone竖屏拍摄，此时home键在上方
          case 8:
            drawWidth = -height * ratio;
            drawHeight = width * ratio;
            canvas.width = Math.abs(drawHeight);
            canvas.height = Math.abs(drawWidth);
            degree=270;
            break;
        }
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height); // canvas清屏
        ctx.rotate(degree*Math.PI/180);
        ctx.drawImage(img,0,0,drawWidth,drawHeight); // 将图像绘制到canvas上 
      }else{
        canvas.height = height * ratio; 
        canvas.width = width * ratio;
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height); // canvas清屏
        ctx.drawImage(img,0,0,width * ratio,height * ratio); // 将图像绘制到canvas上 
      }
      onCompress(canvas.toDataURL(type)); //必须等压缩完才读取canvas值，否则canvas内容是黑帆布
    };
    // 记住必须先绑定事件，才能设置src属性，否则img没内容可以画到canvas
    img.src = imgData;

}
//发表话题添加照片删除操作
$(document).on('click','.up-img-delete',function(){
  var obj = $(this).parents('.up-img');
  obj.fadeOut(200);
  setTimeout(function(){
    obj.remove();
  },200)
})
//ajax上传图片 并设置图片名字符串
function ajax_upload_img(obj,url,btn){
    var base64ImgStr = obj.find('.up-img-item').attr('src');
    $.ajax({
      url: url,
      type: 'post',
      dataType: 'html',
      data: {img: base64ImgStr},
      success:function(data){
        var result = $.parseJSON(data);
        if(result.Code == '200'){
          imgStr = imgStr+result.ImgName+',';
          if(obj.index()!=obj.siblings().length){
            ajax_upload_img(obj.next(),url);
          }else{
            sign = 1;
          }
        }else if(result.Code=='205'){
          clearInterval(submit_sign);
          alert(result.Message);
          window.location.href = login_url+'?url='+this_url;
        }else{
            clearInterval(submit_sign);
            imgStr = '';
            loading_toggle();
            btn.button('reset');
            obj.addClass('active').addClass('border-main');
            $("#myModal").find('.modal-body .alert-container').html(g_danger_alert(result.Message));
        }
      },
      error:function(data){
        alert('请求失败');
      }
    })
}

