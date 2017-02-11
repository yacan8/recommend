// 文章显示列表改变点击事件js
	;$(function(){
		$(".select_list").find('a').click(function(e) {
			var _self = $(this);
			if(_self.hasClass('active'))
				e.preventdefault();
			else{
				type = _self.attr('data-type');
				page = 1;
				getList(type,page,url,_self,sections);
			}
		});
		$("#load").click(function(e) {
			var _self = $(this);
			page++;
			var type;
			if($('body').find(".select_list").length==1){
				type = $(".select_list").find('.active').attr('data-type');
				getList(type,page,url,_self,sections);
			}
			else{
				getList('0',page,url,_self,sections);
			}
		});
	})

