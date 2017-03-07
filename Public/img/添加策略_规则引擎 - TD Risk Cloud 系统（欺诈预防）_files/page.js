/*!
 * 完成页面内的功能需求
 *
 * Copyright 2013, 项目名称
 *
 * Copyright 2013, user
 * Date: Wed Feb 23 13:55:29 2011 -0500
 */
//  =================================== 弹出框关闭或取消时候的数据重置
$(function () {
    $(".modal-header .close,.modal-footer .cancel").click(function () {
        if ($(this).parents(".modal").attr('data-auto-clear') == 'false') {
            return false;
        }
        var MBody = $(this).parents(".modal").find(".modal-body");
        // 重置form表单元素
        if (MBody.find("form").length > 0) {
            MBody.find("form")[0].reset();
        } else {
            MBody.find("input:not(:hidden)").val("");
        }

        // 重置ui-select
        MBody.find(".ui-select").selectIndex(-1);
        // 重置data-vdcode 和 重置提示框内容
        MBody.find(".pure-control-group input").attr("data-vdcode", 0).siblings("em:not('.require'),span:not('.switch-plugin,.switch-off,.switch-on')").removeClass().text("");
    });
});

//  =================================== 表格的checkbox选择 Start
$(function () {
    var checkAll = $(".CheckedAll");
    var _table = $(".CheckedAll").parents(".table");
    // checkAll单击的变化
    checkAll.click(function () {
        //console.log(this.checked);
        var checkItem = _table.find("tbody td>input:checkbox");
        checkItem.attr("checked", this.checked);
        checkAction();
    });
    // 选择单个checkbox后checkAll的变化
    $(".table tbody").on("change click", "td>input:checkbox", function (e) {
        //console.log("checkbox-changed");
        e = e || window.event;
        e.stopPropagation();
        var checkItem = _table.find("tbody td>input:checkbox");
        checkAll.attr('checked', checkItem.length == checkItem.filter(':checked').length);
        checkAll.attr('checked', !checkItem.filter(':not(:checked)').length);
        checkAction();
    });// End judge
    // 如果这个按钮存在 "disabled" 属性，则点击无效
    $(document).on("click", "button.tool-button-disabled", function () {
        return false;
    });
    $(".btnDelete,.btnEdit").addClass("tool-button-disabled");
    // 判断表格的checkbox的组checkbox选中的长度
    function checkAction() {
        var hascheck = $(".table tbody td>input:checked").length;
        if (hascheck == 0) {
            // 如果有被选择的checkbox
            $(".btnEdit,.btnDelete").addClass("tool-button-disabled");
        } else if (hascheck == 1) {
            $(".btnEdit").removeClass("tool-button-disabled");
            $(".btnDelete").removeClass("tool-button-disabled");
        } else {
            // length>1
            $(".btnEdit").addClass("tool-button-disabled");
            $(".btnDelete").removeClass("tool-button-disabled");
        }
    }
});
//对于设置了class tr-click-check table 将委派选中事件给table
$(function () {
    $("table.tr-click-check").on("click", "tbody tr", function (e) {
        //console.log("tr-clicked");
        e = e || window.event;
        e.stopPropagation();
        if (!$("td:first :checkbox", this).length) {
            return false;
        }
        $("td:first :checkbox", this).trigger("click");
    });
});
//  =================================== treeView List
$(function () {
    $(".treeView>li:last-child,ul.second-level>li:last-child").addClass("last");
    $("ul.second-level").hide();
    $(".treeView>li>a").click(function () {
        var Li = $(this).parent("li");
        var secondUl = Li.find(".second-level");
        if (secondUl.is(":visible")) {
            //如果是显示的
            secondUl.hide();
            Li.removeClass("minus");

        } else {
            secondUl.show();
            Li.addClass("minus");
        }
    });
});

