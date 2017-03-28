// 注册JS
;$(function(){

	//昵称检查
	// $("#nickname").blur(function(event) {
	// 	var _self = $(this);
	// 	var nickname = _self.val();
	// 	$.ajax({
	// 		url: check_nickname_url,
	// 		type: 'get',
	// 		dataType: 'text',
	// 		data: {nickname: nickname},
	// 		success:function(data){
	// 			var result = $.parseJSON(data);
	// 			if(result.Code != '200'){
	// 				_self.addClass('has-error').siblings('.help-block').css("color","red").html(result.Message);
	// 			}else{
	// 				_self.removeClass('has-error').siblings('.help-block').html('');
	// 			}
	// 		},
	// 		error:function(data){
	// 			alert('请求失败');
	// 		}
	// 	})
	// });
	//提交按钮检测验证码正确性
	$('#form_submit').on('click',function(event) {
		var _self = $(this);
		var _SMS = $("#SMS");
		var SMS = _SMS.val();
		_self.button('loading');
		$.ajax({
			url: check_SMS_url,
			type: 'post',
			dataType: 'text',
			data: {SMS: SMS},
			success:function(data){
				var result = $.parseJSON(data);
				if(result.Code != '200'){
					_SMS.siblings('.help-block').css("color","red").html(result.Message);
					_self.button('reset');
				}else{
					var objarr = $('.from-input');
					var submit = true;
					for(var i=0 ; i<objarr.length;i++){
						if(objarr.eq(i).hasClass('has-error'))
							submit = false;
					}
					if(submit)
						$("#r_form").submit();
					else
						_self.button('reset');
				}
			},
			error:function(data){
				alert('请求失败');
			}
		})
	});
	
	//验证码检测
	$('#SMS').on('change',function(event) {
		var _SMS = $(this);
		var SMS = _SMS.val();
		$.ajax({
			url: check_SMS_url,
			type: 'post',
			dataType: 'text',
			data: {SMS: SMS},
			success:function(data){
				var result = $.parseJSON(data);
				if(result.Code == '200'){
					_SMS.siblings('.help-block').css("color","red").html("");
				}
			},
			error:function(data){
				alert('请求失败');
			}
		})
	})
	

	
		//用户名ajax检查
	$("#tel").blur(function(event) {
		var _self = $(this);
		var tel = _self.val();
        if(tel.length!=11||!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0-9])\d{8}$/i.test(tel)){
            _self.addClass('has-error');
            _self.siblings(".help-block").css("color","red").html("请输入正确的手机号码");
        }
        else{
        	_self.removeClass('has-error');
			_self.siblings(".help-block").html('');
			$.ajax({
				url: check_url,
				type: 'post',
				dataType: 'text',
				data: {tel: tel},
				beforeSend:function(state){
					$("#username_check").show();
				},
				success:function(data){
					$("#username_check").hide();
					if(data=='1'){
						_self.addClass('has-error');
						_self.siblings(".help-block").css("color","red").html("用户名已存在");
					}else{
						_self.removeClass('has-error');
						_self.siblings(".help-block").html('');
					}
				},
				error:function(data){
					alert('请求失败');
				}
			})
		}

	});
})