// 文章列表改变显示方法js

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
	                success:function(data,textStatus){
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
		                	str = '';
		                    var dataObj = $.parseJSON(data);
		                    if(!BtnLoad){
	                        	object.siblings('a').removeClass('active');
								object.addClass('active');
	                        }
	                        if(dataObj!=null){
		                        for (var i = 0 ; i < dataObj.length; i++) {
		                        	str += generate(dataObj[i]);
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
	                },
	                error:function() {
	                    alert("请求失败");
	                }
	            });
			}
			function generate(data){
				var str ='<div class="bg-white m-t-md p-md overflow-auto border-l-main">'+
	        '<a href="'+data.url+'" class="list-img" style="background-image: url(\''+DATAPATH+'/news_thumb/'+data.image_thumb+'\')">'+
	            '<span class="mask-tags" style="background-color:'+data['type'].color+'">'+data.sections+'</span></a>'+
	        '<div class="list-content">'+
	            '<div class="to"><a class="title" href="'+data.url+'">'+data.title+'</a></div>'+
	            '<div class="info">'+
	                '<span class="time tc-gray6"><span title="发布时间" class="iconfont icon-fabushijian pull-left"> '+data.PublishTime+'</span><br class="visible-xs">'+
	                '<div class="tc-gray9  info-item-continer">'+
	                '<span title="浏览" class="iconfont icon-liulanliang m-r-md"> '+data.browse+'</span><span title="评论" class="iconfont icon-pingjia"> '+data.MessageCount+'</span>'+
	                '</div></div></div></div>';
	            return str;
			}