//  =================================== function Mixed Start
//  一些小js 集合
//  Author:Rookie @ 2014-01-20
$(function () {
    $("th[data-order]").css("cursor", "pointer");
    $("th").click(function () {
        var dOrder = $(this).attr("data-order");
        var iArrow = $(this).find("i.arrow");
        var otherArrow = $(this).siblings().find("i");

        // 检测自定义属性data-order如果不存在，则return
        if (dOrder != null && dOrder !== "") {
            // 如果自定义属性存在，则进行下一步
            // 检测th中是否有i.arrow
            if (iArrow.hasClass("up")) {
                // 如果有UP，就删除
                iArrow.removeClass("up").addClass("down");
            } else {
                // 如果没有UP，就添加一个
                iArrow.hasClass("down") ? iArrow.removeClass("down").addClass("up") : iArrow.addClass("up");
                otherArrow.removeClass().addClass("arrow");
            }//  End Of IF
        }
    });// End Of th click

    // TOP list 风险大盘图表右侧的列表
    $(".top-list li:eq(0)").addClass("first");
    $(".top-list li:eq(1)").addClass("second");
    $(".top-list li:eq(2)").addClass("third");
    $(".top-list li").hover(function () {
        $(this).toggleClass("hover");
    }).click(function () {
        $(this).addClass("active").siblings().removeClass("active");
    }).each(function (index, element) {
        $(this).find(".top-num").text(index + 1);
    });
    // 表格有trCurrent时候点击tr时候的背景
    $(document).on("click", 'table.trCurrent tbody tr', function () {
        $(this).addClass("on").siblings().removeClass("on");
    });
    // 用户自定义模板的li第一个添加first
    $(".condition-list ul li:first").addClass("first");
    // 角色模块选择框的最后一个dl添加last
    $(".select-box-content dl:last").addClass("last");
    // 字段规则验证，modal提示框
    $(document).on("click", '.mtip-close', function () {
        $(this).parent(".m-tips").hide();
    });
});
//  =================================== function Mixed End

//  =================================== function other
$(function () {
    $(".policy-roleList-content ul li .overview .title").click(function () {
        var puff = $(this).siblings(".func").find(".puff");
        var detail = $(this).parent(".overview").siblings(".roleList-detail");
        // 策略列表规则查看方向箭头切换
        if (detail.is(":visible")) {
            // 如果detail是显示的
            detail.slideUp(200);
            puff.removeClass("bottom");
        } else {
            // 如果detail是隐藏的
            detail.slideDown(200);
            puff.addClass("bottom");
        }
    });
    $(".func .puff").click(function () {
        $(this).parent(".func").siblings("a.title").trigger("click");
    });

    //  tipsBar
    $("#a_closeTips").click(function () {
        $(this).parent("#tipsBar").hide();
    });
});

//  =================================== function table operation afterShow
//  当鼠标移到相应的表格行的时候，显示当前的操作按钮
//  Author:Rookie @ 2014-01-20
$(function () {
    // 初始化afterShow
    $(".operation.afterShow a,.operation.afterShow i").hide();
    $(document).on("mouseenter", "tr:not('tr.childItem'),.funcList>li", function () {
        if ($(".operation.afterShow", this).length) {
            $(this).find(".operation.afterShow").show();
            $(this).find(".afterShow a").show();
            $(this).find("i").show();
            $(this).siblings().find(".afterShow a,.afterShow i").hide();
        }
    }).on("mouseleave", "tr", function () {
        if ($(".operation.afterShow", this).length) {
            $(this).find(".afterShow a").hide();
            $(this).find("i").hide();
        }
    }).on("click", ".operation.afterShow", function (e) {
        e = e || window.event;
        e.stopPropagation();
    });
});

// 事件历史显示已知状态，分别是通过、人工审核、拒绝
$(function () {
    // 初始化status状态
    $(".status.afterShow a").hide();
    $(".status.afterShow").parents("tr").mouseenter(function () {
        $(this).find(".afterShow a").show();
        $(this).siblings().find(".afterShow a,.afterShow i").hide();
    }).mouseleave(function () {
        $(this).find(".afterShow a").hide();
    });
    $(".status").click(function (event) {
        event.stopPropagation();
    });
});

//  =================================== function table parent and child
// 策略列表js
$(function () {
    // parentItem And childItem click
    $(document).on("click", "tr.parentItem", function () {
        var childItem = $(this).next("tr.childItem");
        var otherItem = $(this).siblings("tr.childItem").not(childItem);
        var _this = $(this);
        var policyUuidVal = _this.children("td:first").find("input:checkbox").val();
        // 点击判断显示隐藏
        if (childItem.is(":visible")) {
            _this.removeClass("hover");
            childItem.hide();
        } else {
            _this.addClass("hover").siblings().removeClass("hover");
            childItem.show();
            otherItem.hide();
        }
    });// end of click
});

//  =================================== function slide searchCondition
//  点击按钮显示搜索条件
//  Author:Rookie @ 2014-01-17
$(function () {
    $("#searchCondition").click(function () {
        var slidePlate = $(".slide-plate");
        if (slidePlate.is(":visible")) {
            slidePlate.slideUp();
            $(this).html("<i class='icon icon-arrow-down'></i>显示搜索条件");
        } else {
            slidePlate.slideDown();
            $(this).html("<i class='icon icon-arrow-up'></i>隐藏搜索条件");
        }
    });
});

