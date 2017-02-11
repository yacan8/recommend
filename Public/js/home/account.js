// 账户也JS
;$(function(){
	$(".icon_btn").click(function(){
		$("#file").click();
	})
	// 图片预览
    $('#file').change(function(){
        var objUrl = getObjectURL($(this).get(0).files[0]) ;
        if (objUrl) {
            $(".icon").attr("src", objUrl).parent("a").attr("href",objUrl) ;//设置灯箱效果的值
        }
    });

    //建立一個可存取到該file的url
    function getObjectURL(file) {
        var url = null ;
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    }
})