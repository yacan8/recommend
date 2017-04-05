// 文章显示列表改变点击事件js
	;$(function(){
		// $(".category").find('a').click(function(e) {
		// 	var _self = $(this);
		// 	if(_self.parent().hasClass('active'))
		// 		e.preventdefault();
		// 	else{
		// 		var type = _self.attr('data-type');
		// 		page = 1;
		// 		getList(type,page,url,_self.parent(),sections);
		// 	}
		// });
		$(".category").find('a').click(function(e) {
			var _self = $(this),
				li = _self.parent();
			if( li.hasClass('active') )
				e.preventDefault();
			else{
				li.siblings().removeClass('active');
				page = 1;
				var type = li.attr('data-type'),
					loadingFun = li.attr('data-loading-fun'),
					handleFun = loadingFunction[loadingFun],
					params = {type:type};
				handleFun(params,function(){
					li.append('<img src="'+PUBLIC+'/img/loading.gif">');
				}).then(function(result){
					$("#load").html('查看更多内容')
					li.addClass('active');
					li.find('img').remove();
					if ( result.success && result.html ) {
						var _list_container = $("#list_container");
						_list_container.html(result.html);
						$('.list-img>img').scrollLoading();
					} else {
						$.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : result.message});
					}
				});
			}
		});
		$("#load").click(function(e) {
			page++;
			var _self = $(this);
			if (_self.html() != '暂无更多内容'){
				var	li = $(".category").children('li.active').first(),
					type = li.attr('data-type');
				var handleFun = type == '-1' ? loadingFunction['recommend'] : loadingFunction['loading'],
					params = {type:type,page:page};
				handleFun(params,function(){
					_self.html('加载中 <img src="'+PUBLIC+'/img/loading.gif">')
				}).then(function(result){
					if ( result.success ) {
						var _list_container = $("#list_container");
						if ( result.html ) {
							_self.html('查看更多内容');
							_list_container.append(result.html);
							$('.list-img>img').scrollLoading();
						} else {
							_self.html('暂无更多内容');
						}
					} else {
						$.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : result.message});
					}
				})
			}
		});
	})
