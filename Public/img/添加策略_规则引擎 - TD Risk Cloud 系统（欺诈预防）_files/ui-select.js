/**
 * 使用说明：
 * 1..selectInit(data) 方法:
 * 对select插件进行初始化
 * 如果 data 不是 object对象，则回从插件下面的li元素获取数据进行初始化
 * 如果 data 是正确的 object 对象，并且格式如下
 * {
 *     dataList:[{text:xxxx,value:xxxx,……},……],//需要的加载的选项，需要有text（用于显示）和value（用于取值），必须要有，另外可以有disabled=true/false来设置是否禁用选项
 *     selectIndex:number,//默认-1，显示默认text，值为默认值，可以不设置，若不设置则显示默认
 *     defaultText:xxxxx,// 设置默认显示的文字,可以不设置
 *     defaultValue:xxxxx// 设置默认值，可以不设置
 *     callback:function // 默认为undefined，可以不设置，在初始化时不执行，在选择选项后回调（默认选项不执行回调）
 * }
 *
 * 2..selectReset(data) 方法 执行 selectInit
 *
 * 3..selectIndex(index,callback) 方法
 * 根据index选择特定的选项（index从0开始）
 * callbak为回调方法 （index在选项范围内才会执行）
 *
 * 4..selectValue(callback)方法
 * 获取选中项的值，callback为回调函数
 *
 * 5..selectDisableItem(index,flag) 方法
 * 是否禁用对应的选项
 *
 */
