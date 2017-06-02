$(function(){
	
	//平均UV和PV
	$("#average_interval").find('li a').click(function(event) {
		var interval = $(this).attr("data-interval");
		$("#average_show").html($(this).html());
		average_pv_uv(interval,'average-flot-line-chart',average_url);
	});
	$("#average_interval").find('li a').first().click();
	function average_pv_uv(interval,obj,url){
		$("#"+obj).html('玩命加载中...');
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'html',
			data: {interval: interval},
			success:function(data){
				$("#"+obj).html('');
				var dataObj = $.parseJSON(data);
				console.log(dataObj);
				Morris.Bar({
			        element: obj,
			        data: dataObj,
			        xkey: '时间',
			        ykeys: ['uv','pv'],
			        labels: ['uv','pv'],
			        hideHover: 'auto',
			        resize: true
			    });
			},
			error:function(){
				alert('请求失败');
			}
		});	
	}


	//活跃用户事件绑定
	$("#active_mouth").find('li a').click(function(event) {
		var mouth = $(this).attr("data-mouth");
		$("#active_mouth_show").html(mouth);
		active_uv(mouth,'active-flot-line-chart',active_uv_url);
	});
	//活跃用户设置
	function active_uv(mouth,obj,url){
		$("#"+obj).html('玩命加载中...');
		$.ajax({
			url: url,
			type: 'post',
			dataType: 'html',
			data: {mouth: mouth},
			success:function(data){
				$("#"+obj).html('');
				var dataObj = $.parseJSON(data);
				console.log(dataObj);
				Morris.Bar({
			        element: obj,
			        data: dataObj,
			        xkey: '日期',
			        ykeys: ['活跃用户数量'],
			        labels: ['活跃用户数量'],
			        hideHover: 'auto',
			        resize: true
			    });
			},
			error:function(){
				alert('请求失败');
			}
		});	
	}
	active_uv(now_mouth,'active-flot-line-chart',active_uv_url);
	change_pv_post(now_year,now_mouth,id,browse_url);
	change_reg_statistics(now_mouth,reg_statistics_url);
	//修改月份点击事件
	$("#mouth").find('li a').click(function(event) {
		var mouth_value = $(this).attr('data-mouth');
		change_pv('mouth','year',mouth_value,id,browse_url);
	});

	//修改年份点击事件
	$("#year").find('li a').click(function(event) {
		var year_value = $(this).attr('data-year');
		change_pv('year','mouth',year_value,id,browse_url);
	});

	function change_pv(obj1,obj2,value,id,url){
		var obj1_o = $("#"+obj1);
		var obj2_o = $("#"+obj2);
		var obj2_value = obj2_o.attr('data-value');
		obj1_o.attr('data-value',value);
		$('#'+obj1+'_show').html(value);
		if(obj1=='mouth')
			change_pv_post(obj2_value,value,id,url);
		else
			change_pv_post(value,obj2_value,id,url);
	}
	//重置浏览PV视图
  	function change_pv_post(year,mouth,id,url){
  		$("#flot-line-chart").html("玩命加载中...");
  		$.ajax({
  			url:  url,
  			type: 'post',
  			dataType: 'html',
  			data: {year:year,mouth:mouth,id:id},
  			beforeSend:function(){
  				$("#flot-line-chart").html('玩命加载中...');
  				// $(".wait-mask").show();
  			},
  			success:function(data){
  				$("#flot-line-chart").html("");
  				// $(".wait-mask").fadeOut();
				var result = $.parseJSON(data);
					Morris.Line({
					  element: 'flot-line-chart',
					  data: result,
					  xkey: '日期',
					  ykeys: ['浏览数','访客'],
					  labels: ['浏览数','访客']
					});
				},
  			})
  	}


  	$("#reg_mouth").find('li a').click(function(event) {
		var _self = $(this);
		var mouth = _self.attr("data-mouth");
		var text = _self.html();
		$("#mouth_show").html(text);
		change_reg_statistics(mouth,reg_statistics_url);
	});
	
  	function change_reg_statistics(mouth,url){
  		$("#reg-flot-line-chart").html("");
  		$.ajax({
  			url:  url,
  			type: 'post',
  			dataType: 'html',
  			data: {mouth:mouth},
  			beforeSend:function(){
  				$("#reg-flot-line-chart").html('玩命加载中...');
  			},
  			success:function(data){
  				$("#reg-flot-line-chart").html("");
				var result = $.parseJSON(data);
					Morris.Line({
					  element: 'reg-flot-line-chart',
					  data: result,
					  xkey: '日期',
					  ykeys: ['注册数'],
					  labels: ['注册数']
					});
				},
  			})
  	}
})

//Flot Pie Chart
$(function() {
		
		
		$("#area_uv_pre").find('li a').click(function(event) {
			var _self = $(this);
			var pre = _self.attr("data-value");
			var text = _self.html();
			$("#area_uv_pre_show").html(text);
			area_browse(pre,area_uv_url,"uv-flot-pie-chart",id);
		});
		//地区点击事件
		$("#area_pre").find('li a').click(function(event) {
			var _self = $(this);
			var pre = _self.attr("data-value");
			var text = _self.html();
			$("#area_pre_show").html(text);
			area_browse(pre,area_browse_url,"flot-pie-chart",id);
		});

		$("#type_pre").find('li a').click(function(event) {
			var _self = $(this);
			var pre = _self.attr("data-value");
			var text = _self.html();
			$("#type_pre_show").html(text);
			area_browse(pre,type_browse_url,"type-flot-pie-chart",id);
		});

		//地区浏览比例第一个按钮被点击，初始化视图
		$("#area_pre").find('li a').first().click();
		$("#type_pre").find('li a').first().click();
		$("#area_uv_pre").find('li a').first().click();

		/**
		 * [area_browse 根据参数重新加载地区视图]
		 * @param  {[Integer]} pre [前时间段]
		 * @param  {[string]} url [请求URL]
		 * @param  {[object]} obj [加载容器的ID]
		 */
		function area_browse(pre,url,obj,id){
			$("#"+obj).html("玩命加载中...");
			$.post(url,{pre:pre,id:id} , function(data) {
				if(data=='[]' ||data == 'null'){
					$("#"+obj).html("该时间段无数据");
				}else{
					$("#"+obj).html("");
					var result = $.parseJSON(data);
					var plotData = [];
					var other = {label:'其他内容',data:0};
					var sum = 0 ;
					result.forEach(function(item){ item.data = Number(item.data); sum += item.data;});
					result.forEach(function(item){
						var percent = item.data/sum;
						if (percent<0.01){
							other.data+=item.data;
						} else {
							plotData.push(item);
						}
					});
					if(other.data != 0)
						plotData.push(other);
					$.plot($("#"+obj), plotData,{
				        series: {pie: {show: true}},
				        grid: {hoverable: true},
				        tooltip: true,
				        tooltipOpts: {
				        	content: "%p.0%, %s",
				        	shifts: {x: 20,y: 10},
				            defaultTheme: false
				        }
				    });
				}	
			});
		}
});