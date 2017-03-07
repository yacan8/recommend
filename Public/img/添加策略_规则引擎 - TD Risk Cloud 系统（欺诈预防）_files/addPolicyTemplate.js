/**
 * Copyright @2014 杭州同盾科技有限公司 版权所有
 * Author : 李建
 * For：策略中心--超级管理员(sa)添加策略模板
 * Date : 14-4-19
 */
$(function () {
    // 权重匹配下的风险阈值
    // $("#sliderRange").slider({
    //     range: true,
    //     min: 0,
    //     max: 100,
    //     values: [20, 80],
    //     slide: function (event, ui) {
    //         $("#minCount").text(ui.values[0]);
    //         $("#maxCount").text(ui.values[1]);
    //     }
    // });
    // 动态添加节点并赋值
    $("#sliderRange a:eq(0)").append("<i id='minCount'></i>");
    $("#sliderRange a:eq(1)").append("<i id='maxCount'></i>");
    $("#minCount").text($("#sliderRange").slider("values", 0));
    $("#maxCount").text($("#sliderRange").slider("values", 1));
    //策略模式改变事件
    $("#policy_mode").selectInit({
        callback: policyMode_change,
        selectIndex: 0

    });
    function policyMode_change() {
        var policyModeVal = $("#policy_mode").selectValue();
        if (policyModeVal == 'Weighted') {
            $(".pure-control-group.mt20.SR").show();
        } else {
            $(".pure-control-group.mt20.SR").hide();
        }
    }

    $("#event_type").selectInit({
        callback: function (index, sender) {
            $.post("/ruleengine/policy/policytemplate/addPolicyTemplate.json", {
                operationType: 'getRiskType',
                eventType: sender.value
            }, function (resp) {
                if (resp.code != 0) $("#risk_type").selectInit({dataList: resp.attr});
            }, "json");
        }
    });
});

function addPolicyTemplate() {
    var policyName = $("#policy_name").val();
    var eventType = $("#event_type").selectValue();
    var riskType = $("#risk_type").selectValue();
    var appType = $("#app_type").selectValue();
    var partnerType = $("#partner_type").selectValue();
    var industryType = $("#industry_type").selectValue();

    var policyMode = $("#policy_mode").selectValue();
    var reviewThreshold = $("#minCount").val();
    var denyThreshold = $("#maxCount").val();
    var begin_threshold = $("#sel_beginThreshold_val").val();
    var end_threshold = $("#sel_endThreshold_val").val();

    var description = $("#policy_description").val();
    if ($.trim(policyName).length < 2 || $.trim(policyName).length > 20) {
        $.message.pop('策略名称：长度不符合要求<br/>建议输入 长度2-20', 'warning', undefined, 2000);
        return;
    }
    if (!/^[A-Za-z0-9\u4E00-\u9FA5\_]{2,20}$/.test($.trim(policyName))) {
        $.message.pop('策略名称：请不要输入全角字符、中划线等非法字符<br/>建议输入 中文、英文、数字和下划线的组合', 'warning', undefined, 2000);
        return;
    }
    if(begin_threshold==''||end_threshold==''||review_threshold==''||deny_threshold==''){
        $.message.pop('权重配置不能为空', 'warning');
        return 'error';
    }


    var check_threshold = function(val){
        if(/^[-]?\d+$/.test(val)){
            var value = parseInt(val);
            if(value<-100000&&value>100000) {
                $.message.pop('上限下限必须在-100000和100000', 'warning');
                return false;
            }
        }else{
            $.message.pop('请输入正确的数字格式','warning');
            return false;
        }
        return true;
    }
    if(!check_threshold(begin_threshold)||!check_threshold(end_threshold)){
        return 'error';
    }
    var _mess = checkWeight(begin_threshold,review_threshold,deny_threshold,end_threshold);
    if(_mess!==true){
        $.message.pop(_mess, 'warning');
        return 'error';
    }


    $.post(baseUrl + "/ruleengine/policy/policytemplate/addPolicyTemplate.json",
    {
        "operationType": "addPolicytemplate",
        "policyName": policyName,
        "eventType": eventType,
        "riskType": riskType,
        "appType": appType,
        "partnerType": partnerType,
        "industryType": industryType,
        "policyMode": policyMode,
        "reviewThreshold": reviewThreshold,
        "denyThreshold": denyThreshold,
        "description": description,
        "beginThreshold": begin_threshold,
        "endThreshold": end_threshold
    },
    function (data) {
        if (data.success) {
            $.message.pop(data.msg);
            var policyUuid = data.attr.uuid;
            var eventType = data.attr.riskEventType;
            window.location.href = baseUrl + "/ruleengine/policy/policyconfig/policyView.htm?p=1&uuid="
            + policyUuid + "&eventType=" + eventType;
        } else {
            $.message.pop(data.msg);
        }
    }, "json");
}