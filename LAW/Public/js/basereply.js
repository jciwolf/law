/**
 * Created with JetBrains PhpStorm.
 * User: qianxuefeng
 * Date: 13-10-31
 * Time: 下午2:21
 * To change this template use File | Settings | File Templates.
 */
var editor;
$(function () {
        //test
        //getReply();

        //updateReply();
       // editor = new RBMEditor.Editor('oDiv');
        baseReply.initDisplay();
        baseReply.init();


        CKEDITOR.replace( 'textContentTextarea', {
            fullPage: true,
            allowedContent: true
        });

        $("#replayType").change(function () {

            var selectedType = $(this).val();
            if (selectedType == 1) {
                $("#textEdit").show();
                $(".ulrecord").hide();
                $("#saveButton").parent().show();
                $("#newsAdd").hide();
                $("#newsEdit").hide();
                $("#videoEdit").hide();
                $("#voiceEdit").hide();
            }
            else if(selectedType == 2) {
                $("#textEdit").hide();
                $("#edit").show();
                $("#newsAdd").show();
                $(".ulrecord").show();
                $("#videoEdit").hide();
                $("#voiceEdit").hide();
                //$("#newsEdit").show()
            }
            else if(selectedType == 3) {
                $("#textEdit").hide();
                $("#edit").show();
                $("#voiceEdit").show();
                $("#videoEdit").hide();
                $("#newsAdd").hide();
                $(".ulrecord").hide();

            }
            else if(selectedType == 4) {
                $("#textEdit").hide();
                $("#edit").show();
                $("#videoEdit").show();
                $("#voiceEdit").hide();
                $("#newsAdd").hide();
                $(".ulrecord").hide();

            }

        });


        $('#image_upload').uploadify({
            height: 30,
            swf: '/css/uploadify.swf',
            uploader: '/baseReply/uploader',
            'fileSizeLimit': '1024KB',
            'fileTypeDesc': 'Image Files',
            //  'formData': {'someKey': 'someValue', 'BannerType': $("#addBannerType").val()},
            'fileTypeExts': '*.jpg',
            width: 120,
            'buttonText': '请选择...',
            onUploadSuccess: function (file, data, response) {
                //console.log("uploader return data is " + data);
                if (data == "Invalid Size") {
                    $.messager.alert("", "图片不适合要求，请重新上传！");
                }
                else {
                    // $("#image_upload_preview").attr("src",mediaHost+data).show();
                    $("#image_upload_hidden").val(data);

                    //$("#PreImg").text(mediaHost + '/' + $("#image_upload_hidden").val());
                    $("#PreImg").prop('innerHTML', '<img src="' + mediaHost + $("#image_upload_hidden").val() + '"/>');


                }
            },
            'onUploadStart': function (file) {
                // alert($("#addBannerType").val());
                $("#image_upload").uploadify("settings", "formData", {'BannerType': $("#addBannerType").val()});
            }

        });
        $('#voice_upload').uploadify({
            height: 30,
            swf: '/css/uploadify.swf',
            uploader: '/baseReply/uploader',
            'fileSizeLimit': '256KB',
            'fileTypeDesc': 'Image Files',
            'fileTypeExts': '*.arm;*.mp3',
            width: 120,
            'buttonText': '请选择...',
            onUploadSuccess: function (file, data, response) {
                //console.log("uploader return data is " + data);
                if (data == "false") {
                    alert("上传失败！")
                }
                else {
                    $("#voice_upload_hidden").val(data);
                    $("#PreVoice").empty().prop('innerHTML', '<audio src="' + mediaHost + $("#voice_upload_hidden").val() +'" controls="controls"></audio>');


                }
            }

        });
        $('#video_upload').uploadify({
            height: 30,
            swf: '/css/uploadify.swf',
            uploader: '/baseReply/uploader',
            'fileSizeLimit': '1024KB',
            'fileTypeDesc': 'Image Files',
            //  'formData': {'someKey': 'someValue', 'BannerType': $("#addBannerType").val()},
            'fileTypeExts': '*.mp4',
            width: 120,
            'buttonText': '请选择...',
            onUploadSuccess: function (file, data, response) {
                //console.log("uploader return data is " + data);
                if (data == "false") {
                   alert("上传失败！")
                }
                else {
                    $("#video_upload_hidden").val(data);
                    $("#PreVideo").empty().prop('innerHTML', '<video src="' + mediaHost + $("#video_upload_hidden").val() +'" controls="controls"></video>');


                }
            }

        });
    }
);


baseReply = {};
function allHide() {
    $("#textView").hide();
    $("#edit").hide();
    $("#newsEdit").hide();
    $("#newsView").hide();
    $("#newsAdd").hide();
    $("#textEdit").hide();
    $("#saveButton").parent().hide();
    $("#addNewsButton").parent().hide();
    $("#editButton").parent().hide();
}

