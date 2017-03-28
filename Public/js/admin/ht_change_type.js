$(function() {
			     //颜色选择
          $('.demo').each( function() {
              var _self = $(this);
    				$(this).minicolors({
    					control: $(this).attr('data-control') || 'hue',
    					defaultValue: $(this).attr('data-defaultValue') || '',
    					inline: $(this).attr('data-inline') === 'true',
    					letterCase: $(this).attr('data-letterCase') || 'lowercase',
    					opacity: $(this).attr('data-opacity'),
    					position: $(this).attr('data-position') || 'bottom left',
    					change: function(hex, opacity) {
    						if( !hex ) return;
    						if( opacity ) hex += ', ' + opacity;
    						try {

    							console.log(hex);
    						} catch(e) {}
    					},
    					theme: 'bootstrap'
  				});
            });
            $(document).on('click','.changecolor',function(){
            	var _self = $(this);
            	var color_value = _self.parents("td").siblings('.select-color').find('input').val();
              var type = _self.parents("td").siblings('.type').find('input').val();
            	var id = _self.attr('data-type');
            	changecolor(id,type,color_value,changetype_url);
            })
            function changecolor(id,type,color,url){
            	$.ajax({
                url: url,
                data:{id:id,type:type,color:color},
                type: 'get',
                dataType: 'text',
                success:function(data,textStatus){
                  if(data=='1')
                  	$.toaster({ priority : 'success', title : '通知', message : '修改成功'});
                  else
                  	$.toaster({ priority : 'danger', title : '通知', message : '修改失败'});
                },
                error:function() {
                  $.toaster({ priority : 'danger', title : '通知', message : '请求失败'});
                }
            })
            }
		});