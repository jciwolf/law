/**
 * Created with JetBrains PhpStorm.
 * User: qianxuefeng
 * Date: 13-10-31
 * Time: 下午2:21
 * To change this template use File | Settings | File Templates.
 */

$(function () {
        $("#search_navigation").hide();
        var futureDate = new Date();
        futureDate.setDate(futureDate.getDate() + 365);
        $("#endDate").val($.datepicker.formatDate('yy-mm-dd', futureDate));
        operator.query(1);
        $(".queryTrigger").click(function () {
            if( $("#endDate").val()< $("#beginDate").val())
            {
                alert("开始时间不能晚于结束时间");
                return;
            }

            operator.query(1);
        });

        $("#beginDate").datepicker({"dateFormat": "yy-mm-dd"});
        $("#endDate").datepicker({"dateFormat": "yy-mm-dd"});

        $("#newoperator").click(function () {
            operator.clearForm();

            var popID = "modify_operator"; //Get Popup Name
            var popURL = "#?w=700"; //Get Popup href to define size
            popupModify(popID, popURL);
            $('form.required-form').simpleValidate({
                ajaxRequest: true,
                completeCallback: function ($el) {
                    var formData = $el;
                    //alert("kdsjfkd");
                    //Do AJAX request with formData variable

                    var name = $("#namepop").val();
                    var status = $("#statuspop").val();
                    var qq = $("#qqpop").val();
                    var mobile = $("#mobilepop").val();
                    var password = $("#passwordpop").val();
                    var email = $("#emailpop").val();

                    $(this).val("正在提交...").attr('disabled', 'disabled');
                    ConsumeObject('/operator/ajaxUpdateOrSave',
                        {
                            status: status,
                            name: name,
                            qq: qq,
                            email: email,
                            password: password,
                            mobile: mobile,
                            operatorId: operatorID,
                            distributorId:$("#distributorpop").val()


                        },
                        function (r) {
                            if (r.errcode == 0) {
                                var o = r.i;
                                popupclose();
                                operator.query(1);
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
        if (location.search == '?add') $("#newoperator").click();
    }
);

operator = {};
operator.total = 0;
operator.pageSize = global.pagesize;
operator.refreshCallback = function () {
    $(".checkAll").click(function () {
        var oldValue = $(".rowCheckbox").prop('checked');
        $(".rowCheckbox").prop('checked', !oldValue);
        $(".checkAll").prop('checked', !oldValue);
    });
    $(".modifyHack").click(
        function () {
            // alert(1);
            //var id = $(this).attr("operatorID");
            // $(location).attr('href', '/operator/modify/?id=' + id);
            operator.modify(this);
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
            operator.del(this);

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
            var id = $(clicker).attr("operatorID");
            ConsumeObject('/operator/ajaxUpdateStatus', {id: id, status: status}, function (r) {
                if (r.errcode == 0) {
                    operator.query(1);
                }
            }, null);

        }
        });
    }

    $(".managementHack").click(
        function () {
            // alert(1);
            var id = $(this).attr("operatorID");

        }
    );

    $("#batchOpen").click(function () {
        operator.batchUpdateStatus(this, 1);
    });
    $("#batchClose").click(function () {
        operator.batchUpdateStatus(this, 2);
    });
    $("#batchDelete").click(function () {
        operator.batchDelete(this);
    });


}
operator.batchUpdateStatus = function (invoker, status) {
    var list = getAllCheck();
    if (list != "") {
        $(invoker).text("正在处理...");
        ConsumeObject('/operator/batchUpdateStatus/', {ids: list, status: status}, function (r) {
            if (r.errcode == 0) {
                var invoker = r.i;
                invoker.text("设置成功");
                operator.query(1);
            }
        }, $(invoker));

    }
}
operator.del = function (invoker) {
    var s = "您真的要删除吗？"
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        // alert(1);

        ConsumeObject('/operator/del/', {id: $(invoker).attr("operatorID")}, function (r) {
            if (r.errcode == 0) {
                operator.query(1);
            }
        }, null);

    }
    });
}

operator.batchDelete = function (invoker) {
    var s = "您真的要删除吗？"
    $("body").xpopup({title: s, message: s, sure_callback: function () {
        var list = getAllCheck();
        if (list != "") {
            $(invoker).text("正在处理...");
            ConsumeObject('/operator/batchDel/', {ids: list}, function (r) {
                if (r.errcode == 0) {
                    var invoker = r.i;
                    operator.query(1);
                }
            }, $(invoker));

        }

    }
    });
}
operator.query = function (currentPage) {

    var queryParameter = {};
    queryParameter.email = $("#search_email").val();
    queryParameter.rows = operator.pageSize;
    queryParameter.page = currentPage;
    ConsumeObject('/operator/ajaxGet', queryParameter, function (r) {
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
                        <td>' + rows[row].email + '</td>\
                        <td>' + (rows[row].status == 1 ? "启用" : "关闭") + '</td>\
                        <td>\
                            <span class="actionMenu">\
                                <a href="javascript:void(0)" operatorID="' + rows[row].id + '" class="modifyHack">修改</a>\
                               ' + (rows[row].status == 1 ? ' <a href="javascript:void(0)"  operatorID="' + rows[row].id + '" class="closeHack">关闭</a>' : '<a href="javascript:void(0)"  operatorID="' + rows[row].id + '" class="openHack">开启</a>') +
                                '<a href="javascript:void(0)" operatorID="' + rows[row].id + '" '+(hasOpenItem?'disabled="disabled" style="color:gray"':'class="deleteHack"')+'>删除</a>'+
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

            operator.total = total;

            $(".list_bottom").pagination(
                {total_count: operator.total,
                    page_size: operator.pageSize,
                    current_page: currentPage,
                    callback: function (currentPage, invoker) {
                        operator.query(currentPage);
                    },
                    page_toolbar:''
                });

            operator.refreshCallback();
            $('input[id^=Check_]').each(function(){
                $(this).unbind('click').click(function(){$(this).prop('checked', !$(this).prop('checked'));});
            });
            if($("#search_email").val()!=''){
                $("#search_navigation").show().find("span").text( $("#search_email").val()).css({'color':'red'}).unbind('click').click(
                    function()
                    {
                        $("#search_email").val('');
                        operator.query(1);
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
var operatorID=0;
operator.modify = function (invoker) {
    operator.clearForm();
     operatorID = $(invoker).attr('operatorID');
    $('.mask-div600 h3').prop('innerHTML', '操作员修改');
    //ajax get value
     //

    $('form.required-form').simpleValidate({
        ajaxRequest: true,
        isModify:true,
        completeCallback: function ($el) {
            var formData = $el;
            //alert("kdsjfkd");
            //Do AJAX request with formData variable

            var name = $("#namepop").val();
            var status = $("#statuspop").val();
            var qq = $("#qqpop").val();
            var mobile = $("#mobilepop").val();
            var password = $("#passwordpop").val();
            var email = $("#emailpop").val();

            $(this).val("正在提交...").attr('disabled', 'disabled');
            ConsumeObject('/operator/ajaxUpdateOrSave',
                {
                    status: status,
                    name: name,
                    qq: qq,
                    email: email,
                    password: password,
                    mobile: mobile,
                    operatorId: operatorID,
                    distributorId:$("#distributorpop").val()


                },
                function (r) {
                    if (r.errcode == 0) {
                        var o = r.i;
                        popupclose();
                        operator.query(1);

                    }
                    else {


                    }
                }, formData);
        }
    });

    ConsumeObject('/operator/ajaxGetOne', {operatorID: operatorID}, function (r) {
        if (r.errcode == 0) {

            operatorModel = r.data;
            if (!(typeof operatorModel === 'undefined')) {
                $("#namepop").val(operatorModel.name).attr("oid", operatorModel.id);
                $("#distributorpop").val(operatorModel.parentId);
                $("#qqpop").val(operatorModel.qq);
                $("#mobilepop").val(operatorModel.mobile);
                $("#statuspop").val(operatorModel.status);
                $("#passwordpop").parent().append("<em>如不修改，留空</em>");
                //$("#passwordpop2").val(operatorModel.password);
                $("#emailpop").val(operatorModel.email).attr("oid", operatorModel.id);


            }


        }
    }, null);
    popupModify("modify_operator", "#?w=700");

}

operator.clearForm = function () {
    $('.mask-div600 h3').prop('innerHTML', '新增操作员');
    $("#passwordpop").parent().find("em").remove();
    $("#form1")[0].reset();
    $("#form1").find("strong").remove();
    operatorID=0;

}



$(function () {




    }
);