//author diqye
(function($){
		//获取光标位置
		$.fn.getpos = function(){
			var thisDom = $(this).get(0);
			var index = 0; 
			if (document.selection) {// IE Support 
				thisDom.focus(); 
				var Sel = document.selection.createRange(); 
				if (thisDom.nodeName === 'TEXTAREA') {//textarea 
					var Sel2 = Sel.duplicate(); 
					Sel2.moveToElementText(thisDom); 
					var index = -1; 
					while (Sel2.inRange(Sel)) { 
						Sel2.moveStart('character'); 
						index++; 
					}; 
				} else if (thisDom.nodeName === 'INPUT') {// input 
					Sel.moveStart('character', -thisDom.value.length); 
					index = Sel.text.length; 
				} 
			} else if (thisDom.selectionStart || thisDom.selectionStart == '0') { // Firefox support 
				index = thisDom.selectionStart; 
			} 
			return (index); 
		}
		//设置光标位置
		$.fn.setpos = function(posindex){
			var thisDom = $(this).get(0);
			if (document.selection) {// IE Support
				var r = thisDom.createTextRange(); 
				r.collapse(true); 
				r.moveStart('character',posindex); 
				r.select(); 
			}else{
				thisDom.selectionStart = posindex;
				thisDom.selectionEnd = posindex;
			}
	   }
		//选择文本
	   $.fn.selecttext = function(start,len){
			var thisDom = $(this).get(0);
			if (document.selection) {// IE Support
				var r = thisDom.createTextRange(); 
				r.collapse(true); 
				r.moveStart('character',start); 
				r.moveEnd('character',len); 
				r.select(); 
			}else{
				thisDom.selectionStart = start;
				thisDom.selectionEnd = start+len;
			}
	   }
		//在光标位置插入一段文本
	   $.fn.insertforpos = function(text){
			var valstr = $(this).val();
			var pos = $(this).getpos();
			if(valstr.length >= pos){
				var prestr = valstr.substring(0,pos);
				var sufstr = valstr.substring(pos);
				$(this).val(prestr+text+sufstr);
			}
	   }
	   
})($);