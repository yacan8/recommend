//加载中mask生成
function loading_toggle(){
	var _body = $('body');
   if( _body.find('.wait-publish').length==0)
     _body.prepend('<div class="wait-publish"><img src="'+PUBLIC+'/img/publishLoading.gif"></div>');
   _body.find('.wait-publish').fadeToggle('fast');
   if(_body.css('overflow')=='visible')
      _body.css('overflow','hidden');
   else
      _body.css('overflow','visible');
}