/**
 * Created by Administrator on 14-3-5.
 */

jQuery(function () {
    var $ = jQuery;

    $.fn.extend({
        search: function (targetField, dataList, searchField) {
            return this.each(function () {
                $(this).find(targetField).on("blur", function () {
                    if ($(this).find(dataList).is(":focus")) {
                        return false;
                    }
                    $(dataList).slideUp("normal", function () {
                        $(dataList).removeClass("slideDown");
                    });
                });
                $(this).find(targetField).on("keyup click", function (e) {
                    var $this = $(this);
                    var $dataList = $(dataList),
                        $ruleTemplates = $("li",searchField),
                        $ruleTitle = $("h4",searchField),
                        filterText = $.trim($this.val().toLowerCase()),
                        filterArr = [];

                    $ruleTemplates.each(function () {
                        var dataType = $(this).attr("data-type") ? $(this).attr("data-type") : "";
                        var text = $(this).text() ? $(this).text() : "";
                        if (filterText.length
                            && (text.indexOf(filterText) > -1
                            || dataType.indexOf(filterText) > -1)) {
                            filterArr.push($(this).clone());
                        }
                    });

                    $dataList.empty();
                    if (filterArr.length) {
                        $dataList.append(filterArr);
                        $dataList.addClass("slideDown");
                    } else {
                        $(dataList).removeClass("slideDown");
                        return false;
                    }
                    $dataList.slideDown("normal");
                });
            });
        }
    });
});