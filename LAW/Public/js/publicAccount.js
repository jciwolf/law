/**
 * Created with JetBrains PhpStorm.
 * User: qianxuefeng
 * Date: 13-10-31
 * Time: 下午2:21
 * To change this template use File | Settings | File Templates.
 */

$(function () {
        $("#beginDate").val($.datepicker.formatDate('yy-mm-dd', new Date()));
        var futureDate = new Date();
        futureDate.setDate(futureDate.getDate() + 365);
        $("#endDate").val($.datepicker.formatDate('yy-mm-dd', futureDate));
        PublicAccount.query(1);
        $(".queryTrigger").click(function () {
            if( $("#endDate").val()< $("#beginDate").val())
            {
                alert("开始时间不能晚于结束时间");
                return;
            }
            PublicAccount.query(1);
        });
        $("#beginDate").datepicker({"dateFormat": "yy-mm-dd"});
        $("#endDate").datepicker({"dateFormat": "yy-mm-dd"});

        $("#newPublicAccount").click(function () {
            var popID = $(this).attr('data-reveal-id'); //Get Popup Name
            var popURL = $(this).attr('title'); //Get Popup href to define size
            pid=0;
            PublicAccount.clearForm();
            popupModify(popID, popURL);
            return false;
        });

        $("#cancelButton").click(function () {
            //default inputs
            popupclose();
        });

        if (location.search == '?add') $("#newPublicAccount").click();
    }
);

PublicAccount = {};
PublicAccount.total = 0;
PublicAccount.pageSize = global.pagesize;

PublicAccount.refreshCallback = function () {
    $('input[id^=CheckAll]').each(function () {
        $(this).unbind('click').click(function () {
            tableCheck(this);
        });
    });
    $('input[id^=Check_]').each(function () {
        $(this).unbind('click').click(function () {
            $(this).prop('checked', !$(this).prop('checked'));
        });
    });
    $(".modifyHack").click(
        function () {
            // alert(1);
            var id = $(this).attr("publicAccountID");
            $(location).attr('href', '/publicAccount/modify/?id=' + id);
        }
    );
    $(".closeHack").click(
        function () {
            updateStatus(2, this);

        }
    );
    $(".openHack").click(
        function () {
            updateStatus(1, this);

        }
    );


    $("#batchOpen").unbind('click').click(function () {

        PublicAccount.batchUpdateStatus(this, 1);
    });
    $("#batchClose").unbind('click').click(function () {
        PublicAccount.batchUpdateStatus(this, 2);
    });
    $("#batchDelete").unbind('click').click(function () {

        PublicAccount.batchDelete(this);
    });


}
PublicAccount.updateStatus = function (status, clicker) {
    var s = "";
    if (status == 2) {
        s = "您真的要关闭吗？";
    }
    else
        s = "您真的要开启吗？";
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        // alert(1);
        var id = $(clicker).attr("publicAccountID");
        ConsumeObject('/publicAccount/ajaxUpdateStatus', {id: id, status: status}, function (r) {
            if (r.errcode == 0) {
                PublicAccount.query(1);
            }
        }, null);

    }
    });
}
PublicAccount.Delete = function (id, clicker) {
    var s = "";
    if (status == 2) {
        s = "您真的要删除吗？";
    }
    else
        s = "您真的要删除吗？";
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        // alert(1);

        ConsumeObject('/publicAccount/del', {id: id}, function (r) {
            if (r.errcode == 0) {
                PublicAccount.query(1);
            }
        }, null);

    }
    });
}
PublicAccount.batchDelete = function (invoker) {
    var s = "您真的要删除吗？";
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        var list = getAllCheck();
        if (list != "") {
            $(invoker).text("正在处理...");
            ConsumeObject('/publicAccount/batchDel/', {ids: list}, function (r) {
                if (r.errcode == 0) {
                    var invoker = r.i;
                    PublicAccount.query(1);
                }
            }, $(invoker));

        }

    }
    });
}
$(".managementHack").click(
    function () {
        // alert(1);
        var id = $(this).attr("publicAccountID");
        $(location).attr("href", "/reply/follow/" + id + "/");

    }
);

