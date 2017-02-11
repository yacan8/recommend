// 忘记密码Js
;$(function(){
	//绑定提交按钮事件
	$(document).on('click','#form_submit',function(){
		var _SMS = $("#SMS");
		var _self = $(this);
		var _SMS_value = _SMS.val();
		_self.button('loading');
		$.ajax({
			url: check_SMS_url,
			type: 'post',
			dataType: 'text',
			data: {SMS: _SMS_value},
			success:function(data){
				var result = $.parseJSON(data);
				if(result.Code != '200'&&result.Code !="2"){
					alert(result.Message);
				}else if(result.Code == '2'){
					_SMS.addClass('has-error').siblings(".help-block").css("color","red").html(result.Message);
				}else{
					if(check_submit(_self))
						$("#f_form").submit();
				}
			},
			error:function(data){
				alert('请求失败');
			}
		})
	})
	//短信验证码输入框绑定数值修改事件
	$(document).on('change',"#SMS",function(){
		var _self = $(this);
		_self.removeClass('has-error').siblings(".help-block").html("");
	})


	//判断是否可提交
	function check_submit(obj){
		var bool = true;
		$.each($(".input-g"), function(i, n){
		  if($(this).find("input.from-input").hasClass('has-error')){
		  	bool = false;
		  	obj.button('reset');
		  }
		});
		return bool;
	}

	//手机号码改变事件
	$("#tel").change(function(event) {
		var _self = $(this);
		_self.removeClass('has-error');
		_self.siblings(".help-block").html("");
	});
	//用户名检验
	$("#first").on('click',function(event) {
		var _self = $(this);
		var _tel_input = $("#tel");
		var tel = _tel_input.val();
		var _form_container = $("#form_container");
		_self.button('loading');
		if(tel.length==11){
			var tel_geshi= /^((\(\d{3}\))|(\d{3}\-))?13\d{9}|14[57]\d{8}|15\d{9}|18\d{9}$/ ;
	        if(!tel.match(tel_geshi)){
	            _tel_input.addClass('has-error').siblings(".help-block").css("color","red").html("号码格式不正确");
	            _self.button('reset');
	        }else
	         {
	        	_tel_input.removeClass('has-error').siblings(".help-block").html('');
				$.ajax({
					url: check_url,
					type: 'post',
					dataType: 'text',
					data: {tel: tel},
					success:function(data){
						if(data=='2'){
							_tel_input.addClass('has-error').siblings(".help-block").css("color","red").html("该用户已不存在");
							_self.button('reset');
						}else{
							$.post(forget_user,{tel:tel},function(data){})
							_form_container.html(second());
						}
					},
					error:function(data){
						alert('请求失败');
					}
				})
			}
		}else{
	        _tel_input.addClass('has-error').siblings(".help-block").css("color","red").html("号码格式不正确");
		}
	});
	
	function second(){
		var str ='<div class="input-g m-r-110">'+
                 '<span class="input-icon glyphicon glyphicon-check"></span>'+
                 '<input id="verify" class="from-input" type="text" placeholder="图形验证码" />'+
                 '<img class="verify" src="'+verify_src+'" title="看不清？点击刷新验证码">'+
                 '<span class="help-block m-t-sm"></span>'+
                 '</div>'+
                 '<div class="input-g m-r-130">'+
                 '<span class="input-icon glyphicon glyphicon-eye-open"></span>'+
                 '<input id="SMS" class="from-input" name="SMS" type="text" placeholder="短信验证码" title="点击获取短信验证码" />'+
                 '<a id="send" href="javascript:void(0)" class="btn" >点击发送短信</a>'+
                 '<span class="help-block m-t-sm"></span>'+
                 '</div>'+
				 '<div class="input-g">'+
                 '<span class="input-icon glyphicon glyphicon-ice-lolly"></span>'+
                 '<input class="from-input" id="password" type="password" name="password" placeholder="密码" />'+
                 '<span class="help-block m-t-sm"></span>'+
                 '</div>'+
                 '<div class="input-g">'+
                 '<span class="input-icon glyphicon glyphicon-ice-lolly"></span>'+
                 '<input class="from-input" id="repassword" type="password" name="repassword" placeholder="确认密码" />'+
                 '<span class="help-block m-t-sm"></span>'+
                 '</div>'+
                 '<div class="input-g">'+
                 '<button id="form_submit" data-loading-text="Loading..." type="button" class="btn-submit btn-danger btn-block">重置密码</button>'+
                 '</div>';
        return str;
	}
})