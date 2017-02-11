// 登录JS
;$(function(){
	//用户名ajax检查
	$('#form_submit').on('click',function(event) {
		var _self = $(this);
		var bool = true;
		$.each(_self.parents('form').find('input'), function(index, val) {
			 if($(this).val()=='')
			 	bool=false;
		});
		if(bool)
			$(this).button('loading');
		else
			return false;
	});
})