PublicAccount.batchUpdateStatus = function (invoker, status) {
    var list = getAllCheck();
    if (list != "") {
        $(invoker).text("正在处理...");
        ConsumeObject('/publicAccount/batchUpdateStatus/', {ids: list, status: status}, function (r) {
            if (r.errcode == 0) {
                var invoker = r.i;
                invoker.text("设置成功");
                PublicAccount.query(1);
            }
        }, $(invoker));

    }
}
PublicAccount.query = function (currentPage) {
    var i = 0;
    var queryParameter = {};
    queryParameter.systemAccountName = $("#merchantName").val();
    queryParameter.name = $("#name").val();
    queryParameter.weixin = $("#weixin").val();
    queryParameter.beginDate = $("#beginDate").val();
    queryParameter.endDate = $("#endDate").val();
    queryParameter.status = $("#status").val();
    queryParameter.type = $("#type").val();
    queryParameter.rows = PublicAccount.pageSize;
    queryParameter.page = currentPage;
    ConsumeObject('/publicAccount/ajaxGet', queryParameter, function (r) {
        if (r.errcode == 0) {
            $('#ResultList tr').each(function () {
                if (i++ > 0) if (!$(this).hasClass("lasttr")) {
                    $(this).remove();
                }
            });
            $(".noresult").remove();
            if(r.data.list.length==0)
            {

                var tr = $('<tr/>').attr({ 'class': 'odd' });
                $('.lasttr').before(tr).hide();
                $('<td/>').prop({ 'colspan': 8, 'innerHTML': "无搜索结果，请重新搜索！"}).appendTo(tr);
                return;
            }
            $('.lasttr').show();

            var hasOpenItem=false;
            $.each(r.data.list, function (index, item) {
                var tr = $('<tr/>').attr({ 'class': index % 2 == 1 ? 'odd' : '', 'id': 'tr' + item.id });
                $('.lasttr').before(tr);
                tr.mouseenter(function () {
                    $(this).addClass("hover");
                });
                tr.mouseleave(function () {
                    $(this).removeClass("hover");
                });
                $(tr).unbind('click').click(function () {
                    tableCheck('#Check_' + item.id);
                });
                var td, input, span, p, a, label;
                td = $('<td/>').prop('class', '').appendTo(tr);
                label = $('<label/>').prop({ 'class': 'check-one' }).appendTo(td);
                input = $('<input/>').prop({ 'type': 'checkbox', 'value': item.id, 'id': 'Check_' + item.id}).appendTo(label);
                td = $('<td/>').prop('innerHTML', item.pulicAccountName).appendTo(tr);
                td = $('<td/>').prop('innerHTML', item.weixin).appendTo(tr);
                td = $('<td/>').prop('innerHTML', item.merchantName).appendTo(tr);
                td = $('<td/>').prop('innerHTML', item.beginDate.substring(0, 10)).appendTo(tr);
                td = $('<td/>').prop('innerHTML', item.endDate.substring(0, 10)).appendTo(tr);
                td = $('<td/>').prop('innerHTML', item.status == 1 ? '启用' : '关闭').appendTo(tr);
                td = $('<td/>').prop('class', '').appendTo(tr);
                span = $('<span/>').prop({ 'class': 'actionMenu' }).appendTo(td);
                a = $('<a/>').prop({ 'href': 'javascript:void(0);', 'innerHTML': '修改' }).appendTo(span);
                a.attr('publicAccountID', item.id).attr("title", "");
                a.click(function () {
                    PublicAccount.modify(this);
                });


                if(item.status==1)
                {
                    hasOpenItem=true;
                    a = $('<a/>').prop({ 'href': 'javascript:void(0);', 'innerHTML': '删除' }).attr({'disabled':"disabled"}).css("color","gray").attr('publicAccountID', item.id).appendTo(span);
                }
                else

                {
                    a = $('<a/>').prop({ 'href': 'javascript:void(0);', 'innerHTML': '删除' }).attr('publicAccountID', item.id).appendTo(span);
                    a.click(function () {
                        PublicAccount.Delete(item.id, this);
                    });
                }
                a = $('<a/>').prop({ 'href': 'javascript:void(0);', 'innerHTML': item.status == 2 ? '启用' : '关闭' }).attr('publicAccountID', item.id).appendTo(span);
                a.click(function () {
                    PublicAccount.updateStatus(item.status == 1 ? 2 : 1, this);
                });
                a = $('<a/>').prop({ 'href': '/reply/follow?pid=' + item.id, 'innerHTML': '功能管理' }).appendTo(span);



            });

            PublicAccount.total = r.data.count;
            $(".list_bottom").pagination({total_count: PublicAccount.total,
                    page_size: PublicAccount.pageSize,
                    current_page: currentPage,
                    callback: function (currentPage, invoker) {
                        PublicAccount.query(currentPage);
                    },
                    page_toolbar: '<span class="actionMenu"><a href="javascript:void(0)" id="batchOpen">批量开启</a><a href="javascript:void(0)" id="batchClose">批量关闭</a>'+
                        ''//(hasOpenItem==true?'<a href="javascript:void(0)" disabled="disabled" style="color:gray" id="">批量删除</a>':'<a href="javascript:void(0)" id="batchDelete">批量删除</a>')
                        +'</span>'
            });
            PublicAccount.refreshCallback();
            $('input[id^=Check_]').each(function(){
                $(this).unbind('click').click(function(){$(this).prop('checked', !$(this).prop('checked'));});
            });
        }
        //console.log(r);


    });
}

