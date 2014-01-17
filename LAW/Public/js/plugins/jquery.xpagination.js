/**
 * This jQuery plugin displays pagination links inside the selected elements.
 *
 * @author Gabriel Birke (birke *at* d-scribe *dot* de)
 * @version 1.2
 * @param {int} maxentries Number of entries to paginate
 * @param {Object} opts Several options (see README for documentation)
 * @return {Object} jQuery Object
 */
jQuery.fn.pagination = function (opts) {
    opts = jQuery.extend({
        total_count: 100,
        page_size: 10,
        current_page: 1,
        callback: function () {
            return false;
        },
        page_toolbar: ""
    }, opts || {});

    return this.each(function () {

        var panel = jQuery(this);
        var current_page = opts.current_page;
        var total_count = opts.total_count;
        var total_page = 0;

        function draw() {
            var html = '<li class="fl li-first"> </li>'+
                        '<li class="fl">' + opts.page_toolbar + ' </li>\
                        <li class="fl li-three">\
                            <a href="javascript:void(0)" id="firstPage" class="queryTrigger"><img src="/images/first_page.png"></a>\
                            <a href="javascript:void(0)" id="backwardPage" class="queryTrigger"><img src="/images/back_page.png"></a>\
                            <span>第 &nbsp;<input type="text" id="currentPageInput"  class="inputstyle inpWidth40 ac">  &nbsp; 页</span>\
                            <a href="javascript:void(0)" id="forwardPage" class="queryTrigger"><img src="/images/next_page.png"></a>\
                            <a href="javascript:void(0)" id="lastPage" class="queryTrigger"><img src="/images/last_page.png"></a>\
                          </li>\
                          <li class="fr li-last">显示 <label id="pageStart"></label> 到 <label id="pageSizeLabel"></label> ，共 <label\
                          id="totalCountLabel"></label> 个项目\
                          </li>';

            panel.empty();
            if (total_count > 0) panel.append(html);
            total_page = (total_count % opts.page_size == 0) ? Math.floor(total_count / opts.page_size) : Math.floor(total_count / opts.page_size) + 1;
            $("#totalCountLabel").text(total_count);
            $("#currentPageInput").val(opts.current_page);
            $("#pageStart").text((opts.current_page - 1) * opts.page_size + 1);
            $("#pageSizeLabel").text(opts.current_page * opts.page_size < total_count ? opts.current_page * opts.page_size : total_count);

            $("#firstPage").click(function () {
                current_page = 1;
                opts.callback(current_page, this);
            });

            $('#currentPageInput').bind("enterKey",function(e){
                //do stuff here

                current_page=parseInt($("#currentPageInput").val());
                if (current_page < 1) {
                    current_page = 1;
                }
                if (current_page > total_page) {
                    current_page = total_page;
                }
                $("#currentPageInput").val(current_page);
                opts.callback(current_page, this);
            });
            $('#currentPageInput').keyup(function(e){
                if(e.keyCode == 13)
                {
                    $(this).trigger("enterKey");
                }
            });

            $("#backwardPage").click(function () {
                current_page--;
                if (current_page < 1) {
                    current_page = 1;
                }
                opts.callback(current_page, this);
            });
            $("#forwardPage").click(function () {
                current_page++;
                if (current_page > total_page) {
                    current_page = total_page;
                }
                opts.callback(current_page, this);
            });
            $("#lastPage").click(function () {
                current_page = total_page;
                opts.callback(current_page, this);
            });

        }


        draw();

        // opts.callback(current_page, this);
    });
}


