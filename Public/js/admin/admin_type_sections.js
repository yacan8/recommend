$(function(){
        //一级分类改变二级联动
        // $(document).on("change","#type",function(event) {
        //     var type_id = $(this).val();
        //     changeType(type_id);
        // });


        //二级分类添加判读
        $(document).on("change","#sections",function(event) {
            var _self = $(this);
            var _value = _self.val();
            if(_value=='add'){
                $('#AddSections').modal('show');
                _self.find("option[selected]").attr("selected",true);
            }
        });



        //二级分类AJAX添加按钮
        $("#add_sections").click(function(event) {
            var _type = $("#type").val();
            var sections_input =$("input[name='sections']");
            var sections = sections_input.val();
            AjaxAddSections(_type,sections,$("#sections"));
            $('#AddSections').modal('hide');
            sections_input.val('');
        });



})
        /**
         * [AjaxAddSections description]
         * @param {[Integer]} type_id  [归属的分类ID]
         * @param {[string]} sections [二级分类]
         * @param {obj} obj      [操作对象]
         */
        function AjaxAddSections(type_id,sections,obj){
            if(sections==''){
                $.toaster({ priority : 'danger', title : '通知', message : '请输入分类名' });
                obj.find('option').first().attr("selected",true);
            }else{
                $.ajax({
                    url: AjaxAddSections_url,
                    type: 'get',
                    dataType: 'text',
                    data: {type_id:type_id,sections:sections},
                    success:function(data){
                        var result = $.parseJSON(data);
                        if(result.Code = '1'){
                            obj.prepend("<option value='"+result.id+"'>"+result.sections+"</option>");
                            obj.find('option').first().attr("selected",true);
                            $.toaster({ priority : 'success', title : '通知', message : result.Message});
                        }else
                            $.toaster({ priority : 'danger', title : '通知', message : result.Message });
                    },
                    error:function(data){
                        alert("添加失败");
                    }
                })
            }
        }

        // /**
        //  * [changeType 修改一级分类二级联动]
        //  * @param  {[Integer]} type_id [分类ID]
        //  */
        // function changeType(type_id){
        //     $.ajax({
        //         url: changeType_url,
        //         type: 'get',
        //         dataType: 'text',
        //         data: {type_id:type_id},
        //         success:function(data){
        //             var dataObj = $.parseJSON(data);
        //             var option_str = g_sections(dataObj);
        //             $("#sections").html(option_str);
        //             if(data=='[]'){
        //                 $('#AddSections').modal('show');
        //             }
        //
        //         },
        //         error:function(data){
        //             alert("二级分类加载失败");
        //         }
        //     })
        //
        //  }
        //
        //  /**
        //   * [g_sections 生成二级分类字符串]
        //   * @param  {object} data [传入的列表]
        //   * @return {[string]}      [生成的字符串]
        //   */
        //  function g_sections(data){
        //     var str = '';
        //     for(var i = 0 ; i<data.length;i++){
        //         str += "<option value='"+data[i].id+"'>"+data[i].sections+"</option>";
        //     }
        //     str += "<option value='add'>添加+</option>";
        //     return str;
        //  }