(function ($) {
    var defaultData = {
        defaultText: "请选择",
        defaultValue: "",
        dataList: [],     //[{text:xxx,value:xxx,...},...]
        selectIndex: -1,
        callback: undefined
    };
    $.fn.extend({
        selectInit: function (data) {
            return this.each(function () {
                var $this = $(this);
                var placeholder = $(".ui-select-trigger", $this).text();
                if ($this.attr("data-selectInput")) {
                    var tmp = '<span class="ui-select-input" style="display:none;"><input type="text" placeholder="' + (placeholder || '请选择') + '"/><i><b></b></i></span>'
                    $(".ui-select-input", $this).length ? "" : $(".ui-select-trigger", $this).before(tmp);
                    $(".ui-select-trigger", $this).hide();
                    $(".ui-select-input", $this).show();
                    $(".ui-select-input input", $this).off().on("keyup keydown", function (e) {
                        var keyCode = e.keyCode ;
                        var notAllow = [18,91,17,16,20,27,13,35,24,33,36,44,45,37,38,39,40];//不引起文本变化的KEYCODE集合
                        if(notAllow.indexOf(keyCode)==-1){
                            $(".ui-select-input input", $this).attr("data-typed", "true");
                        }                    }).on("change input propertychange", function () {
                        $this.selectFilter($(".ui-select-input input", $this).val());
                    });
                } else {
                    var tmp = '<span class="ui-select-input" style="display:none;"><input type="text" placeholder="' + (placeholder || '请选择') + '"/><i><b></b></i></span>'
                    $(".ui-select-input", $this).length ? "" : $(".ui-select-trigger", $this).before(tmp);
                    $(".ui-select-trigger", $this).hide();
                    $(".ui-select-input", $this).show();
                    $(".ui-select-input input", $this).off().on("change input propertychange", function () {
                        $this.selectFilter($(".ui-select-input input", $this).val());
                    });
                }
                if ($this.hasClass("ui-select-disabled") || (typeof $this.attr("disabled") !== "undefined")) {
                    $(".ui-select-input input", $this).attr('disabled', true);
                } else {
                    $(".ui-select-input input", $this).removeAttr('disabled');
                }
                if ($this.hasClass("ui-select-readonly") || (typeof $this.attr("readonly") !== "undefined")) {
                    $(".ui-select-input input", $this).attr('readonly', true);
                } else {
                    $(".ui-select-input input", $this).removeAttr('readonly');
                }
                var cachedata = $this.data("ui-select-items");
                if(data && data.force){
                    cachedata.dataList = [];
                }
                var isEmptyArray = Object.prototype.toString.call(cachedata) === '[object Object]' ? $.isArray(cachedata.dataList) && cachedata.dataList.length === 0 : false;
                var selectIndex = -1;
                // 设置ui-select-content的宽度和ui-select相同
                $this.find(".ui-select-content").width($this.width());
                if (typeof data === "object" && $.isArray(data.dataList)) {
                    cachedata = $.extend(true, {}, defaultData, data);
                    var dataList = cachedata.dataList;
                    var fragArr = [];
                    for (var i = 0, length = dataList.length; i < length; i++) {
                        if (dataList[i].text) {
                            var disabled = dataList[i].disabled ? " ui-select-item-disabled" : "";
                            var value = dataList[i].value;
                            var type = dataList[i].type || '';
                            var word = dataList[i].word || '';
                            if (!Object.prototype.toString.call(value) === '[object Number]') {
                                value = value || '';
                            }
                            if (value === '') {
                                continue;
                            }
                            var item = '<li class="ui-select-item' + disabled + '" data-value="' + value + '" data-type="' + type + '" data-word="'+word+'">' + escapeHtml(dataList[i].text) + '</li>';
                            fragArr.push(item);
                            if (dataList[i].selected) {
                                selectIndex = i;
                            }
                        }
                    }
                    $(".ui-select-content", $this).empty().append(fragArr);
                }
                else if (typeof cachedata === 'undefined' || isEmptyArray) {
                    cachedata = $.extend(true, {}, defaultData, data);
                    var items = $(".ui-select-item", $this);
                    items.each(function (index, item) {
                        cachedata.dataList[index] = {text: $(item).text(), value: $(item).attr("data-value")};
                        if (typeof $(item).attr("data-selected") !== "undefined") {
                            selectIndex = index;
                            $(item).removeAttr("data-selected");
                        }
                        //$(item).removeAttr("data-value");
                    });
                }
                if (typeof cachedata !== 'undefined' && typeof data.callback === 'function') {
                    cachedata.callback = data.callback;
                }
                $this.data("ui-select-items", cachedata);
                if (typeof data.selectIndex === 'number') {
                    cachedata.selectIndex = data.selectIndex;
                } else {
                    cachedata.selectIndex = selectIndex;
                }
                cachedata.defaultText = $this.attr('data-default-text') || defaultData.defaultText;
                if (typeof data === "object" && data.defaultText) {
                    cachedata.defaultText = data.defaultText;
                }

                $(".ui-select-content", $this).hide();
                if($.selectHandleType == 'c' || data.selectHandleType == 'c'){
                    $(".ui-select-content", $this).off();
                    $('.ui-select-content',$this).on("click", ".ui-select-item",function (e) {
                        var e = e || window.event;
                        e.stopPropagation();
                        e.preventDefault();
                        if ($(this).hasClass("ui-select-item-disabled")) {
                            return false;
                        }
                        var index = $(this).index();
                        $(".ui-select-content", $this).hide();
                        $(".ui-select-input input", $this).removeAttr("data-typed");
                        $this.removeClass("ui-select-down");
                        $this.selectIndex(index, cachedata.callback);
                    });
                }
                else {
                    $(".ui-select-item", $this).off();
                    $('.ui-select-item',$this).on("click",function (e) {
                        var e = e || window.event;
                        e.stopPropagation();
                        e.preventDefault();
                        if ($(this).hasClass("ui-select-item-disabled")) {
                            return false;
                        }
                        var index = $(this).index();
                        $(".ui-select-content", $this).hide();
                        $(".ui-select-input input", $this).removeAttr("data-typed");
                        $this.removeClass("ui-select-down");
                        $this.selectIndex(index, cachedata.callback);
                    });
                }

                if ($this.attr('data-auto-select') != "false") {
                    $this.selectIndex(cachedata.selectIndex);
                }
            });
        },
        selectReset: function (data) {
            return this.each(function () {
                var $this = $(this);
                if (typeof data !== 'object') {
                    data = {dataList: []};
                }
                $this.selectInit(data);
            });
        },
        selectIndex: function (index, callback) {
            return this.each(function () {
                var $this = $(this);
                if (!$this.hasClass("ui-select")) {
                    return false;
                }
                var cachedata = $this.data("ui-select-items");
                if (typeof callback !== 'function') {
                    callback = cachedata.callback;
                }
                if (index > -1 && index < cachedata.dataList.length) {
                    //显示对应数据
                    $(".ui-select-trigger span", $this).text(cachedata.dataList[index].text);
                    cachedata.selectIndex = index;
                    if (typeof callback === 'function') {
                        callback.call($this, index, cachedata.dataList[index]);
                    }
                    if ($this.attr("data-selectInput")) {//若是输入模式显示key(value)
                        $(".ui-select-input input", $this).val($.trim(cachedata.dataList[index].text));
                    } else {
                        $(".ui-select-input input", $this).val($.trim(cachedata.dataList[index].text));
                    }
                } else {
                    //显示默认
                    $(".ui-select-trigger span", $this).text(cachedata.defaultText);
                    cachedata.selectIndex = -1;
                    if ($this.attr("data-selectInput")) {//若是输入模式显示key(value)
                        $(".ui-select-input input", $this).val(cachedata.defaultValue);
                    } else {
                        $(".ui-select-input input", $this).val(cachedata.defaultValue);
                    }
                }
                if ($(".ui-select-content li", $this).length <= 0) {
                    $(".ui-select-trigger span", $this).text(cachedata.defaultText);
                    $(".ui-select-input input", $this).val(cachedata.defaultText)
                }
                var nowIndex = cachedata.selectIndex;
                return cachedata.dataList[nowIndex];
            });
        },
        selectByValue: function (value, callback) {
            var $this = $(this);
            if (!$this.hasClass("ui-select")) {
                return false;
            }
            var cachedata = $this.data("ui-select-items");
            var index = -1;
            for (var len = cachedata.dataList.length, i = 0; i < len; i++) {
                if (cachedata.dataList[i].value == value) {
                    index = i;
                }
            }
            return index;
        },
        chooseByValue: function (value) {
            var index = this.selectByValue(value);
            this.selectIndex(index);
        },
        selectAttr: function (attrName) {
            var index = this.selectCurrentIndex();
            return $('.ui-select-content li', this).eq(index).attr(attrName);
        },
        selectCurrentIndex: function () {
            var $this = $(this);
            if (!$this.hasClass("ui-select")) {
                return false;
            }
            var cachedata = $this.data("ui-select-items");
            var index = -1;
            index = cachedata.selectIndex;
            return index;
        },
        selectValue: function (callback) {
            var $this = $(this);
            if (!$this.hasClass("ui-select")) {
                return false;
            }
            var cachedata = $this.data("ui-select-items");
            var index = cachedata.selectIndex;
            if (typeof callback === 'function') {
                callback(cachedata.dataList[index]);
            }
            if ($this.attr("data-selectInput") && typeof callback === 'string') {
                var newValue = callback;
                $(".ui-select-input input", $this).val(newValue).attr("data-typed", true);
            }
            if ($this.attr("data-selectInput") && $(".ui-select-input input", $this).attr("data-typed")) {
                return $(".ui-select-input input", $this).val();
            } else if (index > -1 && index < cachedata.dataList.length) {
                return cachedata.dataList[index].value;
            } else {
                return cachedata.defaultValue;
            }
        },
        selectType: function () {
            return $(".ui-select-input input", this).attr("data-typed") ? "input" : "select";
        },
        selectText: function (callback) {
            var $this = $(this);
            if (!$this.hasClass("ui-select")) {
                return false;
            }
            var cachedata = $this.data("ui-select-items");
            var index = cachedata.selectIndex;
            if (typeof callback === 'function') {
                callback(cachedata.dataList[index]);
            }
            if (index > -1 && index < cachedata.dataList.length) {
                return cachedata.dataList[index].text;
            } else {
                return cachedata.defaultText;
            }
        },
        selectDisableItem: function (index, flag) {
            var $this = $(this);
            if (!$this.hasClass("ui-select")) {
                return false;
            }
            var cachedata = $this.data("ui-select-items");
            if (index > -1 && index < cachedata.dataList.length) {
                cachedata.dataList[index].disabled = flag;
                if (flag === true)
                    $(".ui-select-item", $this).eq(index).addClass("ui-select-item-disabled");
                else
                    $(".ui-select-item", $this).eq(index).removeClass("ui-select-item-disabled");
            }
        },
        selectAddItem: function (selectObj, attrs) {
            var $this = $(this);
            var _li = $('<li class="ui-select-item"></li>');
            _li.attr('data-value', selectObj.value);
            _li.text(selectObj.text);
            $.each(attrs, function (index, attr) {
                _li.attr(attr.key, attr.value);
            });
            $('.ui-select-content', $this).append(_li);
            var cachedata = $this.data("ui-select-items");
            if(cachedata && cachedata.dataList){
                cachedata.dataList.push(selectObj);
            }
            // var selectIndx = $this.selectCurrentIndex();
            // $this.removeData('ui-select-items');
            // $this.selectInit({
            //     selectIndex: selectIndx
            // });
        },
        selectRemoveItem: function (dataValue) {
            var $this = $(this);
            var selectIndx = $this.selectCurrentIndex();
            var delIndex = -1;
            var cachedata = $this.data("ui-select-items");
            if(cachedata && cachedata.dataList){
                $.each(cachedata.dataList,function(i,data){
                    if(data.value == dataValue){
                        delIndex = i;
                        return false;
                    }
                });
                if(delIndex>-1){
                    cachedata.dataList.splice(delIndex,1);
                }
            }
            $('.ui-select-content li', $this).filter(function (index) {
                if ($(this).attr('data-value') == dataValue) {
                    selectIndx == index ? selectIndx = -1 : selectIndx;
                    return true;
                }
            }).remove();
            //$this.removeData('ui-select-items');
            //$this.selectInit({
            //    selectIndex: selectIndx
            //});
        },
        selectPlaceholder: function (pv) {
            return this.each(function () {
                var _this = $(this);
                if (_this.attr('data-selectInput')) {
                    return;
                }
                _this.find('.ui-select-input input').attr('placeholder', pv);
            });
        },
        selectFilter: function (fv) {
            fv = fv || '';
            var _this = $(this);
            var nv = _this.selectText();
            _this
            .selectPlaceholder(nv || '')
            .find('.ui-select-item')
            .hide()
            .filter(function () {
                var attr = $(this).attr('data-value') || '';
                var text = $(this).text() || '';
                attr = attr.toLowerCase();
                text = text.toLowerCase();
                return (attr.indexOf(fv.toLowerCase()) > -1 || text.indexOf(fv.toLowerCase()) > -1);
            })
            .show();
            $(".ui-select-content", _this).show();
            _this.addClass("ui-select-down");
        }
    });

    $(function () {
        $(".ui-select").selectInit({});
        $(document).on("click", ".ui-select", function (e) {
            var e = e || window.event;
            e.stopPropagation();
            var $this = $(this);
            if ($this.hasClass("ui-select-disabled") || (typeof $this.attr("disabled") !== "undefined")) {
                return false;
            }
            $(".ui-select").not(this).each(function (i, d) {
                $(".ui-select-content", $(d)).hide();
                $(d).removeClass("ui-select-down");
            });
            if ($(".ui-select-content", $this).is(":hidden")) {
                $(".ui-select-content", $this).show();
                $this.addClass("ui-select-down");
            } else {
                $(".ui-select-content", $this).hide();
                $this.removeClass("ui-select-down");
            }
        }).on("click", function () {
            $(".ui-select .ui-select-content").hide();
            $(".ui-select").removeClass("ui-select-down");
        }).on('mouseenter', '.ui-select-item', function () {
            if (!$(this).attr('title')) {
                $(this).attr('title', $.trim($(this).text()));
            }
        }).on('mouseenter', '.ui-select-trigger', function () {
            $(this).attr('title', $.trim($(this).find('span').text()));
        });
    });
})(jQuery);