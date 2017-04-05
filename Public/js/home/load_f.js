// 文章列表改变显示方法js

var loadingFunction = {
	recommend:function(params,beforeSend){
		var url = ROOT + '/index.php/Recommend/recommend.html';
		return loadingFunction.loadingFun(url,params,beforeSend).then(function(result){
			if ( result.success ) {
				result.html = loadingFunction.dataHandle(result.attr);
			}
			return result;
		})
	},
	loading:function(params,beforeSend) {
		var url = ROOT + '/index.php/News/load.html';
		return loadingFunction.loadingFun(url,params,beforeSend).then(function(result){
			if ( result.success ) {
				result.html = loadingFunction.dataHandle(result.attr);
			}
			return result;
		})
	},
	loadingFun:function(url,params,beforeSend){
		var params = Object.keys(params).map(function(key){return key+'='+params[key]}).join('&');
		if(beforeSend){beforeSend();}
		window.ajax_loading = true;
		return fetch(url+'?'+params, {credentials: "include"}).then(function(res){window.ajax_loading = false;return res.json()});
	},
	dataHandle:function(data){
		var result = '';
		for (var i = 0 ; i < data.length; i++) {
			data[i].ROOT = ROOT;
			data[i].DATAPATH = DATAPATH;
			result += template('news-item',data[i]);
		}
		return result;
	}
}



			;function getList(type,page,url,object,sections){
				var BtnLoad;
				if(object.hasClass('btn')){
					BtnLoad =true;
				}
				$.ajax({
	                url: url,
	                data:{type:type,page:page,sections:sections},
	                type: 'get',
	                dataType: 'text',
	                beforeSend:function(XMLHttpRequest){
                		if(BtnLoad)
                			object.html('加载中 <img src="'+PUBLIC+'/img/loading.gif">')
                		else
                			object.append('<img src="'+PUBLIC+'/img/loading.gif">');
	                },
	                success:function(data){
						var _list_container = $("#list_container");
	                	var _load_btn = $("#load");
	                	if(data==null||data=='[]'){
	                		_load_btn.hide();
	                		if(!BtnLoad){
		                        _list_container.html("<div class='m-b-sm p-md text-center' style='font-size:12px;'><hr>暂无数据</div>");
	                        }else{
	                        	_list_container.append("<div class='m-b-sm p-md text-center' style='font-size:12px;'><hr>暂无数据</div>");
	                        	object.html('加载更多');
	                        }
	                	}else{
		                	var str = '';
		                    var dataObj = $.parseJSON(data);
		                    if(!BtnLoad){
	                        	object.siblings().removeClass('active');
								object.addClass('active');
	                        }
	                        if(dataObj!=null){
		                        for (var i = 0 ; i < dataObj.length; i++) {
									dataObj[i].ROOT = ROOT;
									dataObj[i].DATAPATH = DATAPATH;
		                        	str += template('news-item',dataObj[i]);
		                        };
	                        }
	                        if(!BtnLoad){
	                        	if(dataObj!=null){
			                        _list_container.html(str);
			                        _load_btn.show();
			                    }
	                        }
	                        else{
	                        	if(dataObj!=null){
	                        		_list_container.append(str);
	                        		object.html('加载更多');
	                        	}
	                        }
		                    object.find('img').remove();
	                	}
						$('.list-img>img').scrollLoading();

					},
	                error:function() {
	                    alert("请求失败");
	                }
	            });
			}
