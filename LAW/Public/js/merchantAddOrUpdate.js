/**
 * Created with JetBrains PhpStorm.
 * User: qianxuefeng
 * Date: 13-10-31
 * Time: 下午2:21
 * To change this template use File | Settings | File Templates.
 */

$(function () {
        $("#beginDateInputpop").datepicker({"dateFormat": "yy-mm-dd"});
        $("#endDateInputpop").datepicker({"dateFormat": "yy-mm-dd"});
        $('form.required-form').simpleValidate({
            ajaxRequest: true,
            completeCallback: function ($el) {
                var formData = $el;
                //alert("kdsjfkd");
                //Do AJAX request with formData variable

                var name = $("#namepop").val();
                var beginDate = $("#beginDateInputpop").val();
                var endDate = $("#endDateInputpop").val();
                var status = $("#statuspop").val();
                var qq = $("#qqpop").val();
                var mobile = $("#mobilepop").val();
                var password = $("#passwordpop").val();
                var email = $("#emailpop").val();
                var systemAccountId = distributorId;
                $(this).val("正在提交...").attr('disabled', 'disabled');
                ConsumeObject('/merchant/ajaxUpdateOrSave',
                    {
                        status: status,
                        name: name,
                        beginDate: beginDate,
                        endDate: endDate,
                        qq: qq,
                        email: email,
                        password: password,
                        mobile: mobile,
                        distributorId: distributorId,
                        merchantId: merchantId

                    },
                    function (r) {
                        if (r.errcode == 0) {
                            var o = r.i;
                            popupclose();

                        }
                        else {


                        }
                    }, formData);
            }
        });


    }
);
