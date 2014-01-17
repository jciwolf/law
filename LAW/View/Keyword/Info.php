<script type="text/javascript" language="javascript">
    global.id=<?=$Id?>;
    global.max=3;
    global.mediaHost='<?=$mediaHost?>';
    global.type=1;
    global.text='';
    global.arr=[];
    $(document).ready(function () {
        $('.x-assistant').hide();
        QueryInfo();
    });
    function QueryInfo() {
        global.type=1;
        global.text='';
        global.arr=[];
        $('#Result').prop({'class':'success','innerHTML':'','title':''});
        $('#Name').val('');
        RadioCheck('#Type1');
        RadioCheck('#MatchType1');
        RadioCheck('#Status1');
        $('#SecondaryName').val('');
        $('#Content').val('');
        if(global.id>0)
            ConsumeObject('/keyword/detail', {PublicAccountId: <?=$PublicAccountId?>,Id: global.id}, QueryInfoResult);
        else
            ParseContent();
    }
    function QueryInfoResult(r) {
        if (r.errcode == '0') {
            $('#Name').val(r.data.name);
            RadioCheck('#Type'+ r.data.type);
            RadioCheck('#MatchType'+ r.data.matchType);
            RadioCheck('#Status'+ r.data.status);
            $('#SecondaryName').val(r.data.secondaryName);
            global.type=r.data.type;
            var item=jQuery.parseJSON(r.data.content);
            if(r.data.type==1)
                global.text = item.text;
            else
                global.arr = item;
            ParseContent();
        }
        else {
            $('#Result').prop({'class':'fail','innerHTML':'读取数据出错:'+ r.errmsg,'title':'errcode:'+ r.errcode});
        }
    }
    function ParseContent(){
        var i=0;
        if(global.type==1) {
            $('#TextContent').show();
            $('#Content').val(global.text);
            $('#ImageContent').hide();
            $('#ImageContent ul[id!=ulAdd]').each(function () { $(this).remove();});
            if($('#ulAdd').length>0) $('#ulAdd').hide();
        }
        else {
            $('#TextContent').hide();
            $('#ImageContent').show();
            $('#ImageContent ul[id!=ulAdd]').each(function () { $(this).remove();});
            if($('#ulAdd').length>0) $('#ulAdd').hide();
            var ul,li,label,span,img,input;
            $.each(global.arr,function(index,item){
                ul=$('<ul/>').prop({'id':'ul'+index});
                if($('#ulAdd').length==0)
                    ul.appendTo($('#ImageContent'));
                else
                    $('#ulAdd').before(ul);
                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'标题：'}).appendTo(li);
                span=$('<span/>').prop({'innerHTML':item.Title}).appendTo(li);
                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'描述：'}).appendTo(li);
                span=$('<span/>').prop({'innerHTML':item.Description}).appendTo(li);
                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'地址链接：'}).appendTo(li);
                span=$('<span/>').prop({'innerHTML':item.Url}).appendTo(li);
                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'上传图片：'}).appendTo(li);
                span=$('<span/>').appendTo(li);
                img=$('<img/>').prop({'width':373,'height':174,'src':item.PicUrl}).appendTo(span);
                span=$('<span/>').appendTo(li);
                input=$('<input/>').prop({'type':'button','value':'删　除','class':'gray_but'})
                        .unbind('click').click(function () { DeleteItem(index); })
                        .appendTo(span);
            });
        }
        if($('#ImageContent ul').length==0) AddNew();
    }
    function AddNew() {
        $("#Result").prop({'class':'success','innerHTML':''});
        if($('#ulAdd').length==0) {
            if(global.arr.length<global.max) {
                var ul,li,label,span,img, p,input;
                ul=$('<ul/>').prop({'id':'ulAdd'}).appendTo($('#ImageContent'));
                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'标题：'}).appendTo(li);
                span=$('<span/>').appendTo(li);
                input=$('<input/>').prop({'type':'text','class':'inpWidth170','id':'Title'}).appendTo(span);
                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'描述：'}).appendTo(li);
                span=$('<span/>').appendTo(li);
                input=$('<input/>').prop({'type':'text','class':'inpWidth170','id':'Description'}).appendTo(span);
                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'地址链接：'}).appendTo(li);
                span=$('<span/>').appendTo(li);
                input=$('<input/>').prop({'type':'text','class':'inpWidth460','id':'Url'}).appendTo(span);
                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'上传图片：'}).appendTo(li);
                span=$('<span/>').appendTo(li);
                img=$('<img/>').prop({'width':373,'height':174,'src':'','id':'ImgUpload'}).hide().appendTo(span);
                input=$('<input/>').prop({'type':'file','class':'inpWidth460','id':'image_upload','name':'image_upload'}).appendTo(span);
                p=$('<p/>').prop({'class':'x-dy','innerHTML':'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;大小<5M 格式：bmp、png、jpeg、jpg、gif'}).appendTo(li);

                li=$('<li/>').appendTo(ul);
                label=$('<label/>').prop({'innerHTML':'&nbsp;'}).appendTo(li);
                span=$('<span/>').appendTo(li);
                input=$('<input/>').prop({'type':'button','class':'x-green_but','value':'添　加'})
                        .unbind('click').click(function() {AddItem();})
                        .appendTo(span);

                $('#image_upload').uploadify({
                    height: 30,
                    swf: '/css/uploadify.swf',
                    uploader: '/baseReply/uploader',
                    'fileSizeLimit': '1024KB',
                    'fileTypeDesc': 'Image Files',
                    'fileTypeExts': '*.bmp;*.png;*.jpeg;*.jpg;*.gif',
                    width: 120,
                    'buttonText': '请选择...',
                    onUploadSuccess: function (file, data, response) {
                        if (data == "Invalid Size") {
                            $.messager.alert("", "图片不适合要求，请重新上传！");
                        }
                        else {
                            $('#ImgUpload').prop('src',global.mediaHost + data).show();
                        }
                    },
                    'onUploadStart': function (file) {
                        $("#image_upload").uploadify("settings", "formData", {'BannerType': $("#addBannerType").val()});
                    }
                });
            }
            else
                $("#Result").prop({'class':'fail','innerHTML':'图文不能多于'+global.max+'条','title':''});
        }
        else {
            $('#ulAdd').show();
        }
    }
    function AddItem() {
        $("#Result").prop({'class':'success','innerHTML':''});
        if($.trim($('#Title').val())=='' || $.trim($('#Description').val())=='' || !$('#ImgUpload').is(':visible') || $.trim($('#Url').val())=='') {
            $("#Result").prop({'class':'fail','innerHTML':'图文信息不完整','title':''});
        }
        else {
            var item={Title:$.trim($('#Title').val()),Description:$.trim($('#Description').val()),PicUrl:$('#ImgUpload').attr('src'),Url:$.trim($('#Url').val())};
            global.arr.push(item);
            ParseContent();
            $('#Title').val('');
            $('#Description').val('');
            $('#ImgUpload').prop('src','').hide();
            $('#Url').val('');

        }
    }
    function DeleteItem(index) {
        global.arr.splice(index,1);
        ParseContent();
    }
    function RadioCheck(obj) {
        var id=$(obj).prop('id');
        $('#'+id).parent().parent().find('label').each(function(index,item){
            $(item).prop('class',$(item).prop('id')==id?'checked':'');
        });
        if(id=='Type1'||id=='Type2') {
            global.type=GetValue('Type');
            if(GetValue('Type')==1) {
                $('#TextContent').show();
                $('#ImageContent').hide();
            }
            else {
                $('#TextContent').hide();
                $('#ImageContent').show();
            }
        }
    }
    function GetValue(id) {
        var r='';
        $('label[id^='+id+']').parent().parent().find('label').each(function(index,item){
            if($(item).prop('class')=='checked') {
                r=$(item).prop('id').replace(/[^0-9]/ig,'');
                return false;
            }
        });
        return r;
    }
    function Save() {
        $('#ulAdd').length>0 && $('#ulAdd').is(':visible') && AddItem();
        $("#Result").prop({'class':'success','innerHTML':''});
        $("#btnSave").prop({'disabled':true,'value':'保存中···'});
        ConsumeObject('/keyword/'+(global.id==0?'add':'update'),{Id:global.id,PublicAccountId: <?=$PublicAccountId?>,Name:$('#Name').val(),SecondaryName:$('#SecondaryName').val(),Type:GetValue('Type'),MatchType:GetValue('MatchType'),Status:GetValue('Status'),Content:JSON.stringify(GetValue('Type')==1?{text:$('#Content').val()}:global.arr)}, SaveResult);
    }
    function SaveResult(r) {
        var i = 0;
        if (r.errcode == '0') {
            $("#btnSave").prop({'value':'保存成功'});
            setTimeout(function () { window.close();}, 1000);
        }
        else {
            $("#btnSave").prop({'disabled':false,'value':'保　存'});
            $("#Result").prop({'class':'fail','innerHTML':'保存失败，请重试','title':'errcode:'+ r.errcode+',errmsg:'+r.errmsg});
        }
    }
