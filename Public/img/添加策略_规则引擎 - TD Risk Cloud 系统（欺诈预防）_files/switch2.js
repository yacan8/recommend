/**
 * Created by Administrator on 14-3-3. Copyright 2014, 凡赛提Forseti，同盾反欺诈云服务系统
 * Copyright
 *
 * @2014 杭州同盾科技有限公司 版权所有 Date: 2014-02-14 Author: skating
 *
 * 插件试用说明： 你可以对switch-plugin做如下操作 .switchValue() //获取当前值 .switchOn(val) //切换到 on
 * 状态，没有与服务器交互 .switchOff(val) //切换到 off 状态，没有与服务器交互 .switchAction(options)： //
 * 根据options的配置向服务器请求，根据响应切换到不同的状态
 * options 参数格式
 * { url: 'xxxxx',
 * method : 'get/post ', 
 * data : {} , //需要发送到服务器的数据 
 * dataType : "json" // 返回格式，默认为text 
 * success : function(data){ //对data处理 或者其他的额外的处理 
 * return {action: "on/off",value:xxxx} // 必须返回该对象，必须有action、value属性 该属性为switch将要切换到的状态 
 *  }, //
 * error:function(xhr,textStatus,errThrown){ // // 对服务器错误或请求中发生的错误进行处理 // } // }
 */
jQuery(function () {
    var $ = jQuery;
    var _$switch_plugin = jQuery(".switch-plugin");
    if (_$switch_plugin.length) {
        _$switch_plugin.each(function (index, domEle) {
            initSwitch(domEle);
        });
    }

    function initSwitch(domEle) {
        var ckbox = $(domEle).find(":checkbox");
        if (ckbox.is(":checked")) {
            $(domEle).find(".switch-off").hide();
        }
    }

    var defaultOptions = {
        url: undefined,
        data: undefined,
        method: "get",
        success: undefined,// 必须返回{action:on/off,value:xxx}
        error: undefined,
        dataType: "text"
    };

    $.fn.extend({
        switchInit: function () {
            return this.each(function (i, ele) {
                initSwitch(ele);
            });
        },
        switchValue: function () {
            return $(this).find(":checkbox").val();
        },
        switchIsON: function () {
            return $(this).find(":checkbox").is(":checked");
        },
        switchOn: function (value) {
            return this.each(function () {
                var $this = $(this);
                if (!$this.hasClass("switch-plugin")) {
                    return;
                }
                $this.find(".switch-off").hide();
                $this.find(":checkbox").attr("checked", "checked");
                if (value || typeof value == 'number') {
                    $this.find(":checkbox").val(value);
                }
            });
        },
        switchOff: function (value) {
            return this.each(function () {
                var $this = $(this);
                if (!$this.hasClass("switch-plugin")) {
                    return;
                }
                $this.find(".switch-off").show();
                $this.find(":checkbox").removeAttr("checked");
                if (value || typeof value == 'number') {
                    $this.find(":checkbox").val(value);
                }
            });
        },
        switchAction: function (options) {
            return this.each(function () {
                var $this = $(this);
                var op = $.extend(true, {}, defaultOptions, options);
                var isOn = $this.find(":checkbox").is(":checked");
                var on_off = {};
                if (typeof op.url === "string") {
                    $.ajax({
                        url: op.url,
                        type: op.method,
                        dataType: op.dataType,
                        data: op.data,
                        success: function (data, textStatus, jqXHR) {
                            if (typeof op.success === "function") {
                                on_off = op.success(data);
                                if (on_off) {
                                    var newVal = typeof on_off.value !== 'undefined' ? $this.find(":checkbox").val(on_off.value) : undefined;
                                    if (on_off.action === "on") {
                                        $this.switchOn();
                                    } else if (on_off.action === "off") {
                                        $this.switchOff();
                                        if (on_off.isIf == true) {
                                            var childSwitch = $this.parents('.p-rc-spr').siblings('.p-rc-if').find('.switch-plugin');
                                            $.each(childSwitch, function (index, ele) {
                                                $(ele).switchIsON() ? $(ele).switchOff("0") : null;
                                            });
                                        }
                                    }
                                }
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            if (typeof op.error === "function") {
                                op.error(XMLHttpRequest, textStatus,
                                    errorThrown);
                            }
                        }
                    });
                } else {
                    isOn ? $this.switchOff() : $this.switchOn();
                }
            });
        }
    });
});