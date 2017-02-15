    $(function(){

        //表单提交
        $("#new-form").submit(function(event) {
            var keyword_list = $("#dd-container").find(".dd-item");
            var keyword = [];
            if(keyword_list.length==0){
                $.toaster({ priority : 'danger', title : 'Notice', message : '关键字个数最少为1，请添加关键字'});
                return false;
            }else{
                keyword_list.each(function(index, el) {
                    keyword.push({id:keyword_list.eq(index).attr('data-id'),keyword:keyword_list.eq(index).get(0).childNodes[0].textContent})
                });
                $('#keyword').val(JSON.stringify(keyword));
                return true;
            }
        });
        //图片浏览
        $('.btn-file').click(function(event) {
            $('#file').click();
        });
        $('#file').change(function(){
            var objUrl = getObjectURL($(this).get(0).files[0]) ;
            if (objUrl) {
                $("#file-img").attr("src", objUrl);//设置灯箱效果的值
            }
        });

        //标签库点击
        // $(document).on('click','.label_ku',function(){
        //     var _self = $(this);
        //     if($("#label_span_container").find(".la-info").length>=4){
        //         $.toaster({ priority : 'danger', title : 'Notice', message : '标签个数范围为1~4'});
        //     }else{
        //         var id = _self.attr('data-label');
        //         var label = _self.html();
        //         var label_str = g_label(id,label);
        //         _self.hide();
        //         $("#label_span_container").append(label_str);
        //     }
        //     $('#myModal').modal('hide');
        // });

        //删除标签
        // $(document).on('click','.fa.fa-times',function(){
        //     var _self = $(this);
        //     var label_id = _self.parents("span.la-info").attr('data-label');
        //     $(".label_ku[data-label="+label_id+"]").show();
        //     _self.parents("span.la-info").remove();
        // });
        //添加标签按钮
        $('.label_ku_add').click(function(event) {
            $('.add-label').focus();
        });


        //ajax远程添加标签
         $('.add-label').bind('keypress',function(event){
            if(event.keyCode == "13"){
                var label = $('.add-label').val();
                $.ajax({
                    url: add_label_url,
                    type: 'get',
                    dataType: 'text',
                    data: {label:label},
                    beforeSend:function(XMLHttpRequest){
                        $(".wait-mask").show();
                    },
                    success:function(data){
                        $(".wait-mask").hide();
                        var result = $.parseJSON(data);
                        if(result.Code == '1'){
                            var str_ku = g_label_ku(result.LabelId,result.Label);
                            var str = g_label(result.LabelId,result.Label);
                            $(".modal-body.flex-row").prepend(str_ku);
                            $(".label_ku[data-label="+result.LabelId+"]").hide();
                            $("#label_span_container").append(str);
                            $('#myModal').modal('hide');
                            $('.add-label').val('');
                        }else if(result.Code == '2'){
                            $.toaster({ priority : 'danger', title : 'Notice', message : result.Message});
                        }else if(result.Code == '0'){
                            $.toaster({ priority : 'danger', title : 'Notice', message : result.Message});
                        }else if(result.Code == '3'){

                            if($("#label_span_container").find('.a-info[data-label="'+result.LabelId+'"]').length!=0){
                                var str = g_label(result.LabelId,result.Label);
                                $(".label_ku[data-label="+result.LabelId+"]").hide();
                                $("#label_span_container").append(str);
                                $('.add-label').val('');
                            }
                           $('#myModal').modal('hide');
                        }
                    },
                    error:function(){
                        alert('请求失败');
                    }
                })
            };
        });
        function g_label(id,label){
            var str = '<span class="la-info" data-label="'+id+'">'+label+' <a class="fa fa-times"></a></span>';
            return str;
        }
        function g_label_ku(id,label){
            var str = '<a class="la-info label_ku" data-label="'+id+'">'+label+'</a>';
            return str;
        }
        //建立一個可存取到該file的url
        function getObjectURL(file) {
            var url = null ;
            if (window.createObjectURL!=undefined) { // basic
                url = window.createObjectURL(file) ;这
            } else if (window.URL!=undefined) { // mozilla(firefox)
                url = window.URL.createObjectURL(file) ;
            } else if (window.webkitURL!=undefined) { // webkit or chrome
                url = window.webkitURL.createObjectURL(file) ;
            }
            return url ;
        }
 })
