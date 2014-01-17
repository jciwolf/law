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
        Merchant.query(1);
        $(".queryTrigger").click(function () {
            if( $("#endDate").val()< $("#beginDate").val())
            {
                alert("开始时间不能晚于结束时间");
                return;
            }

            Merchant.query(1);
        });

        $("#beginDate").datepicker({"dateFormat": "yy-mm-dd"});
        $("#endDate").datepicker({"dateFormat": "yy-mm-dd"});

        $("#newMerchant").click(function () {
            Merchant.clearForm();
            var popID = "modify_Merchant"; //Get Popup Name
            var popURL = "#?w=700"; //Get Popup href to define size
            popupModify(popID, popURL);
            return false;
        });

        $("#cancelButton").click(function () {
            //default inputs
            popupclose();
        });
        if (location.search == '?add') $("#newMerchant").click();
    }
);

Merchant = {};
Merchant.total = 0;
Merchant.pageSize = global.pagesize;
Merchant.refreshCallback = function () {
    $(".checkAll").click(function () {
        var oldValue = $(".rowCheckbox").prop('checked');
        $(".rowCheckbox").prop('checked', !oldValue);
        $(".checkAll").prop('checked', !oldValue);
    });
    $(".modifyHack").click(
        function () {
            // alert(1);
            //var id = $(this).attr("merchantID");
            // $(location).attr('href', '/merchant/modify/?id=' + id);
            Merchant.modify(this);
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
    $(".deleteHack").click(
        function () {
            Merchant.del(this);

        }
    );
    function updateStatus(status, clicker) {
        var s = "";
        if (status == 2) {
            s = "您真的要关闭吗？"
        }
        else
            s = "您真的要开启吗？"
        $("body").xpopup({title: s, message: s, sure_callback: function () {
            // alert(1);
            var id = $(clicker).attr("merchantID");
            ConsumeObject('/merchant/ajaxUpdateStatus', {id: id, status: status}, function (r) {
                if (r.errcode == 0) {
                    Merchant.query(1);
                }
            }, null);

        }
        });
    }

    $(".managementHack").click(
        function () {
            // alert(1);
            var id = $(this).attr("merchantID");

        }
    );

    $("#batchOpen").click(function () {
        Merchant.batchUpdateStatus(this, 1);
    });
    $("#batchClose").click(function () {
        Merchant.batchUpdateStatus(this, 2);
    });
    $("#batchDelete").click(function () {
        Merchant.batchDelete(this);
    });


}
Merchant.batchUpdateStatus = function (invoker, status) {
    var list = getAllCheck();
    if (list != "") {
        $(invoker).text("正在处理...");
        ConsumeObject('/merchant/batchUpdateStatus/', {ids: list, status: status}, function (r) {
            if (r.errcode == 0) {
                var invoker = r.i;
                invoker.text("设置成功");
                Merchant.query(1);
            }
        }, $(invoker));

    }
}
Merchant.del = function (invoker) {
    var s = "您真的要删除吗？"
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        // alert(1);

        ConsumeObject('/merchant/del/', {id: $(invoker).attr("merchantID")}, function (r) {
            if (r.errcode == 0) {
                Merchant.query(1);
            }
        }, null);

    }
    });
}

