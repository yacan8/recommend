// 搜索加载js
	;$(function(){
		$("#load").click(function(e) {
			var _self = $(this);
			page++;
			$.ajax({
	                url: url,
	                data:{key:key,page:page},
	                type: 'get',
	                dataType: 'text',
	                beforeSend:function(XMLHttpRequest){
                		_self.html('加载中 <img src="'+PUBLIC+'/img/loading.gif">')
                		
	                },
	                success:function(data,textStatus){
	                	var _list_container = $("#list_container");
	                	var load_btn = $("#load");
	                	if(data=='[]'||data==null){
	                		_list_container.append("<div class='m-b-sm p-md text-center' style='font-size:12px;'><hr>暂无数据</div>");
	                		load_btn.hide();
	                	}else{
	                		str = '';
	                		var dataObj = $.parseJSON(data);
							for (var i = 0 ; i < dataObj.length; i++) {
	                        	str += generate(dataObj[i]);
	                        };
	                        $("#list_container").append(str);
	                        _self.html("加载更多");
	                	}
	                },
	                error:function() {
	                    alert("请求失败");
	                }
	            });
		});
	})

