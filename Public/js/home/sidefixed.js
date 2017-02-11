// 侧栏浮动JS
;
$(function() {
  var client = document.documentElement.clientWidth;
  var sldetop = $("#side-list").offset().top;
  // 右侧菜单fixed滚动
  $(document).scroll(function() {
    var footer = $("footer").offset().top;
    var footerheight = $("footer").height();
    var viewheight = $(window).height();
    var sidelistHeight = $("#side-list").height();
    if (client > 768) {
      var obj = $("#side-list");
      var scrolltop = $(document).scrollTop();
      var t = sidelistHeight + scrolltop + 60;
      if (scrolltop < sldetop) {
        obj.removeClass('fixed').css('marginTop', 0);
      } else if (t < footer) {
        obj.addClass('fixed').css('marginTop', 0);
      } else if (t > footer) {
        obj.removeClass('fixed').css('marginTop', footer - sldetop - sidelistHeight - 60);
      }
    }
  });
})