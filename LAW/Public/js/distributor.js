/**
 * Created with JetBrains PhpStorm.
 * User: qianxuefeng
 * Date: 13-10-31
 * Time: 下午2:21
 * To change this template use File | Settings | File Templates.
 */

$(function () {

        distributor.query(1);
        $("#search_navigation").hide();
        $(".queryTrigger").click(function () {
            if( $("#endDate").val()< $("#beginDate").val())
            {
                alert("开始时间不能晚于结束时间");
                return;
            }

            distributor.query(1);
        });


        $("#new_distributor").click(function () {
            distributor.clearForm();
            var popID = "modify_distributor"; //Get Popup Name
            var popURL = "#?w=700"; //Get Popup href to define size
            popupModify(popID, popURL);
            $('form.required-form').simpleValidate({
                ajaxRequest: true,
                completeCallback: function ($el) {
                    var formData = $el;
                    //alert("kdsjfkd");
                    //Do AJAX request with formData variable

                    var name = $("#namepop").val();
                    var endDate = $("#endDateInputpop").val();
                    var status = $("#statuspop").val();
                    var qq = $("#qqpop").val();
                    var mobile = $("#mobilepop").val();
                    var password = $("#passwordpop").val();
                    var email = $("#emailpop").val();

                    $(this).val("正在提交...").attr('disabled', 'disabled');
                    ConsumeObject('/distributor/ajaxUpdateOrSave',
                        {
                            status: status,
                            name: name,
                            endDate: endDate,
                            qq: qq,
                            email: email,
                            password: password,
                            mobile: mobile,
                            distributorId: distributorID


                        },
                        function (r) {
                            if (r.errcode == 0) {
                                var o = r.i;
                                popupclose();
                                distributor.query(1);

                            }
                            else {


                            }
                        }, formData);
                }
            });
            return false;
        });

        $("#cancelButton").click(function () {
            //default inputs
            popupclose();
        });
        if (location.search == '?add') $("#newdistributor").click();
    }
);

distributor = {};
distributor.total = 0;
distributor.pageSize = global.pagesize;
distributor.refreshCallback = function () {
    $(".checkAll").click(function () {
        var oldValue = $(".rowCheckbox").prop('checked');
        $(".rowCheckbox").prop('checked', !oldValue);
        $(".checkAll").prop('checked', !oldValue);
    });
    $(".modifyHack").click(
        function () {
            // alert(1);
            //var id = $(this).attr("distributorID");
            // $(location).attr('href', '/distributor/modify/?id=' + id);
            distributor.modify(this);
            $('form.required-form').simpleValidate({
                ajaxRequest: true,
                isModify:true,
                completeCallback: function ($el) {
                    var formData = $el;
                    //alert("kdsjfkd");
                    //Do AJAX request with formData variable

                    var name = $("#namepop").val();
                    var endDate = $("#endDateInputpop").val();
                    var status = $("#statuspop").val();
                    var qq = $("#qqpop").val();
                    var mobile = $("#mobilepop").val();
                    var password = $("#passwordpop").val();
                    var email = $("#emailpop").val();

                    $(this).val("正在提交...").attr('disabled', 'disabled');
                    ConsumeObject('/distributor/ajaxUpdateOrSave',
                        {
                            status: status,
                            name: name,
                            endDate: endDate,
                            qq: qq,
                            email: email,
                            password: password,
                            mobile: mobile,
                            distributorId: distributorID


                        },
                        function (r) {
                            if (r.errcode == 0) {
                                var o = r.i;
                                popupclose();
                                distributor.query(1);

                            }
                            else {


                            }
                        }, formData);
                }
            });
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
            distributor.del(this);

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
            var id = $(clicker).attr("distributorID");
            ConsumeObject('/distributor/ajaxUpdateStatus', {id: id, status: status}, function (r) {
                if (r.errcode == 0) {
                    distributor.query(1);
                }
            }, null);

        }
        });
    }

    $(".managementHack").click(
        function () {
            // alert(1);
            var id = $(this).attr("distributorID");

        }
    );

    $("#batchOpen").click(function () {
        distributor.batchUpdateStatus(this, 1);
    });
    $("#batchClose").click(function () {
        distributor.batchUpdateStatus(this, 2);
    });
    $("#batchDelete").click(function () {
        distributor.batchDelete(this);
    });


}
distributor.batchUpdateStatus = function (invoker, status) {
    var list = getAllCheck();
    if (list != "") {
        $(invoker).text("正在处理...");
        ConsumeObject('/distributor/batchUpdateStatus/', {ids: list, status: status}, function (r) {
            if (r.errcode == 0) {
                var invoker = r.i;
                invoker.text("设置成功");
                distributor.query(1);
            }
        }, $(invoker));

    }
}
distributor.del = function (invoker) {
    var s = "您真的要删除吗？"
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        // alert(1);

        ConsumeObject('/distributor/del/', {id: $(invoker).attr("distributorID")}, function (r) {
            if (r.errcode == 0) {
                distributor.query(1);
            }
        }, null);

    }
    });
}

