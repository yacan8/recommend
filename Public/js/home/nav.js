// 导航JS
  // onresize = function () {
  //     location.reload();
  // }
$(function () {
  var m_message_icon = $('.m-message-icon');
  if(m_message_icon.length == 0){
    $(".pc-message").parents('li').remove();
  }else{
    var m_message = $('.m-message');
    if(m_message.length != 0){
      $(".pc-message").html(m_message.text());
    }else{
      $(".pc-message").remove();
    }
  }
  
  

  $(document).scroll(function(event) {
    var scrolltop = $(document).scrollTop();
    if(scrolltop>100){
      $(".ttop").fadeIn();
    }else{
      $(".ttop").fadeOut();
    }
  });
  $(".ttop").click(function(event) {
     $('body,html').animate({ scrollTop: 0 }, 200);
  });
  var client = document.documentElement.clientWidth ;
  //导航条
  if(client<768){
    $("meta[name='viewport']").attr('content','width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no');
  }

    $("#search").click(function(){
        $(".nav-s").fadeToggle();
    });
    $(".nav-search-item li").find("a").click(function(){
        $("#select-item").html($(this).html());
    });
    $("li.dropdown").hover(function(){
      if(client > 768){
        $(this).toggleClass('open');
      }
    })
    $("li.dropdown").click(function(event){
      if(client > 768){
        var e=window.event || event;
         if(e.stopPropagation){
            e.stopPropagation();
          }else{
            e.cancelBubble = true;
         }
      }
    })
    $("#toggle-search").click(function(){
      $("#nav-search").toggleClass('open');
    })

    $("#search-toggle").click(function(){
      $("#bs-example-navbar-collapse-1").removeClass('in');
      $(document.body).toggleClass('search-on');
      $("#xs-search").show().fadeIn().find("input").focus();

    })

    $("#search-close").click(function(){
      $(document.body).toggleClass('search-on');
      $("#xs-search").hide();
    })
    //end 导航条
})