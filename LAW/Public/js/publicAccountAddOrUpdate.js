/**
 * Created with JetBrains PhpStorm.
 * User: qianxuefeng
 * Date: 13-10-31
 * Time: 下午2:21
 * To change this template use File | Settings | File Templates.
 */

$(function () {
        if (typeof publicAccountModel == 'undefined') {
            RadioCheck('#weixintype1');
            RadioCheck("#weixinpay1");
            RadioCheck("#customerservice1");
        }
        $("#beginDateInputpop").datepicker({"dateFormat": "yy-mm-dd"});
        $("#endDateInputpop").datepicker({"dateFormat": "yy-mm-dd"});
        $("#cancelButton").click(function () {
            // $(location).attr('href', '/publicAccount/index');
        });
        $('form.required-form').simpleValidate({
            ajaxRequest: true,
            completeCallback: function ($el) {
                // var formData = $el.serialize();
                //alert("kdsjfkd");
                //Do AJAX request with formData variable
                var weixin_pay_type = RadioGetValue("weixinpay");//$("input[name=weixin_pay]:checked").val();
                var customer_service_type = RadioGetValue("customerservice");
                var name = $("#namepop").val();
                var beginDate = $("#beginDateInputpop").val();
                var endDate = $("#endDateInputpop").val();
                var originalId = $("#originalId").val();
                var weixin = $("#weixinpop").val();
                var url = "url";//todo::
                var token = $("#token").val();
                var appId = $("#AppId").val();
                var appSecret = $("#AppSecret").val();
                var type = RadioGetValue("weixintype");//$("input[name=wexin_type]:checked").val();
                var status = $("#statuspop").val();
                var systemAccountId = publicAccount_systemId;
                $(this).val("正在提交...").attr('disabled', 'disabled');
                ConsumeObject('/publicAccount/ajaxUpdateOrSave',
                    {   id: pid,
                        weixinPay: weixin_pay_type,
                        customerService:customer_service_type,
                        status: status,
                        name: name,
                        beginDate: beginDate,
                        endDate: endDate,
                        originalId: originalId,
                        weixin: weixin,
                        url: url,
                        token: token,
                        appId: appId,
                        appSecret: appSecret,
                        type: type,
                        systemAccountId: systemAccountId

                    },
                    function (r) {
                        if (r.errcode == 0) {
                            //$(this).html("添加成功，正在跳转...");
                            //$(location).attr('href', '/publicAccount/index');
                            PublicAccount.query(1);
                            popupclose();

                        }
                        else {

                        }
                    }, null);
            }
        });


    }
);