distributor.batchDelete = function (invoker) {
    var s = "您真的要删除吗？"
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        var list = getAllCheck();
        if (list != "") {
            $(invoker).text("正在处理...");
            ConsumeObject('/distributor/batchDel/', {ids: list}, function (r) {
                if (r.errcode == 0) {
                    var invoker = r.i;
                    distributor.query(1);
                }
            }, $(invoker));

        }

    }
    });
}
distributor.query = function (currentPage) {

    var queryParameter = {};
    queryParameter.name = $("#name").val();
    queryParameter.rows = distributor.pageSize;
    queryParameter.page = currentPage;
    ConsumeObject('/distributor/ajaxGet', queryParameter, function (r) {
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
                    '<tr class="'+(index++ % 2 == 1 ? 'odd' : '')+' rows " id="Rows_'+index+'"> ' +
                       '<td>' + rows[row].name + '</td> \
                        <td>' + rows[row].endDate.substring(0, 10) + '</td>\
                        <td>' + (rows[row].status == 1 ? "启用" : "关闭") + '</td>\
                        <td>\
                            <span class="actionMenu">\
                                <a href="javascript:void(0)" distributorID="' + rows[row].id + '" class="modifyHack">修改</a>\
                               ' + (rows[row].status == 1 ? ' <a href="javascript:void(0)"  distributorID="' + rows[row].id + '" class="closeHack">关闭</a>' : '<a href="javascript:void(0)"  distributorID="' + rows[row].id + '" class="openHack">开启</a>') +
                                '<a href="javascript:void(0)" distributorID="' + rows[row].id + '" '+(hasOpenItem?'disabled="disabled" style="color:gray"':'class="deleteHack"')+'>删除</a>'+
                            '</span>\
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

            distributor.total = total;

            $(".list_bottom").pagination(
                {total_count: distributor.total,
                    page_size: distributor.pageSize,
                    current_page: currentPage,
                    callback: function (currentPage, invoker) {
                        distributor.query(currentPage);
                    },
                    page_toolbar:''
                });

            distributor.refreshCallback();
            $('input[id^=Check_]').each(function(){
                $(this).unbind('click').click(function(){$(this).prop('checked', !$(this).prop('checked'));});
            });
            if($("#name").val()!=''){
            $("#search_navigation").show().find("span").text( $("#name").val()).css({'color':'red'}).unbind('click').click(
                function()
                {
                    $("#name").val('');
                    distributor.query(1);
                    $("#search_navigation").hide();
                }
            );
            }
        }
        else {

        }
        //console.log(r);


    });
}
var distributorID=0;
distributor.modify = function (invoker) {
    distributor.clearForm();
     distributorID = $(invoker).attr('distributorID');
    $('.mask-div600 h3').prop('innerHTML', '代理商修改');
    //ajax get value

    ConsumeObject('/distributor/ajaxGetOne', {distributorID: distributorID}, function (r) {
        if (r.errcode == 0) {

            distributorModel = r.data;
            if (!(typeof distributorModel === 'undefined')) {
                $("#namepop").val(distributorModel.name).attr("did", distributorModel.id);
                $("#endDateInputpop").val(distributorModel.endDate.substring(0, 10));
                $("#qqpop").val(distributorModel.qq);
                $("#mobilepop").val(distributorModel.mobile);
                $("#statuspop").val(distributorModel.status);
                $("#passwordpop").parent().append("<em>如不修改，留空</em>");
                //$("#passwordpop2").val(distributorModel.password);
                $("#emailpop").val(distributorModel.email).attr("did", distributorModel.id);


            }


        }
    }, null);
    popupModify("modify_distributor", "#?w=700");

}

distributor.clearForm = function () {
    $('.mask-div600 h3').prop('innerHTML', '新增代理商');
    $("#form1")[0].reset();
    $("#passwordpop").parent().find("em").remove();
    $("#form1").find("strong").remove();
    distributorID=0;
}



$(function () {
        $("#beginDateInputpop").datepicker({"dateFormat": "yy-mm-dd"});
        $("#endDateInputpop").datepicker({"dateFormat": "yy-mm-dd"});



    }
);