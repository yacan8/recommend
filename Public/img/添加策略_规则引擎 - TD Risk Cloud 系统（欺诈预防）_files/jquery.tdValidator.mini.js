// JavaScript Document
;(function(){
    // List reg
    
    regChinese= new RegExp(/^[\u4e00-\u9fa5]+$/);  // 仅中文
    regFullNumber= new RegExp(/^\d+$/);  // 仅数字
    regNotEmpty= new RegExp(/^[\S]+$/);  // 非空不包含空格
    regNotEmptyAll= new RegExp(/^[\s\S]+$/);  // 非空包含空格
    regWd= new RegExp(/^[A-Za-z0-9\u4E00-\u9FA5\_]{2,16}$/);  // 字母数字下划线
    regWd32= new RegExp(/^[A-Za-z0-9\u4E00-\u9FA5\_]{2,32}$/);  // 字母数字下划线
    regWd50= new RegExp(/^[A-Za-z0-9\u4E00-\u9FA5\_]{2,50}$/);  // 数字，字母长度2~50.至少匹配一个汉字
    regWd50AddField=new RegExp(/^[a-zA-Z0-9_]{2,50}$/);//数字字母下划线2~50 不能有汉字
    regWc= new RegExp(/^[A-Za-z0-9\_]+$/);
    regWe= new RegExp(/^[A-Za-z\_]+$/);
    regWf= new RegExp(/(^[A-Za-z0-9\u4E00-\u9FA5\.\_\,]+$)|(^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$)/);  // 字母数字下划线
    regWg= new RegExp(/^[A-Za-z0-9\u4E00-\u9FA5\_\/]{2,32}$/);  // 字母数字下划线 
    regMail= new RegExp(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/);  // 邮箱帐号验证
    regphone= new RegExp(/(^0?(13|14|15|18)[0-9]{9}$)|(^(0[0-9]{2,3}-?)?([2-9][0-9]{6,7})+(-[0-9]{1,4})?$)/);  // example:15155298221 or 0517-4665726
    regtel= new RegExp(/^(([0\+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/);   //电话号码的函数(包括验证国内区号,国际区号,分机号)
    regIdcard= new RegExp(/(^\d{15}$)|(^\d{17}([0-9]|X)$)/);  //15位或18号码可以有x
    regname= new RegExp(/^[A-Za-z0-9_\-\u4e00-\u9fa5]+$/);  //4-20位字符，可由英文、数字及“-”、“_”组成
    regNameMail= new RegExp(/(^[A-Za-z0-9_\-\u4e00-\u9fa5]+$)|(^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$)/);
    regNickName= new RegExp(/^\D[^\s]*\S$/);  // 昵称校验，不能以数字，空格开头，可以是中文，数字和英文
    regNickNameBase= new RegExp(/^[\u4e00-\u9fa5a-zA-Z][\u4e00-\u9fa5\w\d\-_]{1,17}$/i);  // 昵称校验，不能以数字，空格开头，可以是中文，数字和英文 - _ 2-18 位
    regRealName= new RegExp(/(^[A-Za-z]{2,16}$)|(^[\u4e00-\u9fa5]{2,16}$)/);  // 真实姓名
    regCompanyName= new RegExp(/^[A-Za-z0-9_()（）\-\u4e00-\u9fa5]+$/);  // 公司名称
    regCompanyAddr= new RegExp(/^[A-Za-z0-9_()（）\#\\-\u4e00-\u9fa5]+$/);  // 公司地址
    regurl= new RegExp(/^http[s]?:\/\/([\w-]+\.)+[\w-]+([\w-./?%&=]*)?$/);    // url
    regpassword=new RegExp(/^(?=.*[0-9].*)(?=.*[a-zA-Z].*).{6,}$/);  // 6~32个字符，区分大小写，必须同时包含字母和数字。
    oldregpassword=new RegExp(/^[A-Za-z0-9]{6,32}$/);  // 6~32个字符，区分大小写
    regMobilePhoto=new RegExp(/^(1[1-9])\d{9}$/);  
    regPositiveInteger = new RegExp(/^[1-9]\d*$/); //正整数
    regPositiveIntegerUnder = new RegExp(/^(0|[1-9]\d?|100)$/); //100以内正整数
    regInteger = new RegExp(/(^[1-9]\d*$)|(^(\-[1-9])\d*$)|(^\s*$)/); //正负整数或空字符串

	// start tdValidator
	$.fn.tdValidator = function(a) {
		var b = {
			subFormId : "subform",
			RegName : "reg list name",
			blurEmpty : "失去焦点为空的时候",
			blurFit : "输入正确",
			blurUnfit : "失去焦点规则不匹配的文字",
			onFoucs : "焦点的时候提示文字",
			submitEmpty : "提交时input为空"
		};
		var a = $.extend(b, a);
		this.each(function() {
			var f = $(this);
			var c = this.id + "Tip";
			var e = $("#" + c);
			f.attr("data-vdCode", 1);
			f.keyup(function(g) {
				$("#" + a.subFormId).submit(function(h) {
					d();
				});
				d();
			});
			f.focus(function(g) {
				e.removeClass().text(a.onFoucs).addClass("onFocus");
			});
			function d() {
				if (f.val() == "") {
					e.text(a.blurEmpty);
					f.attr("data-vdCode", 0);
					f.siblings("em").text("").removeClass().addClass("error");
					return;
				}
				if (!a.RegName.test(f.val())) {
					e.removeClass().text(a.blurUnfit).addClass("onError");
					f.attr("data-vdCode", 0);
					f.siblings("em").text("").removeClass().addClass("error");
				} else {
					e.removeClass().text(a.blurFit).addClass("onCorrect");
					f.attr("data-vdCode", 1);
					f.siblings("em").text("").removeClass().addClass("success");
				}
			}
			$("#" + a.subFormId).submit(function() {
				if ($(this).find("input").not(".empty").val() == "") {
					e.text(a.submitEmpty);
					f.attr("data-vdCode", 0);
					f.siblings("em").text("").removeClass().addClass("error");
					return false;
				}
			});
			$("#" + a.subFormId).find("input:text").keydown(function(g) {
				if (g.keyCode == "13") {
					d();
				}
			});
		});
		return this;
	};
	// End tdValidator
    
	// start compareValidator
	$.fn.compareValidator = function(a) {
		var b = {
			compareID : "password1",
			operateor : "=",
			onError : "两次密码输入不一致"
		};
		var a = $.extend(b, a);
		$(this).each(function() {
			var f = $(this);
			var d = this.id + "Tip";
			var e = $("#" + d);
			f.live("keyup", function(g) {
				c()
			});
			f.keydown(function(g) {
				if (g.keyCode == "13") {
					c()
				}
			});
			function c() {
				if (f.val() == $("#" + a.compareID).val()) {
					f.attr("data-vdCode", 1);
					f.siblings("em").text("").removeClass().addClass("success");
					return;
				} else {
					e.removeClass().text(a.onError).addClass("onError");
					f.attr("data-vdCode", 0);
					f.siblings("em").text("").removeClass().addClass("error");
				}
			}
	
		});
		return this
	};
	//End compareValidator
    // start compareNum
    $.fn.compareNum = function(a) {
        var b = {
            subFormId: "subform",
            RegName: "reg list name",
            blurEmpty: "请填写长度值，Number类型",
            blurFit: "输入正确",
            blurUnfit: "失去焦点规则不匹配的文字",
            onFoucs: "焦点的时候提示文字",
            submitEmpty: "请填写长度值，Number类型",
            minNum: 2,
            maxNum:10
        };
        var a = $.extend(b, a);
        this.each(function() {
            var f = $(this);
            var c = this.id + "Tip";
            var e = $("#" + c);
            f.attr("data-vdCode", 0);
            
            f.keyup(function(g) {
                $("#" + a.subFormId).submit(function(h) {
                    d();
                });
                d();
            });
            f.focus(function(g) {
                e.removeClass().text(a.onFoucs).addClass("onFocus");
            });

            function d() {
                if (f.val() == "") {
                    e.text(a.blurEmpty);
                    f.attr("data-vdCode", 0);
                    f.siblings("em").text("").removeClass().addClass("error");
                    return;
                }
                if (!a.RegName.test(f.val())) {
                    e.removeClass().text(a.blurUnfit).addClass("onError");
                    f.attr("data-vdCode", 0);
                    f.siblings("em").text("").removeClass().addClass("error");
                } else {
                    if(f.val()>=a.minNum&&f.val()<=a.maxNum){
                        e.removeClass().text(a.blurFit).addClass("onCorrect");
                        f.attr("data-vdCode", 1);
                        f.siblings("em").text("").removeClass().addClass("success");
                    }else{
                        e.removeClass().text(a.blurUnfit).addClass("onError");
                        f.attr("data-vdCode", 0);
                        f.siblings("em").text("").removeClass().addClass("error");
                    }
                }
            }
        });
        return this;
    };
    // End compareNum
    // start inputValidator
    $.fn.inputValidator=function(a){var b={minLen:0,maxLen:Number.MAX_VALUE,lenTip:"您输入的文本长度不正确",hasSpace:false,emptyError:"您输入的内容不能有空符号"};var a=$.extend(b,a);$(this).each(function(){var g=$(this);var c=this.id+"Tip";var f=$("#"+c);g.live("blur",function(){if(a.hasSpace==true){return}else{d()}e()});function d(){if(/\s+/g.test(g.val())){f.removeClass().text(a.emptyError).addClass("onError");g.attr("data-vdCode",0);g.siblings("em").text("").removeClass().addClass("error")}else{return}}function e(){if(!a.minLen==0&&!a.maxLen==0){if(g.val().length<a.minLen|g.val().length>a.maxLen){f.removeClass().text(a.lenTip).addClass("onError");g.attr("data-vdCode",0);g.siblings("em").text("").removeClass().addClass("error")}}}});return this};
    //End inputValidator
    $.fn.splitValidator=function(options){
    	var compareItem = {
    			minLen: 0,        // 最小值为0的时候表示无限制
    			maxLen: 0,        // 最大值为0的时候表示无限制
    			splitRegx:",",    // 默认逗号分隔
    			lenTip:"您输入的文本长度不正确"
        };
    	var options = $.extend(compareItem,options);
    	$(this).each(function() {
    		var _this = $(this);
    		var messageId=this.id+'Tip';
    	    var messageTip=$("#"+messageId);
        	
        	_this.on("focus",function(){
        		messageTip.prev("em").removeClass();
    	    	messageTip.text('').removeClass().addClass("onCorrect");
        	});
        	_this.on("change blur",function(){
        		var values = _this.val().split(compareItem.splitRegx);
        		var flag = true;
        		for(var i=0,len=values.length;i<len;i++){
        			if(!values[i]){
        				flag = false;
        				if(!flag){
        	    			break;
        	    		}
            		}
        	    	if(compareItem.minLen){
        	    		flag = values[i].length>=compareItem.minLen;
        	    		if(!flag){
        	    			break;
        	    		}
        	    	}
        	    	if(compareItem.maxLen){
        	    		flag = values[i].length<=compareItem.maxLen;
        	    		if(!flag){
        	    			break;
        	    		}
        	    	}
        	    }
        	    if(!flag){
        	    	messageTip.prev("em").removeClass().addClass("error");
        	    	messageTip.text(compareItem.lenTip).removeClass().addClass("onError");
        	    	_this.attr("data-vdcode",0);
        	    }else{
        	    	messageTip.prev("em").removeClass().addClass("success");
        	    	messageTip.text('').removeClass().addClass("onCorrect");
        	    	_this.attr("data-vdcode",1);
        	    }
        	});
        });
        
    	return this;
    };
    
})(jQuery);
;(function($){
    $.extend({
        validResult:function(target){
            var results={result:0,resultList:[]};
            $(".pure-control-group",target).each(function(){
                var _this =this;
                var result={target:_this};
                if($(this).hasClass("unValidate")){
                	return true;//continue
                }
                if($("[data-vdcode]",this).length){
                	result.value = $("[data-vdcode]",this).attr("data-vdcode")==1?1:0;
                	if($(this).prop("readonly")){
                		result.value = 1;
                	}
                }
                if($(".ui-select",this).length){
                	result.value = $(".ui-select",this).selectValue()?1:0;
                }

                if ($('.input-radio', this).length) {
                    result.value = $('.input-radio:checked', this).length ? 1 : 0;
                }
                results.resultList.push(result);

                if(result.value){
                    results.result+=1;
                }
            });
            results.result == $(".pure-control-group",target).not(".unValidate").length ? results.result = true : results.result = false;
            return results;
        }
    });
})(jQuery);
// 通用规则验证
// Edit By Rookie 

$(function(){
    // 网址
    // 公司网址暂时不用了
    $("#hereiswebsite").tdValidator({
        RegName:regurl,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误，请输入正确的网址',
        onFoucs:'请输入正确的网址'}).inputValidator({
            hasSpace:false,
            minLen: 6, 
            lenTip:"请输入正确的网址"
        });
    // 用户名校验
	$("#loginId,#u_loginId,#oneKeyLoginId").tdValidator({
        RegName:regMail,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误，请输入正确的电子邮箱',
        onFoucs:'请输入邮箱作为用户名'}).inputValidator({
            hasSpace:false,
            minLen: 6, 
            lenTip:"请输入正确的电子邮箱"
        });
    // 用户名校验
    $("#nickName,#u_nickName,#oneKeyNickName").tdValidator({
        RegName:regNickNameBase,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'不能以数字开头,且不能有空格,由2－18位中文、英文、数字及"-"、"_"组成',
        onFoucs:'由中文、英文、数字及"-"、"_"组成'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:18,
            lenTip:"您输入的文本长度必须在2~18之间"
        }); 
    // 真实姓名校验
    $("#realName,#u_realName,#oneKeyRealName").tdValidator({
        RegName:regRealName,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'由中文或英文组成'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:16,
            lenTip:"真实姓名文本长度在2~16之间"
        }); 

    // 密码校验 老密码验证
    //$("#oldPass").tdValidator({
    //    RegName: regNotEmptyAll, // 长度不能为空
    //    blurEmpty:'',
    //    blurFit:'',
    //    blurUnfit:'密码不能为空',
    //    onFoucs:'密码不能为空'}).inputValidator({
    //        hasSpace: false,
    //        minLen: 1,
    //        lenTip: "密码不能为空"
    //    });
    // newPasswd在用户管理、重置密码
    $("#password,#addPassword,#newPasswd,#appAdminPassword,#oneKeyAddPassword").tdValidator({
        RegName:regpassword,// 以字母开头，长度在6~32之间，只能包含字符和数字
        blurEmpty:'',
        blurFit:'',
        blurUnfit:'区分大小写，必须同时包含字母和数字',
        onFoucs:'6~32个字符，区分大小写，必须同时包含字母和数字'}).inputValidator({
            hasSpace:false,
            minLen: 6, 
            maxLen:32,
            lenTip:"密码长度在6~32之间"
        }); 
               
    // 新密码校验
    $("#newPass").tdValidator({
        RegName:regpassword,// 以字母开头，长度在6~32之间，只能包含字符和数字
        blurEmpty:'',
        blurFit:'',
        blurUnfit:'您输入的密码格式不正确',
        onFoucs:'6~32个字符，区分大小写，必须同时包含字母和数字'}).inputValidator({
            hasSpace:false,
            minLen: 6, 
            maxLen:32,
            lenTip:"密码长度在6~32之间"
        }); 
        
    // 重复新密码
    $("#reNewPass").tdValidator({
        RegName:regpassword,// 以字母开头，长度在6~32之间，只能包含字符和数字
        blurEmpty:'请重复您的密码',
        blurFit:'',
        blurUnfit:'您输入的密码格式不正确',
        onFoucs:'请重复您的密码'}).compareValidator({
            compareID: "newPass",
            onError: "两次密码输入不一致，请重新输入"
        });
        
    // 手机号校验
    $("#phone,#u_phone,#oneKeyPhone").tdValidator({
        RegName:regMobilePhoto,// 手机号码验证
        blurEmpty:'请输入11位手机号码',
        blurFit:'',
        blurUnfit:'您输入的手机号格式不正确',
        onFoucs:'示例：15921554956',
        submitEmpty:'请输入正确的手机号'}).inputValidator({
            hasSpace:false,
            lenTip:"手机号为11位"
        });
    
    
    // 公司名称校验
    $("#addDisplayName,#editDisplayName,.addDisplayName,.editDisplayName,#oneKeyAddDisplayName").tdValidator({
        RegName:regCompanyName,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'由中文、英文、数字及"-"、"_"组成'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:32,
            lenTip:"名称文本长度在2~32之间"
        });
        
     // 管理员邮箱校验
    $("#addAdmin, #editAdmin,#oneKeyAddAdmin").tdValidator({
        RegName:regMail,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'请填写正确有效的邮箱'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:48,
            lenTip:"邮箱格式不正确"
        });
    $("#addAlias,#editAlias").tdValidator({
        RegName:regWg,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'由英文、数字、中文、"_"、"/"组成'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:32,
            lenTip:"您输入的文本长度必须在2~32之间"
        }); 
    // 字母、数字、下划线
    $("#addIDSA,#addID,#displayName,#u_displayName,#editKey").tdValidator({
        RegName:regWd,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'由英文、数字、中文、"_"组成'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:16,
            lenTip:"您输入的文本长度必须在2~16之间"
        });
    //#addKey
    $('#addKey').tdValidator({
        RegName:regWd32,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'由英文、数字、"_"组成'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:32,
            lenTip:"您输入的文本长度必须在2~32之间"
        });
    //  系统字段字段名长度
    $("#name,#u_name").tdValidator({
        RegName:regWd50AddField,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误，请检查字符格式和长度，2~50之间',
        onFoucs:'由英文、数字、"_"组成，长度2~50个字符'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:50,
            lenTip:"您输入的字段长度必须在2~50之间"
        }); 
        // event_id事件标识
    $("input#addPartnerCode,#addConfValue,#editValue,#addName,#appAdminUsername,#event_id,#oneKeyAddPartnerCode,.addName").tdValidator({
    	RegName:regWc,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您输入的格式错误',
        onFoucs:'由英文、数字、"_"组成'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:16,
            lenTip:"您输入的文本长度必须在2~16之间"
        });
    $("#role").tdValidator({
    	RegName:regWd,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'由英文、数字、中文、"_"组成'}).inputValidator({
            hasSpace:false,
            minLen: 2, 
            maxLen:16,
            lenTip:"您输入的文本长度必须在2~16之间"
        });
    $("#addValue,#addValueSA").tdValidator({
    	RegName:regWf,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'由英文、数字、中文、"_"、"."组成'}).splitValidator({
        	minLen: 1,        // 最小值为0的时候表示无限制
			maxLen: 32,       // 最大值为0的时候表示无限制
			splitRegx:",",    // 默认逗号分隔
			lenTip:"您输入的每个名单长度不正确"
        });
    
    //字段长度，仅数字
    $("#maxLength,#u_maxLength").compareNum({
    	RegName:regFullNumber,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'请填写正确的数值类型数值，正整数(1-200)',
        onFoucs:'请填写数字，正整数(1-200) ',
        minNum:1,
        maxNum:200
        }).inputValidator({
            hasSpace:false,
            minLen: 1, 
            maxLen:3,
            lenTip:"请填写正确的数值类型数值，正整数(1-200)"
        });;
        
    $(".purchaseCount,#purchaseCount,#resetCount,#warningNum,#warningDay").tdValidator({
    	RegName:regPositiveInteger,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'请输入正整数'
    });
    $("#warningPercent").tdValidator({
        RegName:regPositiveIntegerUnder,
        blurEmpty:'',
        blurFit:'输入正确',
        blurUnfit:'您的输入错误',
        onFoucs:'请输入100内正整数'
    });
    window.validations = {
        regPositiveIntegerConfig:{
            RegName:regPositiveInteger,
            blurEmpty:'',
            blurFit:'输入正确',
            blurUnfit:'您的输入错误',
            onFoucs:'请输入正整数'

        }
    };
});







