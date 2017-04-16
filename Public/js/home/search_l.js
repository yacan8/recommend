
;$(function(){
		$("#load").click(function(e) {
			var _self = $(this);
			page++;
			$.ajax({
	                url: url,
	                data:{key:key,page:page},
	                type: 'get',
	                dataType: 'json',
	                beforeSend:function(XMLHttpRequest){
                		_self.html('加载中 <img src="'+PUBLIC+'/img/loading.gif">')
	                },
	                success:function(data,textStatus){
						template.config("escape", false);

						var result = '';
						for (var i = 0 ; i < data.length; i++) {
							data[i].ROOT = ROOT;
							data[i].DATAPATH = DATAPATH;
							// data[i].title = htmlDecode(data[i].title).replaceAll(key,'<span style="color:red">'+key+'</span>');
							result += template('news-item',data[i]);
						}
						$("#list_container").append(result);
						if(data.length)
							_self.html("加载更多");
						else
							_self.html('暂无更多内容');

	                },
	                error:function() {
	                    alert("请求失败");
	                }
	            });
		});
	})

