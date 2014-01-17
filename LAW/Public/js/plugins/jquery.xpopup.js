/**
 * This jQuery plugin displays pagination links inside the selected elements.
 *
 * @author Gabriel Birke (birke *at* d-scribe *dot* de)
 * @version 1.2
 * @param {int} maxentries Number of entries to paginate
 * @param {Object} opts Several options (see README for documentation)
 * @return {Object} jQuery Object
 */
jQuery.fn.xpopup = function (opts) {
    opts = jQuery.extend({
        title: '标题',
        message: '内容',
        sure_callback: function () {
            return false;
        },
        no_callback: function () {
            return false;
        }
    }, opts || {});

    return this.each(function () {

        function getHtml() {
            var html =
                '<div class="mask-div" id="jQuery_fn_xpopup_container" style="display: none">\
                     <h3><label id="jQuery_fn_xpopup_title"></label></h3>\
                     <div class="warm">\
                     <img src="/images/warm.png"><label id="jQuery_fn_xpopup_message"></label>？\
                     </div>\
                     <div class="mask-input">\
                         <input type="button" id="jQuery_fn_xpopup_yes" value="确认" class="x-green_but">\
                         <input type="button" id="jQuery_fn_xpopup_no" value="取消" class="gray_but">\
                     </div>\
                 </div>'

            return html;
        }

        $("#jQuery_fn_xpopup_container").remove();
        var h = getHtml();
        $("body").append(h);
        var divH = $("#jQuery_fn_xpopup_container").height();
        var divW = $("#jQuery_fn_xpopup_container").width();
        var x = $(this).width() / 2;
        var y = $(this).height() / 2;
        $("#jQuery_fn_xpopup_container").fadeIn().prepend('').offset({ top: (y - divH / 2), left: (x - divW / 2) }).css({"z-index": "10000", "position": "absolute"});
        var div = $('<div/>').prop({'class': 'divpop'}).appendTo($('body'));
        div.css({'filter': 'alpha(opacity=80)'}).fadeIn();
        //console.log("divH" + divH + ":divW" + divW + ":x" + x + ":y" + y + ":top" + (y - divH / 2) + "left:" + (x - divW / 2));
        $("#jQuery_fn_xpopup_title").text(opts.title);
        $("#jQuery_fn_xpopup_message").text(opts.message);
        $("#jQuery_fn_xpopup_yes").click(function () {
            $("#jQuery_fn_xpopup_container").remove();
            $(".divpop").remove();
            opts.sure_callback();
        });
        $("#jQuery_fn_xpopup_no").click(function () {
            $("#jQuery_fn_xpopup_container").remove();
            $(".divpop").remove();
            opts.no_callback();
        });
        // opts.callback(current_page, this);
    });
}


