$(function(){
	$("#submit").click(function(){
		var areas = $("select[name='areas']").val();
		var school = $("select[name='school']").val();
		var sex = $('input[name="sex"]').val();
		console.log(areas);
		console.log(sex);
		console.log(school);

	});
});

	// loading_toggle();
$(document).on('change',"select[name='province']",function(event) {
	var word = $(this).val();
	var _city_select = $("select[name='city']");
	var _area_select = $("select[name='areas']");
	select_change(word,province_change_url,change_callback,_city_select,_area_select);
});


$(document).on('change',"select[name='city']",function(event) {
	var word = $(this).val();
	var _area_select = $("select[name='areas']");
	select_change(word,city_change_url,change_callback,_area_select,_area_select);
});

$(document).on('change',"select[name='schoolP']",function(event) {
	var word = $(this).val();
	var _city_select = $("select[name='schoolC']");
	var _school_select = $("select[name='school']");
	select_change(word,s_p_c_url,change_callback,_city_select,_school_select);
});

$(document).on('change',"select[name='schoolC']",function(event) {
	var word = $(this).val();
	var _school_select = $("select[name='school']");
	select_change(word,s_c_c_url,change_callback,_school_select,_school_select);
});



function select_change(word,url,callback,obj,resetObj){
	loading_toggle();
	$.ajax({
		url: url,type: 'post',dataType: 'html',data: {word: word},
		success:function(data){
			var result = $.parseJSON(data);
			loading_toggle();
			callback(result,obj,resetObj);
		},
		error:function(xhr){
			loading_toggle();
			alert('请求失败');
		}
	});
}

function change_callback(result,obj,resetObj){
	var str = '<option value="0">--请选择--</option>';
	for(var i=0;i<result.length;i++){
		str = str + '<option>'+result[i].item+'</option>';
	}
	obj.html(str);
	if(obj!=resetObj)
		resetObj.html('<option value="0">--请选择--</option>');
}
