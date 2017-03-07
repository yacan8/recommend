;$(function(){
    LoadingFun.commentManageLoading(comment_manage_loading_url,order,page);
    //评论删除点击事件
    $(document).on('click','.comment-delete',function(){
        var _self = $(this);
        $.confirm({
            theme:'material',
            title: '提示',
            content: '确认删除吗？',
            buttons: {
                confirm: {
                    text: '确认',
                    btnClass: 'btn-danger',
                    action: function(){
                        $.confirm({
                            content: function(){
                                var self = this;
                                return $.ajax({
                                    url: comment_delete_url,
                                    data:{comment_id:_self.attr('data-id')},
                                    dataType: 'json',
                                    method: 'post'
                                }).done(function (result) {
                                    if(result.success){
                                        self.setContentAppend(result.message);
                                        _self.parents('.user-item').remove();
                                    }else{
                                        self.setContentAppend(result.message);
                                    }
                                }).fail(function(){
                                    self.setContentAppend('请求失败');
                                });
                            },
                            title:'消息提示',
                            buttons:{
                                somethingElse:{
                                    text:'OK',
                                    btnClass:'btn-default'
                                }
                            }
                        });
                    }
                },
                cancel: {
                    text: '取消',
                    btnClass: 'btn-default'
                }

            }
        });
    });
});