PublicAccount.modify = function (invoker) {
    var popID = $(invoker).attr('data-reveal-id'); //Get Popup Name
    var popURL = $(invoker).attr('title'); //Get Popup href to define size
    var publicAccountId = $(invoker).attr('publicAccountId');
    //ajax get value
    PublicAccount.clearForm();
    $('.mask-div600 h3').prop('innerHTML', '公众号修改');

    ConsumeObject('/publicAccount/ajaxGetOne', {PubicAccountId: publicAccountId}, function (r) {
        if (r.errcode == 0) {

            publicAccountModel = r.data;
            if (!(typeof publicAccountModel == 'undefined')) {
                pid = publicAccountModel.id;
                $("#namepop").val(publicAccountModel.name);
                $("#beginDateInputpop").val(publicAccountModel.beginDate.substring(0, 10));
                $("#endDateInputpop").val(publicAccountModel.endDate.substring(0, 10));
                $("#originalId").val(publicAccountModel.originalId);
                $("#weixinpop").val(publicAccountModel.weixin);
                $("#AppId").val(publicAccountModel.appId);
                $("#AppSecret").val(publicAccountModel.appSecret);

                $("#token").val(publicAccountModel.token);
                RadioCheck("#weixinpay" + publicAccountModel.weixinPay);
                RadioCheck('#weixintype' + publicAccountModel.type);
                RadioCheck('#customerservice' + publicAccountModel.customerService);
                $("#statuspop").val(publicAccountModel.status);

                $("#namepop").attr("pid", pid);
                $("#originalId").attr("pid", pid);
                $("#weixinpop").attr("pid", pid);
            }

        }
    }, null);
    popupModify("modify_Public", "#?w=800");

}

PublicAccount.clearForm = function () {
    $('.mask-div600 h3').prop('innerHTML', '新增公众号');
    $(".error").remove();
    RadioCheck('#weixintype1');
    RadioCheck("#weixinpay1");
    $("#form1")[0].reset();
}