//  sideNav functions end
//  =================================== function search
// 搜索
$(function () {
    // 搜索框回车键提交
    $("input.searchInput").keydown(function (event) {
        if (event.keyCode == "13") {//回车键
            // 判断搜索框value是否为空
            var trimVal = $.trim($(this).val());
            //trimVal==null||trimVal==0?$.message.alert("请输入搜索关键字"):$(this).siblings("button").trigger("click");
            $(this).siblings("button").trigger("click");
        }
    });
    // 搜索框条件选择
    if (!$("#selectType li:visible").length) {
        $("#selectType li:first").show();
    }
    $("#selectType").on("mouseenter", "li", function () {
        $("#selectType").addClass("hover");
        $("#selectType li").show();
        $("#selectType li:first").find("a i").addClass("up");
        $(this).find("a").parent("li").siblings().find("a");
        serialize();
    }).on("mouseleave", "li", function () {
        $("#selectType").removeClass("hover");
        $("#selectType li:first").find("a i").removeClass("up");
        $("#selectType li:gt(0)").hide();
        serialize();
    }).on("click", "li", function () {
        $("#selectType").removeClass("hover");
        var _this = $(this).clone();
        $(this).parent("#selectType").find("li:first").before(_this);
        $(this).remove();
        $("#selectType li:gt(0)").hide();
        serialize();
    });
    function serialize() {
        $("#selectType li:first").addClass("first").siblings().removeClass("first");
    }

    serialize();
});

$.setSearchType = function (typeName) {
    $("#selectType li").each(function (index, element) {
        if ($(this).attr("data-searchtype") == typeName) {
            var $_this = $("#selectType li").eq(index).clone();
            $(this).parent("#selectType").find("li:first").before($_this);
            $("#selectType li").eq(index + 1).remove();
            $("#selectType li:first").trigger("mouseenter").delay(50).trigger("click");
        }
    });
};

// function search End
//  =================================== top search
// 顶部header的search 交互
// Author：Rookie@ 2014-09-03
$(function () {
    var searchVal = $("#topSearch .hideSearchText,#topSearch #hideSearchText,#topSearch .hiddenSearchText").val();
    // 判断搜索框是否为空
    if (!$.trim(searchVal) == "") {
        $("#topSearch").fadeIn(500).find("#searchText").val(searchVal);
        $("#searchGuide").hide();
    }
    $("#topSearch #searchText").on('input propertychange change', function () {
        $("#topSearch .hideSearchText,#topSearch #hideSearchText,#topSearch .hiddenSearchText").val($(this).val());
    });
    $(document).on("click", "#searchGuide", function () {
        // 搜索交互动画
        var w = $(document).width();
        var exw = w - 520 - 200;
        var searchVal = $("#topSearch .hideSearchText,#topSearch #hideSearchText,#topSearch .hiddenSearchText").val();
        $("#searchGuide i.iconfont").animate({"right": exw + "px"}).parents("#searchGuide").fadeOut(300).find("i.iconfont").animate({"right": "0px"});
        $("#topSearch").fadeIn(300).find("#searchText").val(searchVal).focus();
    });
    // 点击body隐藏搜索，并清空搜索内容
    $("body").click(function () {
        // 判断搜索的值是否为空，如果是空，就隐藏
        if ($.trim(searchVal) == "") {
            $("#topSearch").fadeOut(300).find("#searchText").val("");
        } else {
            $("#topSearch").show().find("#searchText").val(searchVal);
            ;
        }

        $("#topSearch").click(function (event) {
            event.stopPropagation();
        });
        $("#searchGuide").show();
    });
    $(document).on("keyup", "#searchText", function () {
        var enterButton = $(this).siblings(".searchBtn");
        if (!$.trim($(this).val()) == "") {
            enterButton.show();
        } else {
            enterButton.hide();
        }
    });
    // 监控键盘，如果按下s键，可以调用搜索
    $(document).on('keyup', function (event) {
        if (event.keyCode == "83") {    // S键
            if ($("#topSearch").length == 1) {
                if (!$("#topSearch").is(":visible")) {
                    $("#searchGuide").trigger("click");
                    //$("#searchText").val(searchVal);
                }
            }
        }
    }).on('keyup', 'input,textarea', function (e) {
        e = e || window.event;
        e.stopPropagation();
    });
});

