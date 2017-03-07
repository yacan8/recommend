/*
 * 策略管理权重滑块函数
 * Copyright @2016 杭州同盾科技有限公司 版权所有
 * Date: 2016-12-27
 * Author：yc
 */
// function resetSlider(selBeginThreshold,selEndThreshold,selDenyThreshold,selReviewThreshold){
//     var min1,max1,deny,review;
//     if(selEndThreshold<selBeginThreshold){
//         var mid1 = (selEndThreshold+selBeginThreshold)/2;
//         deny = 2*mid1 - selDenyThreshold;
//         review = 2*mid1 - selReviewThreshold;
//         max1 = selBeginThreshold;min1 = selEndThreshold;
//     }else{
//         deny = selDenyThreshold; review = selReviewThreshold;
//         min1 = selBeginThreshold;max1 = selEndThreshold;
//     }
//
//     $("#sliderRange").slider({
//         range: true,
//         min: min1,
//         max: max1,
//         values: [review, deny],
//         // values: slider_values,
//         slide: function (event, ui) {
//             if(selEndThreshold<selBeginThreshold){
//                 var mid = (selEndThreshold+selBeginThreshold)/2;
//                 var end = ui.values[1];
//                 var start = ui.values[0];
//                 $("#maxCount").text(2*mid-end);
//                 $("#minCount").text(2*mid-start);
//             }else{
//                 $("#minCount").text(ui.values[0]);
//                 $("#maxCount").text(ui.values[1]);
//             }
//         }
//     });
//     // 动态添加节点并赋值
//     if($('#minCount').length==0){
//         $("#sliderRange a:eq(0)").append("<i id='minCount'></i>");
//         $("#sliderRange a:eq(1)").append("<i id='maxCount'></i>");
//     }
//     if(selEndThreshold<selBeginThreshold){
//         var mid = (selEndThreshold+selBeginThreshold)/2;
//         var end = $("#sliderRange").slider("values", 1);
//         var start = $("#sliderRange").slider("values", 0);
//         $("#maxCount").text(2*mid-end);
//         $("#minCount").text(2*mid-start);
//     }else{
//         $("#minCount").text($("#sliderRange").slider("values", 0));
//         $("#maxCount").text($("#sliderRange").slider("values", 1));
//     }
// }
$(function(){
    $('#sel_beginThreshold_val,#sel_endThreshold_val,#minCount,#maxCount').keyup(function(event) {
        var val = $(this).val();
        var reg = /^[-]?\d+$/;
        if(reg.test(val)||val==''){
            if(val.length==1&&val=='-'){
                return;
            }
            if(val!='0') {
                var s = val[0] == '-' ? '-' : '';
                var value = val == '' ? 0 : parseInt(val);
                if (value > -100000 && value < 100000) {
                    var _val = val[0] == '-' ? val.substr(1, val.length - 1) : val;
                    if (_val[0] == '0') {
                        while (1) {
                            if (_val[0] == '0') {
                                _val = _val.substr(1, _val.length - 1);
                            } else {
                                break;
                            }
                        }
                        $(this).val(s + _val);
                    }

                } else {
                    if(val!='')
                        $(this).val('0');
                    $.message.pop('权重分数必须在-100000和100000', 'warning');
                    return false;
                }
            }
        }else{
            if(val.length==1&&val=='-'){
                return ;
            }else {
                var str = '';
                var result = '';
                var j = 0;
                if (val[0] == '-') {
                    str += val[0];
                    j = 1;
                }
                for (var i = j; i < val.length; i++) {
                    var char = val[i];
                    if (/^\d+$/.test(char)) {
                        result += char;
                    }
                }

                $(this).val(str + result);
                $.message.pop('请输入正确的数字格式', 'warning');
                return false;
            }
        }
    });
})



var checkWeight = function(start,min,max,end){
    var test = /^[-]?\d+$/;
    if(!test.test(start)){
        return '请输入正确的上限数字。';
    }
    if(!test.test(end)){
        return '请输入正确的下限数字。';
    }
    if(!test.test(min)){
        return '请输入正确的整数格式。';
    }
    if(!test.test(max)){
        return '请输入正确的整数格式。';
    }
    var result = '权重配置必须从左到右递增或者递减';
    start = parseInt(start);
    min = parseInt(min);
    max = parseInt(max);
    end = parseInt(end);
    if(start>end){
        if(start>=min&&min>=max&&max>=end){
            return true;
        }else{
            return result;
        }
    }else{
        if(start<=min&&min<=max&&max<=end){
            return true;
        }else{
            return result;
        }
    }

}