function allDisplay() {
    $("#textView").show();
    $("#edit").show();
    $("#newsEdit").show();
    $("#newsView").show();
    $("#newsAdd").show();
    $("#textEdit").show();
}
baseReply.initDisplay = function () {


    allHide();


    if (typeof  replyModel != 'undefined') {
        //console.log(replyModel);
        if (replyModel.type == 1) {
            $("#textContent").html($.parseJSON(replyModel.content).text);
            $("#textView").show();
            $("#editButton").parent().show();

        }
        else if (replyModel.type == 2) {
            $(".ulrecord").remove();
            $("#replayTypeDisplay").text("图文型");
            if (replyModel.content instanceof Array) {

            }
            else {
                replyModel.content = $.parseJSON(replyModel.content);
            }
            for (var i in replyModel.content) {
                var m = replyModel.content[i];
                baseReply.appendNewView(m, i);
            }
            $("#replayType").val(2);
            $("#newsAdd").show();
            $("#edit").show();
            $("#saveButton").parent().show();

        }
       else if (replyModel.type == 3) {
            $(".ulrecord").remove();
            $("#editButton").parent().show();
            $("#replayTypeDisplay").text("语音型");
            $("#replayType").val(3);
            $("#textEdit").show();
            $("#voiceEdit").show();
            $("#voice_upload_hidden").val(  $.parseJSON(replyModel.content).file);
            $("#PreVoice").empty().prop('innerHTML', '<audio src="' + mediaHost + $("#voice_upload_hidden").val() +'" controls="controls"></audio>');
            $("#saveButton").parent().hide();
            $("#span_voice_edit").hide();

        }
       else if (replyModel.type == 4) {
            $("#textContent").html($.parseJSON(replyModel.content).text);
            $("#textView").show();
            $(".ulrecord").remove();
            $("#editButton").parent().show();
            $("#replayTypeDisplay").text("视频型");
            $("#replayType").val(4);
            $("#textEdit").show();
            $("#videoEdit").show();
            $("#video_upload_hidden").val(  $.parseJSON(replyModel.content).file);
            $("#PreVideo").empty().prop('innerHTML', '<video src="' + mediaHost + $("#video_upload_hidden").val() +'" controls="controls"></video>');
            $("#saveButton").parent().hide();
            $("#span_video_edit").hide();

        }
        else {
            $("#edit").show();
            $("#textEdit").show();
            $("#editButton").parent().hide();
            $("#saveButton").parent().show();
            $("#replayType").val(1);
        }
    }

}
baseReply.appendNewView = function (m, i) {


    var tpl = '<ul id="newsView"  class="ulrecord" style="">\
                     <li><label for="">标题：</label><span>{0}</span></li>\
                     <li><label for="">描述：</label><span>{1}</span></li>\
                     <li><label for="">地址链接：</label><span>{2}</span></li>\
                     <li><label for="">上传图片：</label><span><img src="{3}" width="373" height="174"></span><span><input type="button" index="' + i + '" value="删除" class="gray_but newbuttonDel"></span></li>\
                     </ul>';

    $("#save_cancel_bar").before(String.format(tpl, m.Title, m.Description, m.Url, mediaHost + m.PicUrl));

    $(".newbuttonDel").click(function () {
        var index = $(this).attr('index');
        replyModel.content[index] = null;
        $(this).parents(".ulrecord").remove();
        $("#saveButton").parent().show();

    });


}
baseReply.init = function () {

    $("#editButton").click(function () {
        if (typeof  replyModel != 'undefined') {

            if (replyModel.type == 1) {
                allHide();
                $("#edit").show();
                $("#textEdit").show();
                //$("#textContentTextarea").val($.parseJSON(replyModel.content).text);
                CKEDITOR.instances.textContentTextarea.setData( $.parseJSON(replyModel.content).text);

                $(this).parent().hide();
                $("#saveButton").parent().show();

            }
            else if (replyModel.type == 2) {

            }
            else if (replyModel.type == 3) {
                $(this).parent().hide();
                $("#edit").show();
                $("#textEdit").hide();
                $("#saveButton").parent().show();
                $("#span_voice_edit").show();
                $("#textView").hide();
                $("#newsView").hide();

            }
            else if (replyModel.type == 4) {
                $(this).parent().hide();
                $("#edit").show();
                $("#textEdit").hide();
                $("#saveButton").parent().show();
                $("#span_video_edit").show();
                $("#textView").hide();
                $("#newsView").hide();

            }

        }
    });

    $("#saveButton").click(function () {

        replyModel.type = $("#replayType").val();
        var content = {};
        if (replyModel.type == 1) {

            // content.text = $("#textContentTextarea").val();
            content.text = CKEDITOR.instances.textContentTextarea.document.getBody().getHtml();

            if(!ValidLength(content.text,600,1))
            {

                $(this).val("文本内容超出长度限制！").css({'color': 'red'});
                var $l = $(this);
                setTimeout(function () {
                    $l.val("保　存").css({'color': "#fff"});
                }, 1000);
                return;

            }

        }
        else if(replyModel.type == 2) {

           if(!$.isArray(replyModel.content))
           {
               $(this).val("请添加图文信息后再保存！").css({'color': 'red'});
               var $l = $(this);
               setTimeout(function () {
                   $l.val("保　存").css({'color': "#fff"});
               }, 1000);
               return;
           }
            content = jQuery.grep(replyModel.content, function (n, i) {
                return (n !== "" && n != null);
            });

            if(content.length==0)
            {
                $(this).val("请添加图文信息后再保存！").css({'color': 'red'});
                var $l = $(this);
                setTimeout(function () {
                    $l.val("保　存").css({'color': "#fff"});
                }, 1000);
                return;


            }

        }
        else if(replyModel.type == 3)
        {
            content.file=$("#voice_upload_hidden").val();
            if(content.file=="")
            {
                $(this).val("请选择文件上传！").css({'color': 'red'});
                var $l = $(this);
                setTimeout(function () {
                    $l.val("保　存").css({'color': "#fff"});
                }, 1000);
                return;
            }
        }
        else if(replyModel.type == 4)
        {
            content.file=$("#video_upload_hidden").val();
            if(content.file=="")
            {
                $(this).val("请选择文件上传！").css({'color': 'red'});
                var $l = $(this);
                setTimeout(function () {
                    $l.val("保　存").css({'color': "#fff"});
                }, 1000);
                return;
            }
        }
        replyModel.content = JSON.stringify(content);
        $(this).val("正在保存请耐心等待...");
        ConsumeObject('/baseReply/ajaxUpdate',
            {   ReplyType: type,
                accountId: publicAccountId,
                content: replyModel.content,
                ReplyMessageType: replyModel.type
            },
            function (r) {
                if (r.errcode == 0) {
                    $(r.i).val("保存成功");
                    $(r.i).delay(500).val("保　存");
                    baseReply.initDisplay();
                    if (replyModel.type == 2) {
                        $(r.i).parent().show();
                    }


                }
                else {


                }
            }, this);
    })

    $("#cancelSaveButton").click(function () {
        baseReply.initDisplay();
    });


    $("#newAddButton").click(function () {

        if(replyModel!='undefined')
        {
            if (replyModel.content instanceof Array) {

            }
            else {
                replyModel.content = new Array();
            }
            var  c = jQuery.grep(replyModel.content, function (n, i) {
                return (n !== "" && n != null);
            });
            if (c.length >= global.maxNewsCount) {
                $(this).val("最多只能添加8个图片，请删除后再保存！").css({'color': 'red'});
                var $l = $(this);
                setTimeout(function () {
                    $l.val("保　存").css({'color': "#515151"});
                }, 1000);
                baseReply.clearnews();
                return;
            }
        }
        baseReply.clearnews();
        $("#newsEdit").show();
    });

    $("#sureButton").click(function () {

        function validate() {

            $("#errorDisplayLabel").text();

            if ($("#newTitleInput").val() == "" || $("#newDescriptionInput").val() == "" || $("#image_upload_hidden").val() == "" || $("#newUrlInput").val() == "") {
                $("#errorDisplayLabel").text("各内容都不能为空！").css({'color': 'red'});
                return false;
            }
            if(!ValidLength($.trim($("#newTitleInput").val()),100,0)||!ValidLength($.trim($("#newDescriptionInput").val()),400,0)||!ValidLength($.trim($("#newUrlInput").val()),400,0))
            {
                $("#errorDisplayLabel").text("输入的内容越过长度！").css({'color': 'red'});
                return false;
            }

            if(!$.trim($("#newUrlInput").val()).match("^http://"))
            {
                $("#errorDisplayLabel").text("链接地址格式不正确！").css({'color': 'red'});
                return false;
            }

            return true;
        }

        if (!validate()) {
            return;
        }
        var m = {
            Title: $("#newTitleInput").val(),
            Description: $("#newDescriptionInput").val(),
            PicUrl: $("#image_upload_hidden").val(),
            Url: $("#newUrlInput").val()

        }
        if (replyModel.content instanceof Array) {

        }
        else {
            replyModel.content = new Array();
        }

        replyModel.content.push(m);
        baseReply.appendNewView(m, replyModel.content.length - 1);
        $("#newsEdit").hide();
        $("#saveButton").parent().show();
    });
}
baseReply.clearnews=function()
{
    $("#errorDisplayLabel").text("");
    $("#newTitleInput").val("");
    $("#newDescriptionInput").val("");
    $("#newUrlInput").val("http://");
    $("#PreImg").html("");
    $("#image_upload_hidden").val("");

}