//  =================================== function policyView temp pine
//  Author:Rookie @ 2014-03-15
//  policyRule规则模板列表，滚动条固定
$(function () {
    $(document).scroll(function () {
        var s_top = $(document).scrollTop();
        test(0);
        function test(outHeigth) {
            if (s_top > (200 + outHeigth)) {
                var top = s_top - 200 - outHeigth;
                if (($("#addPolicyRuleManagement").height() - $(".func-left").height()) < top) {
                    return false;
                }
                $(".func-left").css({"top": top + "px"});
            } else {
                $(".func-left").css({"top": "0px"});
            }
        }
    });
});

//  =================================== function Feedback
//Author:huabin @ 2014-03-18
$(function () {
    function Feedback(e) {
        e = e || window.event;
        e.stopPropagation();
        e.preventDefault();
        var feedbackContent = "<div class='feedback'>";
        feedbackContent += "<div class='inputGroup'>";
        feedbackContent += "<label for='feedbackCaption'>问题标签：</label><input type='text' name='feedbackCaption' id='feedbackCaption'/>";
        feedbackContent += "</div>";
        feedbackContent += "<div class='inputGroup'>";
        feedbackContent += "<label for='feedbackDesc' class='textTop'>问题描述：</label><textarea maxLength='125' name='feedbackDesc' id='feedbackDesc'></textarea>";
        feedbackContent += "</div>";
        feedbackContent += "<div class='inputGroup'>";
        feedbackContent += "<label for='feedbackEmail'>联系邮箱：</label><input type='text' name='feedbackCaption' id='feedbackEmail' placeholder='可选'/>";
        feedbackContent += "</div>";
        feedbackContent += "</div>";
        var msg = $(feedbackContent)[0];
        $(msg).find("textarea").keyup(function () {
            var val = $(this).val();
            var maxLength = $(this).attr("maxLength") || 50;
            if (val.length > maxLength) {
                $(this).val(val.substring(0, maxLength));
            }
        });
        $.message.dialog(msg, "问题反馈", function (flag) {
            //TODO AJAX
            if (!flag) {
                return;
            }
            $.message.alert(["您的问题已提交", "感谢您的提交和反馈"], "问题反馈");
        });
    }

    $("#Feedback").on("click", Feedback);
});
//当表格没有数据的时候
$(function () {
    $.fn.extend({
        delDataTip: function () {
            return this.each(function () {
                $(this).nextAll(".noDataTip").remove();
                $(this).parents(".table-area").find(".count-pages").show();
            });
        },
        addDataTip: function (msg) {
            return this.each(function () {
                var tip = msg || "没有发现相应的记录";
                if ($(this).nextAll(".noDataTip").length) {
                    return;
                }
                var len = $("tbody tr", this).length;
                
                if (!len) {
                    $(this).after("<div class='noDataTip'>" + tip + "</div>");
                    $(this).parents(".table-area").find(".count-pages").hide();
                }
            });
        }
    });
    var len = $("table[data-noDataTip] tbody tr").length;
    if (!len) {
        $("table[data-nodatatip]").addDataTip();
    }
});
//使用流程页面的步骤切换
$(function () {
    if (!$(".view-flow-guide").length) {
        return;
    }
    $(".view-flow-guide").on("click", ".view-flow-head li,.view-flow-head .guide-icon", function () {
        var $this = $(this);
        $(this).hasClass("guide-icon") ? $this = $(this).parents("li:first") : "";
        var index = $this.index();
        if ($this.hasClass("active")) {
            return;
        }
        showFlowMain(index);
    });
    function showFlowMain(index) {
        $(".view-flow-guide .view-flow-head li").eq(index).addClass("active").siblings().removeClass("active");
        $(".view-flow-main[data-index=" + index + "]").show();
        $(".view-flow-main").not("[data-index=" + index + "]").hide();
        $(".view-flow-navList .view-flow-navStep:eq(" + index + ")").children("ol").show().end().siblings().children("ol").hide();
    }

    $(".view-flow-navList").on("click", "li.view-flow-navStep>a", function () {
        $("body,html").stop().animate({scrollTop: 0});
        var stepIndex = $(this).parents("li.view-flow-navStep").index();
        if ($(".view-flow-guide active").index() !== stepIndex) {
            showFlowMain(stepIndex);
            //$(".view-flow-navList").css({top:180});
        }
    }).on("click", "li.view-flow-navStep>ol li>a", function (e) {
        e = e || window.event;
        e.stopPropagation();
        e.preventDefault();
        var id = $(this).attr("href");
        $("body,html").stop().animate({scrollTop: $(id).offset().top});
    });
    $(".view-icon-top").on("click", function () {
        $('html, body').animate({scrollTop: 0}, 300);
    });
    var edgeHeightg = 300;
    $(window).on("scroll", function () {
        if ($(window).scrollTop() < edgeHeightg) {
            //$(".view-flow-navList").hide();
        } else {
            //$(".view-flow-navList").show();
        }
        rePosition();
        //$(".view-flow-navList").stop().animate({top:$(window).scrollTop()},1000);
    }).on("resize", function () {
        rePosition();
    });
    function rePosition() {
        var tmpH = 0;
        if ($(window).scrollTop() < edgeHeightg) {
            $(".view-flow-navList").get(0).style.cssText = "";
            $(".view-flow-navList").css({position: "absolute", top: 180, right: 0});
        } else {
            var _left = $(".view-flow-wrap").offset().left + $(".view-flow-wrap").width() + 20 - $(window).scrollLeft();
            var _top = tmpH + 20;
            $(".view-flow-navList").css({position: "fixed", top: _top, left: _left});
        }
    };
    rePosition();
});

