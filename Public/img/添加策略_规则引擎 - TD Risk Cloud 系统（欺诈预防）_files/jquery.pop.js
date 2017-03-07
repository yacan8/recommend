//--------Edit by Rookie
//--------2012.10.15

(function($) {	
	jQuery.fn.extend({
		
		popOn: function(popY,popNoFlow,fadeTime,hideBox) {	
			var _this=$(this);
			var isIE6,popBox,popClose,popMask;
			resetThis();
			
			function resetThis(){
				
				popY=popY || $(window).height()*0.1; //默认赋值
				popNoFlow=popNoFlow || false;//默认赋值
				fadeTime=fadeTime||250;
				hideBox=hideBox||"";
				isIE6=$.browser && $.browser.msie && $.browser.version=="6.0";
				popClose=_this.children(".close");
				if(!_this.parent().is(".popBox")){
					_this.wrap("<div class='popBox'></div>");
					_this.before("<div class='popMask'></div>");
				}//end if
				popBox=_this.parent();
				popMask=_this.siblings();	
				if(!popNoFlow && !isIE6){popBox.css("position","fixed");}
				popMask.show();	
				if(fadeTime<=1){_this.show();}else{_this.fadeIn(fadeTime);}
				if(hideBox!=""){$(hideBox).hide();}
				maskReset();
				
				
				_this.bind('resize',popReset);//end bind
				_this.one('close',popHide);//end bind
				//popMask.one('click',popHide);
				popClose.one('click',popHide);//end bind
				$(window).bind('resize',maskReset);//end bind
				$(window).bind('scroll',popReset);//end bind
			}//end func	
			
			function popHide(event){
				$(window).unbind('resize',maskReset);
				$(window).unbind('scroll',popReset);
				_this.unbind('resize',popReset);
				//popMask.remove();
				//_this.unwrap();
				
				popMask.fadeOut(290);
                _this.fadeOut(290);
				_this.hide();
				if(hideBox!=""){$(hideBox).show();}
			}//end func
			
			function maskReset(event){
				//alert("window resize");//测试bind用
				popMask.width($(window).width());
				if(popNoFlow){popMask.height($(document).height()>$(window).height()?$(document).height():$(window).height());}
				else if(!isIE6){popMask.height($(window).height());}
				else{popMask.height($(document).height()>$(window).height()?$(document).height():$(window).height());}
				popReset();
			}//end func
			function popReset(event){
				_this.css("left",Math.floor($(window).width()/2-_this.outerWidth()/2));
				if(popNoFlow){_this.css("top",popY);}
				else if(!isIE6){_this.css("top",popY);}
				else{_this.css("top",$(document).scrollTop()+popY);}				
			}//end func
		},//end fn
		
		popOff: function() {
			$(this).trigger('close');
		}//end fn		
			
	});//end extend	
})(jQuery);//闭包