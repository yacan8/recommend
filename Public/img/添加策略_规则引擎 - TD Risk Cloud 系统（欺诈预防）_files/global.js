/*!
 * 封装不同浏览器js的差异，提供统一的接口
 *
 * Copyright 2013, 凡赛提Forseti，同盾反欺诈云服务系统
 * Copyright @2013 杭州同盾科技有限公司 版权所有
 * Date: 2014-01-13
 * Author：Rookie
 */
//JSON PolyFill 支持旧版本浏览器
if (!window.JSON) {
    window.JSON = {
        parse: function (sJSON) {
            return eval("(" + sJSON + ")");
        },
        stringify: function (vContent) {
            if (vContent instanceof Object) {
                var sOutput = "";
                if (vContent.constructor === Array) {
                    for (var nId = 0; nId < vContent.length; sOutput += this.stringify(vContent[nId]) + ",", nId++);
                    return "[" + sOutput.substr(0, sOutput.length - 1) + "]";
                }
                if (vContent.toString !== Object.prototype.toString) {
                    return "\"" + vContent.toString().replace(/"/g, "\\$&") + "\"";
                }
                for (var sProp in vContent) {
                    sOutput += "\"" + sProp.replace(/"/g, "\\$&") + "\":" + this.stringify(vContent[sProp]) + ",";
                }
                return "{" + sOutput.substr(0, sOutput.length - 1) + "}";
            }
            return typeof vContent === "string" ? "\"" + vContent.replace(/"/g, "\\$&") + "\"" : String(vContent);
        }
    };
}
//Array PolyFill 支持旧版本浏览器
if (!Array.prototype.filter) {
    Array.prototype.filter = function (fun) {
        "use strict";
        if (this === void 0 || this === null)
            throw new TypeError();
        var t = Object(this);
        var len = t.length >>> 0;
        if (typeof fun !== "function")
            throw new TypeError();
        var res = [];
        var thisArg = arguments.length >= 2 ? arguments[1] : void 0;
        for (var i = 0; i < len; i++) {
            if (i in t) {
                var val = t[i];
                if (fun.call(thisArg, val, i, t))
                    res.push(val);
            }
        }
        return res;
    };
}

if (!String.prototype.endsWith) {
    String.prototype.endsWith = function (searchString, position) {
        var subjectString = this.toString();
        if (position === undefined || position > subjectString.length) {
            position = subjectString.length;
        }
        position -= searchString.length;
        var lastIndex = subjectString.indexOf(searchString, position);
        return lastIndex !== -1 && lastIndex === position;
    };
}

if (!Array.prototype.forEach) {
    Array.prototype.forEach = function (callback, thisArg) {
        var T, k;
        if (this == null) {
            throw new TypeError(" this is null or not defined");
        }
        var O = Object(this);
        var len = O.length >>> 0;
        if (typeof callback !== "function") {
            throw new TypeError(callback + " is not a function");
        }
        if (arguments.length > 1) {
            T = thisArg;
        }
        k = 0;
        while (k < len) {

            var kValue;
            if (k in O) {
                kValue = O[k];
                callback.call(T, kValue, k, O);
            }
            k++;
        }
    };
}
//日期格式化 扩展
Date.prototype.format = function (format) {
    var o = {
        "M+": this.getMonth() + 1, //month
        "d+": this.getDate(), //day
        "h+": this.getHours(), //hour
        "m+": this.getMinutes(), //minute
        "s+": this.getSeconds(), //second
        "q+": Math.floor((this.getMonth() + 3) / 3), //quarter
        "S": this.getMilliseconds() //millisecond
    };
    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }

    for (var k in o) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return format;
};
function issbccase(source) {
    if (source == "") {
        return true;
    }
    var reg = /^[\w\u4e00-\u9fa5\uf900-\ufa2d]*$/g;
    if (reg.test(source)) {
        return false;
    } else {
        return true;
    }
}
function DecimalFormat(num) {
    var num = num | 0;
    var ab = '';
    if (!num) {
        return 0
    }
    if (num < 0) {
        num = -(num);
        ab = '-';
    }
    num = num.toFixed(0);
    var n1 = num.split('.')[0];
    var dfn = '';
    var ex = 0;
    for (var i = n1.length; i >= 1; i--, ex++) {
        if ((ex % 3 == 0) && (ex > 1)) {
            dfn = ',' + dfn;
        }
        dfn = n1.charAt(i - 1) + dfn;
    }
    dfn = ab + dfn;
    return dfn
}
//获取ajax路径
var domainMap_shine, domainMap_default;
var domainMap = $('#domainMap').val().slice(1,-1).split(',');
for (var i=0;i<domainMap.length;i++){
    domainMap[i].replace(/\s/g, "").indexOf('default')==0 && (domainMap_default = (domainMap[i].split('='))[1]);
    domainMap[i].replace(/\s/g, "").indexOf('shine')==0 && (domainMap_shine = (domainMap[i].split('='))[1]);
};
// console.log(domainMap);
// console.log(domainMap_default);
// console.log(domainMap_shine);
//获取url中的参数
var getUrlParams = function (key) {
    var reg = new RegExp("(^|&)" + key + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]);
    return null; //返回参数值
}
var escapeHtml = function (obj) {
    //如果对象是数组
    if ($.isNumeric(obj) || $.isFunction(obj) || $.isEmptyObject(obj)) {
        return obj;
    } else if (typeof obj == 'string') {
        return obj.indexOf('<script>') > -1 ? $('<div>').text(obj).html() : obj;
    } else if ($.isArray(obj)) {
        for (var i in obj) {
            obj[i] = escapeHtml(obj[i]);
        }
    } else {
        for (var j in obj) {
            if (obj.hasOwnProperty(j)) {
                obj[j] = escapeHtml(obj[j]);
            }
        }
    }
    return obj;
}
$.ajaxSetup({
    dataFilter: function (data, type) {
        var newData = $('<div>').text(data).html();
        try {
            newData = $.parseJSON(data);
            newData = escapeHtml(newData);
            newData = JSON.stringify(newData);
        } catch (e) {
            newData = data;
        }
        return newData;
    }
});
$(function () {
    // =================================== 用户顶部控件
    // 点击logo弹出nav导航
    $('.head-top .logo').on('click', function (e) {
        e = e || window.event;
        e.stopPropagation();
        if ($(".seNav").is(':hidden')) {
            var height = $('#sidebar').height();
            $(".seNav")
            .css({height: height - 1, left: -200})
            .show()
            .animate({
                left: 0
            }, 200)
        } else {
            $(".seNav").hide();
        }
    });
    $(document).on('click', function () {
        if (!$(".seNav").is(':hidden')) {
            $(".seNav").hide();
        }
    })

    // user top 用户顶部控件
    var toggleTimeout = -1;
    $(".dropdown").on("mouseenter", ".dropdown-toggle", function () {
        $(this).next(".dropdown-menu").show();
        $(this).find("b.caret").addClass("toggle");
        clearTimeout(toggleTimeout);
    }).on('click', function () {
        var dropMenu = $(this).find(".dropdown-menu");
        var caret = $(this).find(".dropdown-toggle b.caret");
        if (dropMenu.is(":visible")) {
            dropMenu.hide();
            caret.removeClass("toggle");
        } else {
            dropMenu.show();
            caret.addClass("toggle");
        }
    }).on("click", ".dropdown-menu", function (e) {
        e = e || window.event;
        event.stopPropagation();
    }).mouseleave(function () {
        $(this).find(".dropdown-menu").hide();
    });

    // 点击页面的时候隐藏下拉层菜单，并阻止它本身冒泡
    $(document).click(function () {
        $(".dropdown .dropdown-menu").hide().siblings("a").find("b.caret").removeClass("toggle");
    });
    $(".dropdown .dropdown-toggle,.dropdown .dropdown-menu").click(function (event) {
        event.stopPropagation();
    });
    // 用户鼠标从下拉层移开的时候
    $(".dropdown .dropdown-menu").mouseleave(function (e) {
        e = e || window.event;
        e.stopPropagation();
        var _this = $(this);
        toggleTimeout = setTimeout(function () {
            _this.hide().siblings("a").find("b.caret").removeClass("toggle");
        }, 300);
    });
    // 当用户鼠标移到其他li的时候
    $(".navList>li").mouseenter(function () {
        $(this).siblings().find("ul.dropdown-menu").hide().siblings("a").find("b.caret").removeClass("toggle");
    });
});

$(function () {
    //获取PartnerAppSelect 信息
    var getPartnerAppSelect = function () {
        $.ajax({
            url: '/PartnerAppSelect.json',
            type: 'post',
            data: {
                operationType: 'doSelectMessage'
            },
            dataType: 'json',
            success: function (data, textStatus, jqXhr) {
                if (data.success) {
                    if ($("#appSelector").length) {
                        var lis = '';
                        lis += '<li data-appID="all"><a href="javascript:void(0)">全部应用</a></li>';

                        var selectMap = data.attr.selectMap;
                        for (var i in selectMap) {
                            var partner = i.split(",");
                            var apps = selectMap[i];
                            for (var j = 0; j < apps.length; j++) {
                                lis += '<li data-appID="' + escapeHtml(apps[j].name || '') + '" data-appuuid="' + escapeHtml(apps[j].uuid || '') + '"><a href="javascript:void(0)">' + escapeHtml(apps[j].displayName || '') + '</a></li>';
                            }
                        }
                        $("#appSelector .drop_cont ul").append(lis);
                    } else if ($("#partnerSelector").length) {
                        var paLis = '<li data-partnerid="all"><a href="javascript:void(0)">全部合作方</a></li>';
                        var selectMap = data.attr.selectMap;
                        for (var i in selectMap) {
                            var partner = i.split(",");
                            var apps = selectMap[i];
                            paLis += '<li data-partnerid="' + escapeHtml(partner[0]) + '"><a href="javascript:void(0)">' + escapeHtml(partner[1]) + '</a></li>';
                            var subAppMenu = $('<div id="subAppMenu-' + escapeHtml(partner[0]) + '" class="sub-menu"> <i class="ia"></i><i class="ib"></i><div class="sub_cont"><ul class="specialScrollbar"><li data-appid="all"><a href="javascript:void(0)" title="">全部应用</a></li></ul></div></div>');
                            var appLis = '';
                            for (var j = 0; j < apps.length; j++) {
                                appLis += '<li data-appID="' + escapeHtml(apps[j].name || '') + '" data-appuuid="' + escapeHtml(apps[j].uuid || '') + '"><a href="javascript:void(0)">' + escapeHtml(apps[j].displayName || '') + '</a></li>';
                            }
                            $("ul", subAppMenu).append(appLis);
                            $("#partnerSelector").append(subAppMenu);
                        }
                        $("#partnerSelector .drop_cont ul").append(paLis);
                    }
                    partnerAppSelectInit();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                //console.log(errorThrown);
            }
        });
    };
    getPartnerAppSelect();
    var toggleDrop = function (isShow, filterVal) {
        var dropBox = $(this).find(".drop_wrap");
        if (isShow) {
            if (filterVal) {
                dropBox
                .find('.drop_cont .specialScrollbar li')
                .filter(function () {
                    return $(this).attr('data-partnerid').indexOf(filterVal) > -1 || $(this).text().indexOf(filterVal) > -1;
                })
                .show()
                .end()
                .filter(function () {
                    return $(this).attr('data-partnerid').indexOf(filterVal) === -1 && $(this).text().indexOf(filterVal) === -1;
                })
                .hide();
            }
            else {
                dropBox.find('.drop_cont .specialScrollbar li').show();
            }
            dropBox.show();
            $(this).addClass("onit dropdown");
        }
        else if (dropBox.is(":visible")) {
            dropBox.hide();
            $(this).removeClass("onit dropdown");
        }
        else {
            dropBox.show().find('.drop_cont .specialScrollbar li').show();
            $(this).addClass("onit dropdown");
        }
    }
    $(".selector").click(function (event) {
        // 阻止冒泡
        event.stopPropagation();
        $(this).find('.drop_cont').css('min-height', 'initial');
        toggleDrop.call(this);
    }).mouseenter(function () {
        $(this).addClass("onit ");
    }).mouseleave(function () {
        if (!$(this).find(".drop_wrap").is(":visible")) {
            $(this).removeClass("onit ");
        }
        if ($("#partnerSelector").length) {
            $("#partnerSelector .sub-menu").hide();
        }
    });

    // 点击body区域但不包含本身
    $(document).click(function () {
        $(".drop_wrap").hide();
        $(".selector").removeClass("onit dropdown");
        if ($("#partnerSelector").length) {
            $("#partnerSelector .sub-menu").hide();
        }
    });

    var partnerAppSelectHandle = function (reload) {
        if ($(this).attr("data-partnerid") == "all") {
            var selectItem = $(this).parents(".selector").find(".select-item");
            selectItem.val($(this).find("a").text());
            selectItem.attr("title", selectItem.text());
            selectItem.attr("data-appid", '').attr("data-partnerId", $(this).attr("data-partnerid"));
            jQuery.ajax({
                url: '/PartnerAppSelect.json',
                type: 'POST',
                dataType: 'json',
                data: {
                    operationType: "doChange",
                    partnerCodeSelect: $(this).attr("data-partnerid"),
                    appNameSelect: ''
                },
                success: function (data) {
                    if (reload && data.success) {
                        var href = window.location.protocol+'//'+window.location.host+window.location.pathname;
                        window.location.replace(href);
                    }
                }
            });
        } else {
            var selectItem = $(this).parents(".selector").find(".select-item");
            var paId = $(this).parents(".sub-menu:first").attr("id").replace("subAppMenu-", "");
            var pa = $(this).parents('.selector').find('li[data-partnerid="' + escapeHtml(paId) + '"]');
            selectItem.text(pa.find("a").text() + "-" + $(this).find("a").text());
            selectItem.attr("title", selectItem.text());
            selectItem.attr('data-appuuid', $(this).attr("data-appuuid")).attr("data-appid", $(this).attr("data-appid")).attr("data-partnerId", pa.attr("data-partnerid"));
            $("#partnerSelector .sub-menu").hide();
            $('.selector .drop_wrap').hide();
            var appid = $(this).attr("data-appid");
            var appuuid = $(this).attr("data-appuuid");
            jQuery.ajax({
                url: '/PartnerAppSelect.json',
                type: 'POST',
                dataType: 'json',
                data: {
                    operationType: "doChange",
                    appNameSelect: appid,
                    appNameUuidSelect: appuuid,
                    partnerCodeSelect: paId
                },
                success: function (data) {
                    if (reload && data.success) {
                        var href = window.location.protocol+'//'+window.location.host+window.location.pathname;
                        window.location.replace(href);
                    }
                }
            });
        }
    };
    $("#partnerSelector").on('input change propertychange', 'input.select-item', function () {
        var selector = $(".selector");
        selector.find('.drop_cont').css('min-height', '20px');
        var val = $(this).val();
        toggleDrop.call(selector, true, val);
    }).on("mouseenter", ".drop_cont li", function (e) {
        e = e || window.event;
        e.stopPropagation();
        var paId = $(this).attr("data-partnerID");
        var subAppMenu = $(document.getElementById("subAppMenu-" + paId)).eq(0);
        var offsetPaId = $(this).offset(),
            widthPaId = $(this).width(),
            heightPaId = $(this).height();
        subAppMenu.css({
            left: widthPaId,
            top: (offsetPaId.top - 69)
        });
        subAppMenu.show().siblings(".sub-menu").hide();
    }).on("mouseleave", ".drop_cont li", function (e) {
        e = e || window.event;
        e.stopPropagation();
        var paId = $(this).attr("data-partnerID");
        var subAppMenu = $(document.getElementById("subAppMenu-" + paId));
        subAppMenu.hide();
    }).on("click", ".drop_cont li:not(':first')", function () {
        return false;
    }).on("click", ".drop_cont li:first", function (e) {
        e = e || window.event;
        e.stopPropagation();
        partnerAppSelectHandle.call(this, true);
    }).on("mouseenter", ".sub_cont", function (e) {
        e = e || window.event;
        e.stopPropagation();
        $(this).parent(".sub-menu").show();
    }).on("click", ".sub_cont li", function (e) {
        e = e || window.event;
        e.stopPropagation();
        partnerAppSelectHandle.call(this, true);
    });

    var selectAppName = function (reload) {
        var selectItem = $(this).parents(".selector").find(".select-item");
        var appid = $(this).attr("data-appid");
        var appuuid = $(this).attr("data-appuuid");
        selectItem.text($(this).find("a").text()).attr("data-appid", $(this).attr("data-appid"));
        $('.selector .drop_wrap').hide();
        jQuery.ajax({
            url: '/PartnerAppSelect.json',
            type: 'POST',
            dataType: 'json',
            data: {
                operationType: "doChange",
                appNameSelect: appid,
                appNameUuidSelect: appuuid
            },
            success: function (data) {
                if (reload && data.success) {
                    var href = window.location.protocol+'//'+window.location.host+window.location.pathname;
                    window.location.replace(href);
                }
            }
        });
    };
    $("#appSelector").on("click", ".drop_cont ul li", function (e) {
        e = e || window.event;
        e.stopPropagation();
        selectAppName.call(this, true);
    });

    //加载页面时初始化
    function partnerAppSelectInit() {
        var appNameSelect = $('input#appNameSelect').val();
        var partnerCodeSelect = $('input#partnerCodeSelect').val();
        if (partnerCodeSelect && appNameSelect) {
            var subAppMenuId = '#subAppMenu-' + partnerCodeSelect;
            $(".sub_cont ul li", subAppMenuId).each(function (index, dom) {
                if ($(dom).attr('data-appid') == appNameSelect) {
                    partnerAppSelectHandle.call(dom, false);
                    $('.selector .drop_wrap').hide();
                    return false;//break
                }
            });
        } else if (appNameSelect) {
            $("#appSelector .drop_cont ul li").each(function (index, dom) {
                if ($(dom).attr('data-appid') == appNameSelect) {
                    selectAppName.call(dom, false);
                    $('.selector .drop_wrap').hide();
                    return false;//break
                }
            });
        }
    }
});

//  =================================== function displayLength
// 限制字符显示长度
// 2014-03-21
$.fn.extend({
    displayPart: function () {
        return this.each(function () {
            var displayLength = $(this).attr("data-displayLength");
            var text = $(this).text();
            if (!text)
                return "";

            var result = "";
            var count = 0;
            for (var i = 0; i < displayLength; i++) {
                var _char = text.charAt(i);
                if (count >= displayLength)
                    break;
                if (/[^x00-xff]/.test(_char))
                    count++;
                //双字节字符，//[u4e00-u9fa5]中文

                result += _char;
                count++;
            }// End for
            if (result.length < text.length) {
                result += "...";
            }// End if
            $(this).text(result);
        });
    }
});

$(function () {
    $("[data-displayLength]").displayPart();
});

//  =================================== function Draggable Start
// 2014-01-15
$(function () {
    $(".modal").draggable({
        handle: ".modal-header"
    });
});

//  =================================== function aside
//  sideNav functions start
//  author:Rookie
function navAppendSpan() {
    $("#sidebar").find("li").each(function () {
        var _this = $(this);
        if (_this.find("ul").length > 0) {
            _this.find(">a").prepend($("<span>")).click(function () {
                return;
            });
        }
    });
}
// 给日期类对象添加日期差方法，返回日期与diff参数日期的时间差，单位为天
Date.prototype.diff = function (date) {
    return (this.getTime() - date.getTime()) / (24 * 60 * 60 * 1000);
}
function GetDateStr(AddDayCount) {
    var dd = new Date();
    dd.setDate(dd.getDate() + AddDayCount);//获取AddDayCount天后的日期
    var y = dd.getFullYear();
    var m = dd.getMonth() + 1;//获取当前月份的日期
    var d = dd.getDate();
    return y + "/" + m + "/" + d;
};
//  function execution area
$(function () {
    navAppendSpan();
});
function createElementPlaceholder() {
    var doc = document,
        inputs = doc.getElementsByTagName('input'),
        supportPlaceholder = 'placeholder' in doc.createElement('input'),
        placeholder = function (input) {
            var text = input.getAttribute('placeholder'),
                defaultValue = input.defaultValue;
            if (defaultValue == '') {
                input.value = text
            }
            input.onfocus = function () {
                if (input.value === text) {
                    this.value = ''
                }
            };
            input.onblur = function () {
                if (input.value === '') {
                    this.value = text
                }
            }
        };
    if (!supportPlaceholder) {
        for (var i = 0, len = inputs.length; i < len; i++) {
            var input = inputs[i],
                text = input.getAttribute('placeholder');
            if (input.type === 'text' && text) {
                placeholder(input)
            }
        }
    }
}

// 生成随机密码
var passwdTester = /(?:(?:[^ ]*)[0-9](?:[^ ]*)[a-zA-Z](?:[^ ]*))|(?:(?:[^ ]*)[a-zA-Z](?:[^ ]*)[0-9](?:[^ ]*))/gm;
function generateRandPassword() {
    var text = ['abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '1234567890', '~!@#$%^&_.'];
    var rand = function (min, max) {
        return Math.floor(Math.max(min, Math.random() * (max + 1)));
    }
    var len = rand(12, 12); //密码长度
    var pw = '';
    for (var i = 0; i < len; ++i) {
        var strpos = rand(0, 3);
        pw += text[strpos].charAt(rand(0, text[strpos].length));
    }
    if (!passwdTester.test(pw)) {
        return pw = generateRandPassword();
    }
    return pw;
}

// 填充随机密码:当randPaswordButton被点击之后,会讲for属性对应的值设置为随机的密码
$(function () {
    $(".randPasswordButton").click(function () {
        var randPassword = generateRandPassword();
        var textBoxId = $(this).attr("for");

        $("#" + textBoxId).val(randPassword).trigger('keyup');
    });
});

//添加随机密码按钮:重置用户密码+新建合作方
// Sidebar 手风琴效果
/*$(function() {
 $('#sidebar >ul > li > a').click(function() {
 $(this).find("span").toggleClass("open");
 });
 var accordion_head = $('#sidebar >ul > li > a'),
 accordion_body = $('#sidebar >ul > .submenu');
 li_click = $('#sidebar > ul > .submenu li');
 $("#sidebar >ul > li.active").siblings(".submenu").css("display", "none");
 $("#sidebar >ul > li.active").find("a span").addClass("open");
 $(".this>ul").css("display", "block").removeClass("active");

 accordion_head.click(function() {
 $(this).toggleClass("active")//给当前元素添加"active"样式
 .next().slideToggle()//下一个元素显示
 .parent().siblings().children("a").removeClass("active").find("span").removeClass("open")//父元素的兄弟元素的子元素<a>移除"active"样式
 .parent("a").next().slideUp();
 //它们的下一个元素隐藏
 return false;
 });
 });*/

//  headRoom
// 为页面顶部多留些空间。在不需要页头时将其隐藏
/*
 $(function(){
 (function() {
 var header = new Headroom(document.querySelector("#header"), {
 tolerance: 100,
 offset :0,
 classes: {
 initial: "animated",
 pinned: "slideDown",
 unpinned: "slideUp"
 }
 });
 header.init();
 }());
 });
 */


// 获取未读消息数量
function getNewMessageNum() {
    $.ajax({
        url:domainMap_default+'/mycenter/message.json?callback=messgaeCallback',
        type:'get',
        dataType:'jsonp',
        callback:"messgaeCallback",
        fail:function (error) {
            $('#user-nav .marshmallow .after').hide()
        }})


}

function messgaeCallback(data) {
    // console.log(data);
    if (data.success && data.attr != 0) {
        $('#user-nav .marshmallow .after').text(data.attr).show().addClass('animate');
    }
    if (data.attr == 0) {
        $('#user-nav .marshmallow .after').fadeOut(300)
    }
}

function getIntelligenceNum(){
    $.ajax({
        url: domainMap_shine + '/shine/mine/myintelligence.json?operationType=getUnreadNums&callback=intelligenceCallback',
        type:'get',
        dataType:'jsonp',
        callback:"intelligenceCallback",
        fail: function (error) {
            $('#user-nav .marshmallow .after').hide()
        }
    })
}

function intelligenceCallback(data) {

    // console.log(data);
    if (data.success && data.attr != 0) {
        $(".sidebar2-subnav.intelligence").find(".badge").text(data.attr).fadeIn();
    }
    if (data.attr == 0) {
        $(".sidebar2-subnav.intelligence").find(".badge").fadeOut();
    }

}

$(function () {

    //读取未读情报数量
    var intelliSidebar = $(".sidebar2-subnav.intelligence");
    if(intelliSidebar && intelliSidebar.find(".sidebar2-subnav-item").length > 1){
        //获取未读情报数量
        setTimeout(function(){
            getIntelligenceNum();
        },10000);
    }


    setTimeout(getNewMessageNum, 8000);
});

//获取url参数
window.getUrlParam = function (name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]);
    return null; //返回参数值
}

//ip详情报告扩展方法
$.extend({
   showIpDetail : function(_ipAddress){
        $('body').load('')
   }
});