Merchant.batchDelete = function (invoker) {
    var s = "您真的要删除吗？"
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        var list = getAllCheck();
        if (list != "") {
            $(invoker).text("正在处理...");
            ConsumeObject('/merchant/batchDel/', {ids: list}, function (r) {
                if (r.errcode == 0) {
                    var invoker = r.i;
                    Merchant.query(1);
                }
            }, $(invoker));

        }

    }
    });
}
Merchant.query = function (currentPage) {

    var queryParameter = {};
    queryParameter.name = $("#name").val();
    queryParameter.distributor = $("#distributor").val();
    queryParameter.beginDate = $("#beginDate").val();
    queryParameter.endDate = $("#endDate").val();
    queryParameter.status = $("#status").val();
    queryParameter.rows = Merchant.pageSize;
    queryParameter.page = currentPage;
    ConsumeObject('/merchant/ajaxGet', queryParameter, function (r) {
        if (r.errcode == 0) {
            r = r.data;
            var total = r.count;
            var rows = r.rows;
            var index=0;
            $(".rows").remove();
            $(".noresult").remove();
            if(total==0)
            {

                var tr = $('<tr/>').attr({ 'class': 'odd noresult' });
                $('.lasttr').before(tr).hide();
                $('<td/>').prop({ 'colspan': 8, 'innerHTML': "无搜索结果，请重新搜索！"}).appendTo(tr);
                return;
            }
            $('.lasttr').show();
            var hasOpenItem=false;
            for (row in rows) {
                if(rows[row].status==1){
                    hasOpenItem=true;
                }
                var tmpl =
                    '<tr class="'+(index++ % 2 == 1 ? 'odd' : '')+' rows " id="Rows_'+index+'"> <td>' +
                        '<label class="check-one"><input type="checkbox" id="Check_' + rows[row].id + '" class="rowCheckbox" value="' + rows[row].id + '" ></label>' +
                        '</td> \
                        <td>' + rows[row].name + '</td> \
                        <td>' + rows[row].distributor + '</td> \
                        <td>' + rows[row].beginDate.substring(0, 10) + '</td>\
                        <td>' + rows[row].endDate.substring(0, 10) + '</td>\
                        <td>' + (rows[row].status == 1 ? "启用" : "关闭") + '</td>\
                        <td>\
                            <span class="actionMenu">\
                                <a href="javascript:void(0)" merchantID="' + rows[row].id + '" class="modifyHack">修改</a>\
                               ' + (rows[row].status == 1 ? ' <a href="javascript:void(0)"  merchantID="' + rows[row].id + '" class="closeHack">关闭</a>' : '<a href="javascript:void(0)"  merchantID="' + rows[row].id + '" class="openHack">开启</a>') +
                                '<a href="javascript:void(0)" merchantID="' + rows[row].id + '" '+(hasOpenItem?'disabled="disabled" style="color:gray"':'class="deleteHack"')+'>删除</a>' +
                                '<a href="/publicAccount/index/?merchantId=' + rows[row].id + '"  merchantID="' + rows[row].id + '"  class="managementHack">商家公众号</a>\
                            </span>\
                        </td>\
                    </tr>';
                $("#header").after(tmpl);


            }
            $("tr[id^=Rows_]").each(function () {
                $(this).unbind('click').click(function () {
                        tableCheck('#Check_' + $($(this).find(".rowCheckbox")[0]).val());
                    })
                    .mouseenter(function () {
                        $(this).addClass("hover");
                    })
                    .mouseleave(function () {
                        $(this).removeClass("hover");
                    });
            });

            Merchant.total = total;

            $(".list_bottom").pagination(
                {total_count: Merchant.total,
                    page_size: Merchant.pageSize,
                    current_page: currentPage,
                    callback: function (currentPage, invoker) {
                        Merchant.query(currentPage);
                    },
                    page_toolbar: '<span class="actionMenu"><a href="javascript:void(0)" id="batchOpen">批量开启</a><a href="javascript:void(0)" id="batchClose">批量关闭</a>'+
                        ''//(hasOpenItem==true?'<a href="javascript:void(0)" disabled="disabled" style="color:gray" id="">批量删除</a>':'<a href="javascript:void(0)" id="batchDelete">批量删除</a>')
                         +'</span>'
                });

            Merchant.refreshCallback();
            $('input[id^=Check_]').each(function(){
                $(this).unbind('click').click(function(){$(this).prop('checked', !$(this).prop('checked'));});
            });
        }
        else {

        }
        //console.log(r);


    });
}

Merchant.modify = function (invoker) {
    Merchant.clearForm();
    var merchantID = $(invoker).attr('merchantID');
    $('.mask-div600 h3').prop('innerHTML', '商家修改');
    //ajax get value

    ConsumeObject('/merchant/ajaxGetOne', {merchantID: merchantID}, function (r) {
        if (r.errcode == 0) {

            merchantModel = r.data;
            if (!(typeof merchantModel === 'undefined')) {
                $("#namepop").val(merchantModel.name).attr("mid", merchantModel.id);
                $("#beginDateInputpop").val(merchantModel.beginDate.substring(0, 10));
                $("#endDateInputpop").val(merchantModel.endDate.substring(0, 10));
                $("#qqpop").val(merchantModel.qq);
                $("#mobilepop").val(merchantModel.mobile);
                $("#statuspop").val(merchantModel.status);
                $("#passwordpop").val(merchantModel.password);
                $("#passwordpop2").val(merchantModel.password);
                $("#emailpop").val(merchantModel.email).attr("mid", merchantModel.id);


            }


        }
    }, null);
    popupModify("modify_Merchant", "#?w=700");

}

Merchant.clearForm = function () {
    $('.mask-div600 h3').prop('innerHTML', '新增商家');
    $("#form1")[0].reset();
}