</script>
<?php
$this->layout()->addJs('plugins/jquery.uploadify.js');
$this->layout()->addCss('uploadify.css');
?>
<?php XP_Lib_Partial::includes('Navigation'); ?>
<h4 class="path-H">新建关键词：</h4>
<div class="x-list-li x-l-p">
    <ol id="male">
        <li>关键词名称：<span><input type="text" value="" class="inpWidth170" name="" id="Name"></span></li>
        <li id="key">关键词类型：
            <span><label id="Type1" class="" onclick="RadioCheck(this)"> 文字型</label></span>
            <span><label id="Type2" class="" onclick="RadioCheck(this)"> 图文型</label></span>
        </li>
        <li id="march">&nbsp;&nbsp;&nbsp;&nbsp;匹配类型：
            <span><label id="MatchType1" class="" onclick="RadioCheck(this)"> 精确&nbsp;&nbsp;&nbsp;</label></span>
            <span><label id="MatchType2" class="" onclick="RadioCheck(this)"> 模糊</label></span>
            <a href="" onclick="$('.x-assistant').show();return false;" style="margin-left:40px;">不懂点我</a>
        </li>
        <li id="status">&nbsp;&nbsp;&nbsp;&nbsp;启用状态：
            <span><label id="Status1" class="" onclick="RadioCheck(this)"> 启用&nbsp;&nbsp;&nbsp;</label></span>
            <span><label id="Status0" class="" onclick="RadioCheck(this)"> 禁用</label></span>
        </li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;次关键词：<span><textarea id="SecondaryName" name="" cols="" rows="6" class="x-texttarea440" ></textarea><p class="x-dy" >*多个用“；”隔开</p></span></li>
        <li id="TextContent">&nbsp;&nbsp;&nbsp;&nbsp;回复内容：<span><textarea id="Content" name="" cols="" rows="6" class="x-texttarea440" ></textarea><p class="x-dy" >*最多可输入200个文字</p></span></li>
    </ol>
    <div id="ImageContent">
        <ol>
            <li><label for="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;回复内容：&nbsp;&nbsp;</label><span><input type="button" value="添加图片" class="gray_but" onclick="AddNew();"></span></li>
        </ol>
        <div class="clear"></div>
        <ul>
            <li><label for="">标题：</label><span>图片素材标题</span></li>
            <li><label for="">描述：</label><span>图片素材描述</span></li>
            <li><label for="">地址链接：</label><span>http://w.gaopeng.com</span></li>
            <li><label for="">上传图片：</label><span><img src="images/1.png" width="373" height="174"></span><span><input type="button" value="删除" class="gray_but" onclick="DeleteItem();"></span></li>
        </ul>
        <ul>
            <li><label for="">标题：</label><span><input type="text" value="" class="inpWidth170" name="" id=""></span></li>
            <li><label for="">描述：</label><span><input type="text" value="" class="inpWidth170" name="" id=""></span></li>
            <li><label for="">地址链接：</label><span><input type="text" value="" class="inpWidth460" name="" id=""></span></li>
            <li><label for="">上传图片：</label>
                <span><input type="text" value="" class="inpWidth460" name="" id=""></span>
                <input type="button" value="浏览" class="gray_but">
                <input type="button" value="上传" class="x-green_but">
                <p class="x-dy" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;大小<5M 格式：bmp、png、jpeg、jpg、gif</p>
            </li>
        </ul>
    </div>
</div>
<div class="x-assistant">
    <h5>小助手</h5>
    <p>1、精确型即当用户回复的内容与关键词完全一致时，才下发该关键词的内容，否则下发默认回复的内容;<br>2、模糊型即当用户回复的内容包含关键词，则下发该关键词的内容；若用户回复的内容包含多个关键词，则下发最近保存的关键词内容。</p>
    <div class="ac"><input type="button" value="知道了" class="x-green_but" onclick="$('.x-assistant').hide();"></div>
</div>
<div class="butbox"><input type="button" value="保　存" class="x-green_but" id="btnSave" onclick="Save();"/>&nbsp;&nbsp;&nbsp;<input type="button" value="取　消" class="gray_but" onclick="QueryInfo();"/><span id="Result"></span></div>


