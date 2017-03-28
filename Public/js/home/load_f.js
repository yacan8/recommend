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
		                	str = '';
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
			function generate(data){
				data.type = data.type || '其他';
				data.user.icon = data.user.icon || 'default.jpg';
				var str ='<div class="m-t-sm p-l-sm p-l-sm  border-l-main">'+
	        				'<a href="'+data.url+'" class="list-img" style="background-image: url(\''+DATAPATH+'/news_thumb/'+data.image_thumb+'\')">'+
	            				'<span class="mask-tags" >'+data.type+'</span>' +
							'</a>'+
							'<div class="list-content">'+
	            				'<div class="to2">' +
									'<a class="title" href="'+data.url+'">'+data.title+'</a>' +
								'</div>'+
	            				'<div class="info">'+
	                				'<span class="font-smoothing tc-gray9"> ' +
					'<a href="#"><img class="i-user-icon" src="'+DATAPATH+'/login_thumb/'+data.user.icon+'" > ' +
					'</a><a href="#">'+data.user.nickname+'</a> ·' +' '+
					data.PublishTime+' · '+data.browse+'次浏览 · '+data.comment_count+' 次评论 ' +
					'</span>'+
								'</div>' +
							'</div>' +
						'</div><div class="list-hr m-l-sm m-r-sm m-t-sm"></div>';
	            return str;
			}