//  =================================== 资源中心的问题导航
$(function () {
    // 添加Class的
    $("#subNav .func-list").each(function () {
        $(this).children("li:last").addClass("last");
    });
    // 点击大标题
    $("#subNav").on("click", "h2", function () {
        var nextUl = $(this).next(".func-list");
        var arrow = $(this).find("i");
        if (nextUl.is(":visible")) {
            nextUl.hide();
            $(this).removeClass("open");
        } else {
            $(this).siblings(".func-list").hide().siblings("h2").removeClass("open");
            nextUl.show();
            $(this).addClass("open");
        }
    });

    // 有二级菜单的时候
    $(".has-list>a").click(function () {
        var submenu = $(this).next(".func-sub-menu");
        var sibMenu = $(this).parent(".has-list").siblings(".has-list");
        submenu.find("li:last").addClass("last");
        if (submenu.is(":visible")) {
            submenu.hide();
            $(this).parent(".has-list").removeClass("open");
        } else {
            submenu.show();
            sibMenu.find(".func-sub-menu").hide();
            $(this).parent(".has-list").removeClass("open");
        }
    });
    // 开始load文章
    $("#subNav li a[data-loadpage]").click(function () {
        var loadPage = $(this).attr("data-loadpage");
        var articleH = $(".viewbox").height();// 只是文章的高度
        var wh = $(window).height();
        var _this = $(this);

        $("#content .viewbox").load("/ruleengine/documentation/" + loadPage + ".html .box", function (responseTxt, statusTxt, xhr) {
            if (statusTxt == "success")
            // 外部内容加载成功！
            // load不同高度的文章，保持左边的侧边栏高度协调
                if (wh > articleH) {// 如果可视区域高度大于文章的高度
                    $("#subNav").css("height", (wh - 8) + "px");
                } else {
                    $("#subNav").css("height", $(document).height() + "px");
                }
            if (statusTxt == "error")
                alert("Error: " + xhr.status + ": " + xhr.statusText);
        });

    });

    $("#subNav-projectList li a[data-loadpage]").click(function () {
        var loadPage = $(this).attr("data-loadpage");
        var articleH = $(".viewbox").height();// 只是文章的高度
        var wh = $(window).height();
        var _this = $(this);

        $("#content .viewbox").load("/integration/selfhelp/" + loadPage + ".html .box", function (responseTxt, statusTxt, xhr) {
            if (statusTxt == "success")
            // 外部内容加载成功！
            // load不同高度的文章，保持左边的侧边栏高度协调
                if (wh > articleH) {// 如果可视区域高度大于文章的高度
                    $("#subNav").css("height", (wh - 8) + "px");
                } else {
                    $("#subNav").css("height", $(document).height() + "px");
                }
            if (statusTxt == "error")
                alert("Error: " + xhr.status + ": " + xhr.statusText);
        });

    });

    // 导航布局
    function layoutSubNav() {
        var h = $(document).height() - 8 - $(".head-top").height() - $("#content-header").height() + 6;
        $("#subNav").css("height", h + "px");
    };
    $(window).resize(function () {
        layoutSubNav();
    });
    layoutSubNav();

    // task
    $(document).on("click", ".task h2", function () {
        var taskBody = $(this).next(".task-body");
        if (taskBody.is(":visible")) {
            taskBody.hide();
            $(this).removeClass("expanded");
        } else {
            taskBody.show();
            $(this).addClass("expanded");
        }

        // 重置左边的菜单导航
        layoutSubNav();
    });
});


