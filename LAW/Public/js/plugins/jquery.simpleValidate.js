/*
 * Simple jQuery Form Validation Plugin
 * http://github.com/davist11/jQuery-Simple-Validate
 *
 * Copyright (c) 2010 Trevor Davis (http://trevordavis.net)
 * Dual licensed under the MIT and GPL licenses.
 * Uses the same license as jQuery, see:
 * http://jquery.org/license
 *http://trevordavis.net/blog/jquery-simple-validation-plugin
 * @version 0.3
 *
 * Example usage:
 * $('form.required-form').simpleValidate({
 *	 errorClass: 'error',
 *	 errorText: '{label} is a required field.',
 *	 emailErrorText: 'Please enter a valid {label}',
 *	 errorElement: 'strong',
 *	 removeLabelChar: '*',
 *	 inputErrorClass: '',
 *	 completeCallback: '',
 *	 ajaxRequest: false
 * });
 */
;
(function ($, window, document, undefined) {

    // our plugin constructor
    var SimpleValidate = function (elem, options) {
        this.elem = elem;
        this.$elem = $(elem);
        this.options = options;
        this.metadata = this.$elem.data('plugin-options');
        this.$requiredInputs = this.$elem.find(':input.required');
    };

    // the plugin prototype
    SimpleValidate.prototype = {
        defaults: {
            errorClass: 'error',
            errorText: '{label} 必须添加哦.',
            emailErrorText: '请输入正确的格式 {label}',
            errorElement: 'strong',
            removeLabelChar: '：',
            inputErrorClass: 'input-error',
            completeCallback: '',
            ajaxRequest: false,
            isModify:false
        },

        init: function () {
            var self = this;

            // Introduce defaults that can be extended either
            // globally or using an object literal.
            self.config = $.extend({}, self.defaults, self.options, self.metadata);

            // What type of error message is it
            self.errorMsgType = self.config.errorText.search(/{label}/);
            self.emailErrorMsgType = self.config.emailErrorText.search(/{label}/);

            self.$elem.on('submit.simpleValidate', $.proxy(self.handleSubmit, self));

            return this;
        },

        checkField: function (index) {
            var self = this;
            var $field = self.$requiredInputs.eq(index);
            var fieldValue = $.trim($field.val());
            var labelText = $field.parent().siblings('label').text().replace(self.config.removeLabelChar, '');
            var errorMsg = '';

            //Check if it's empty or an invalid email and format the error message
            if (fieldValue === '') {
                if(self.config.isModify)
                {
                    if($field.hasClass('compare_password')||$field.hasClass('pass'))
                    {


                    }
                    else
                    {
                        errorMsg = self.formatErrorMsg(self.config.errorText, labelText, self.errorMsgType);
                        self.hasError = true;
                    }
                }
                else
                {
                    errorMsg = self.formatErrorMsg(self.config.errorText, labelText, self.errorMsgType);
                    self.hasError = true;
                }
            } else if ($field.hasClass('email')) {
                if (!(/^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/.test(fieldValue.toLowerCase()))) {
                    errorMsg = self.formatErrorMsg(self.config.emailErrorText, labelText, 1);
                    self.hasError = true;
                }
            }
            else if ($field.hasClass('wexinname')) {
                if (ValidLength(fieldValue,16,2) ){

                }
                else {

                    errorMsg = self.formatErrorMsg("名称2-16个字符！{label}", labelText, 1);
                    self.hasError = true;
                }
                $.ajax({
                    type: 'POST',
                    url: '/publicAccount/checkName',
                    data: {weixinName: fieldValue, id: $field.attr("pid")},
                    success: function (d) {
                        if (d.errcode == 0) {
                            if (d.data == true) {
                                errorMsg = self.formatErrorMsg("此名称已经存在啦！{label}", labelText, 1);
                                self.hasError = true;
                            }
                        }
                    },
                    dataType: 'json',
                    async: false
                });
            }
            else if ($field.hasClass("originalId")) {
                $.ajax({
                    type: 'POST',
                    url: '/publicAccount/checkOriginalId',
                    data: {OriginalId: fieldValue, id: $field.attr("pid")},
                    success: function (d) {
                        if (d.errcode == 0) {
                            if (d.data == true) {
                                errorMsg = self.formatErrorMsg("该公众号原始ID已存在！{label}", labelText, 1);
                                self.hasError = true;
                            }
                        }
                    },
                    dataType: 'json',
                    async: false
                });
            }
            else if ($field.hasClass("weixin")) {

                if (ValidLength(fieldValue,16,2) ){

                }
                else {

                    errorMsg = self.formatErrorMsg("名称2-16个字符！{label}", labelText, 1);
                    self.hasError = true;
                }
                $.ajax({
                    type: 'POST',
                    url: '/publicAccount/checkWeixinId',
                    data: {weixin: fieldValue, id: $field.attr("pid")},
                    success: function (d) {
                        if (d.errcode == 0) {
                            if (d.data == true) {
                                errorMsg = self.formatErrorMsg("该微信号已存在！{label}", labelText, 1);
                                self.hasError = true;
                            }
                        }
                    },
                    dataType: 'json',
                    async: false
                });
            }
            else if ($field.hasClass("merchantEmail")) {
                if (!(/^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/.test(fieldValue.toLowerCase()))) {
                    errorMsg = self.formatErrorMsg(self.config.emailErrorText, labelText, 1);
                    self.hasError = true;
                }
                else {
                    $.ajax({
                        type: 'POST',
                        url: '/merchant/checkEmail/',
                        data: {email: fieldValue, mid: $field.attr("mid")},
                        success: function (d) {
                            if (d.errcode == 0) {
                                if (d.data == true) {
                                    errorMsg = self.formatErrorMsg("该邮箱已存在！", labelText, 1);
                                    self.hasError = true;
                                }
                            }
                        },
                        dataType: 'json',
                        async: false
                    });
                }
            }
            else if ($field.hasClass("merchantName")) {
                $.ajax({
                    type: 'POST',
                    url: '/merchant/checkName/',
                    data: {name: fieldValue, mid: $field.attr("mid")},
                    success: function (d) {
                        if (d.errcode == 0) {
                            if (d.data == true) {
                                errorMsg = self.formatErrorMsg("该名称已存在！", labelText, 1);
                                self.hasError = true;
                            }
                        }
                    },
                    dataType: 'json',
                    async: false
                });
            }
            else if ($field.hasClass("compare_password")) {
                var firstPassword = $("#" + $field.attr("for")).val();
                if(self.config.isModify)
                {
                    if(fieldValue==''&&firstPassword=='')
                    {

                    }
                    else
                    {
                        if (fieldValue != firstPassword) {
                            errorMsg = self.formatErrorMsg("两次密码要输入一致", labelText, 1);
                            self.hasError = true;
                        }
                    }
                }
                else if (fieldValue == firstPassword) {

                }
                else {
                    errorMsg = self.formatErrorMsg("两次密码要输入一致", labelText, 1);
                    self.hasError = true;
                }
            }

            else if ($field.hasClass("compare_date")) {
                var begindate = $("#" + $field.attr("compareto")).val();

                var date1 = new Date(fieldValue);
                var date2 = new Date(begindate);
                if (date1 <= date2) {
                    errorMsg = self.formatErrorMsg("结束时间须大于开始时间", labelText, 1);
                    self.hasError = true;
                }
                else {

                }
            }
            else if ($field.hasClass("mobile")) {
                var firstPassword = $("#" + $field.attr("for")).val();
                if (/^[0-9]{11}$/.test(fieldValue.toLowerCase())) {

                }
                else {
                    errorMsg = "请输入正确的手机格式";
                    self.hasError = true;
                }
            }
            else if ($field.hasClass("qq")) {
                var firstPassword = $("#" + $field.attr("for")).val();
                if (/^[0-9]{4,}$/.test(fieldValue.toLowerCase())) {

                }
                else {
                    errorMsg = "请输入正确的QQ号";
                    self.hasError = true;
                }
            }

            //If there is an error, display it
            if (errorMsg !== '') {
                $field.addClass(self.config.inputErrorClass).after('<' + self.config.errorElement + ' style="color:red" class="' + self.config.errorClass + '">' + errorMsg + '</' + self.config.errorElement + '>');
            }
        },

        formatErrorMsg: function (errorText, labelText, errorMsgType) {
            return (errorMsgType > -1 ) ? errorText.replace('{label}', labelText) : errorText;
        },

        handleSubmit: function (e) {
            var self = this;

            // We are just starting, so there are no errors yet
            this.hasError = false;

            // Reset existing displayed errors
            self.$elem.find(self.config.errorElement + '.' + self.config.errorClass).remove();
            self.$elem.find(':input.' + self.config.inputErrorClass).removeClass(self.config.inputErrorClass);

            // Check each field
            self.$requiredInputs.each($.proxy(self.checkField, self));

            // Don't submit the form if there are errors
            if (self.hasError) {
                e.preventDefault();
            } else if (self.config.completeCallback !== '') { // If there is a callback
                self.config.completeCallback(self.$elem);

                // If AJAX request
                if (self.config.ajaxRequest) {
                    e.preventDefault();
                }
            }
        }
    };

    SimpleValidate.defaults = SimpleValidate.prototype.defaults;
    var methods = {
        init : function(options) {

        },
        changeModify : function(v ) {
            console.log(this);
           // this.config.isModify=v;
            console.log(  this.config);
            return this;
        },// IS
        hide : function( ) {  },// GOOD
        update : function( content ) {  }// !!!
    };
    $.fn.simpleValidate = function (options) {
        if(methods[options])
        {
            return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        }
        else
        {
            return this.each(function () {
                       new SimpleValidate(this, options).init();
            });
        }
    };

})(jQuery, window, document);