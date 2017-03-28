$(document).on('click','[data-role="remove"]',function(){
	$(this).parent('.dd-item').remove();
})
$(document).on('mouseout','.dd-load-list',function(){
	var dd_input = $('[data-role="dd-input"]');
	dd_input.bind('blur',dd_input_blur);
})
$(document).on('mouseover','.dd-load-list li',function(){
	var _self = $(this);
	_self.siblings('li').removeClass('active');
	_self.addClass('active');
	var dd_input = $('[data-role="dd-input"]');
	dd_input.unbind('blur');
})
$(document).on('click','.dd-load-list li',function(){
	add_dd_item();
	var dd_input = $('[data-role="dd-input"]');
	dd_input.val('');
	dd_input.bind('blur',dd_input_blur);
})

function dd_input_blur(){
	$(this).val('');
	$(".dd-load-list").hide();
}
$(function(){
	$("#submit").click(function(){
		var _self = $(this);
		_self.button('loading');
		var container = $(".dd-container");
		var item = container.children('.dd-item');
		var length = item.length;
		if(length>0){
			var str = '';
			for(var i=0;i<length;i++){
				if(i==0){
					str = str + item.eq(i).attr('data-id');
				}else{
					str = str +','+item.eq(i).attr('data-id');
				}
			}
			if($.trim($("input[name='title']").val())==''){
				$.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '请输入标题'});
				_self.button('reset');
				return false;
			}else if($.trim($("#dd_content").val())==''){
				$.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '请输入内容'});
				_self.button('reset');
				return false;
			}
			$("input[name='space']").val(str);
		}else{
			$.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '至少选择一个旅游地点'});
			_self.button('reset');
			return false;
		}
	})
	var lastTime;
	$('[data-role="dd-input"]').keyup(function (e) {
		var _self = $(this);
		var load_list =$(".dd-load-list");
		lastTime = e.timeStamp;
		setTimeout(function () {
			if (lastTime - e.timeStamp == 0) {
				if(e.keyCode != 40 && e.keyCode != 39 && e.keyCode != 13){
					var value = $.trim(_self.val());
					if(value!=''){
						$.get(dd_load_url,{word:value},function(data) {
							var result = $.parseJSON(data);
							var str ='';
							if(result.length>0){
								var sign = false;
								for(var i=0;i<result.length;i++){
									str = str + '<li data-id="'+result[i].id+'"><a href="javascript:;"><span>'+result[i].name+'</span></a></li>';
									if($.trim(result[i].name) == value){
										sign = true;
									}
								}
								if(!sign)
									str += '<li data-id="0"><a href="javascript:;">创建关键字 <span>'+value+'</span></a></li>';
							}else{
								str = '<li data-id="0"><a href="javascript:;">创建关键字 <span>'+value+'</span></a></li>';
							}
							var con = _self.parents('.form-group');
							var left = _self.offset().left-con.offset().left-5;
							$('.dd-load-list').html(str);

							load_list.css('left',left).show().children('li').first().addClass('active');
						});
					}
				}
			}
		}, 500);
	})
	$('[data-role="dd-input"]').keydown(function(e) {
		var _self = $(this);
		var _vaule = _self.val();
		var load_list =$(".dd-load-list");
		// console.log(e.keyCode);
		if(e.keyCode == 40 || e.keyCode == 38){
			//按键↓ 或者 按键↑
			var list_li = load_list.children('li');
			var index = load_list.children('li.active').index();
			if(e.keyCode == 40){
				//按键↓
				if(index+1<list_li.length){
					list_li.removeClass('active').eq(index+1).addClass('active');
				}
			}else{
				//按键↑
				if(index>0){
					list_li.removeClass('active').eq(index-1).addClass('active');
				}
			}
		}else if(e.keyCode == 13){
			//按键Enter
			var load_list =$(".dd-load-list");
			var obj = load_list.children('li.active');
			if( obj.length > 0 ){
				add_dd_item();
				_self.val('');
			}
			return false;
		}else if(e.keyCode == 188){
			$.toaster({ priority : 'danger', title : '<span class="glyphicon glyphicon-info-sign"></span>', message : '逗号 , 为非法字符'});
			_self.val(_vaule.substring(0,_vaule.length-1));
		}
	});
	$('[data-role="dd-input"]').bind('blur',dd_input_blur);
})

var g_dd_item_str = function(id,name){
	var str = '<span data-id="'+id+'" class="dd-item">'+name+'<span data-role="remove">×</span></span>';
	return str;
}
var add_dd_item = function(){
	var load_list =$(".dd-load-list");
	var obj = load_list.children('li.active');
	if( obj.length > 0 ){
		var id = obj.attr('data-id');
		var keyword = id == '0'? obj.find('span').text() : obj.get(0).childNodes[0].textContent;
		var name = obj.children('a').children('span').html();
		var dd_item_container = $('.dd-container');
		var dd_item_obj = $('.dd-container').children('.dd-item');
		var dd_length = dd_item_obj.length;
		var dd_item_str = g_dd_item_str(id,name);
		if(dd_length==0)
			dd_item_container.prepend(dd_item_str);
		else{
			var bool = true;
			for(var j=0;j<dd_item_obj.length;j++){
				var data_id = dd_item_obj.eq(j).attr('data-id');
				var _keyword = dd_item_obj.eq(j).get(0).childNodes[0].textContent;
				if(data_id == id && data_id != '0' || _keyword == keyword){
					bool =false;
				}
			}
			if(bool)
				dd_item_obj.last().after(dd_item_str);
		}
		load_list.html('').hide();
	}

}
