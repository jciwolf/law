<?php
$this->layout()->addJs('plugins/jquery.xpagination.js');
$this->layout()->addJs('plugins/jquery.uploadify.js');
$this->layout()->addCss('uploadify.css');
?>
<script type="text/javascript" language="javascript">
global.id=0;
global.max = 8;
global.mediaHost='<?=$mediaHost?>';
global.type=1;
global.text='';
global.arr=[];
var editor;

$(document).ready(function () {
    $('.x-assistant').hide();
    /*
    editor = new RBMEditor.Editor('oDiv');
    $("#insertLink").click(function () {
        var theResponse = window.prompt("请输入链接地址！", "http://");
		if(!theResponse.match("^http://"))
		{
			$("#insertLink").click();
		}
        editor.ExecuteCommand(RBMEditor.EditorCommands.LINK, 'href', theResponse);
        return false;
    });
    */
    CKEDITOR.replace( 'Content', {
        fullPage: true,
        allowedContent: true
    });
    Query();
});
function Query() {
    $("#btnQuery").prop({'disabled':true,'value':'查询中···'});

    global.first = true;
    global.load = true;
    global.params = null;
    QueryList(1, global.params);
}
function QueryList(pageno,params) {
    $('#CheckAll1').prop('checked',false);
    global.params = params || { PageSize: global.pagesize, PageNo: pageno,PublicAccountId: <?=$PublicAccountId?>, Name: $('#SearchName').val(), Type: $('#Type').val(), MatchType: $('#MatchType').val(), Status: $('#Status').val() };
    global.params.PageNo = pageno;
    if (global.load==true) {
        global.load = false;
        ShowResult('loading');
    }
    ConsumeObject('/keyword/list', global.params, QueryResult);
}
function QueryResult(r) {
    var i = 0;
    $("#btnQuery").prop({'disabled':false,'value':'查　询'});
    if (r.errcode == '0') {
        if (r.data.amount == 0)
            ShowResult('noresult');
        else {
            $('.table_gzh tr[class!="pagetr"]').each(function () {
                if (i++ > 0) $(this).remove();
            });
            $.each(r.data.list, function (index, item) {
                var tr = $('<tr/>').prop({ 'class': index % 2 == 1 ? 'odd' : '' });
                tr.mouseenter(function () { $(this).addClass("hover"); });
                tr.mouseleave(function () { $(this).removeClass("hover"); });
                $(tr).unbind('click').click(function () {Check('#Check_'+item.id);});
                $('.pagetr').before(tr);
                tr.mouseenter(function () { $(this).addClass("hover"); });
                tr.mouseleave(function () { $(this).removeClass("hover"); });
                var td, input, span, label, a;
                td=$('<td/>').appendTo(tr);
                label=$('<label/>').prop({'class':'check-one','innerHTML':''}).appendTo(td);
                input=$('<input/>').prop({'type':'checkbox','value':item.id,'id':'Check_'+item.id}).appendTo(label);
                td=$('<td/>').prop('innerHTML',item.name).appendTo(tr);
                td=$('<td/>').prop('innerHTML',Enum('messageType',item.type)).appendTo(tr);
                td=$('<td/>').prop('innerHTML',item.secondaryName).appendTo(tr);
                td=$('<td/>').prop('innerHTML',Enum('matchType',item.matchType)).appendTo(tr);
                td=$('<td/>').prop('innerHTML',item.updateTime).appendTo(tr);
                td=$('<td/>').prop({'innerHTML':Enum('status',item.status),'id':'status'+item.id}).appendTo(tr);
                td=$('<td/>').appendTo(tr);
                span=$('<span/>').prop('class','actionMenu').appendTo(td);
                //a=$('<a/>').prop({'innerHTML':'修改','href':'/keyword/info?pid=<?=$PublicAccountId?>&id='+item.id,'target':'_blank'}).appendTo(span);
                a=$('<a/>').prop({'innerHTML':'修改'})
                    .unbind('click').click(function () { QueryInfo(item.id);})
                    .appendTo(span);
                a=$('<a/>').prop({'href':'javascript:void(0);','innerHTML':Enum('status',item.status==1?0:1)})
                    .unbind('click').click(function () { ChangeStatus(item.id,item.status==1?0:1); })
                    .appendTo(span);
                a=$('<a/>').prop({'href':'javascript:void(0);','innerHTML':'次关键词'})
                    .unbind('click').click(function () { ChangeSecondaryName(item.id,item.name,item.type,item.secondaryName);})
                    .appendTo(span);
                a=$('<a/>').prop({'href':'javascript:void(0);','innerHTML':'删除','disabled':item.status==1?true:false,'class':item.status==1?'gray':''})
                    .unbind('click').click(function () { if(item.status!=1)Delete(item.id); })
                    .appendTo(span);
            });
        }
        $(".list_bottom").pagination({
            total_count: r.data.amount,
            page_size: global.params.PageSize,
            current_page: global.params.PageNo,
            callback: function (currentPage, invoker) {
                QueryList(currentPage,global.params);
            },
            page_toolbar: '<span class="actionMenu">\
                                    <a href="javascript:ChangeStatus(null,1);" id="batchOpen">批量启用</a>\
                                    <a href="javascript:ChangeStatus(null,0);" id="batchClose">批量禁用</a>\
                                    <!--<a href="javascript:Delete(null);" id="batchDelete">批量删除</a>-->\
                                    </span>'
        });
        $('input[id^=CheckAll]').each(function(){
            $(this).unbind('click').click(function(){Check(this);});
        })
        $('input[id^=Check_]').each(function(){
            $(this).unbind('click').click(function(){$(this).prop('checked', !$(this).prop('checked'));});
        });
    }
    else
        ShowResult('error', decodeURIComponent(r.errmsg));
}
function ShowResult(type, title) {
    var message = '';
    var i=0;
    title = title || '';
    $('.table_gzh tr[class!="pagetr"]').each(function () {
        if (i++ > 0) $(this).remove();
    });
    switch (type) {
        case 'loading':
            message = '<img src="/images/loading.gif" />数据加载中···';
            break;
        case 'noresult':
            message = '未找到符合条件的记录！';
            break;
        case 'error':
            message = '<span class="fail">页面出错，请刷新后重试！</span>';
            break;
        default:
            break;
    }
    var tr = $('<tr/>').attr({ 'class': 'odd' });
    $('.pagetr').before(tr);
    $('<td/>').prop({ 'colspan': 8, 'innerHTML': message, 'title': title }).appendTo(tr);
}
function Check(source) {
    var selected = 0, count = 0;
    if($(source).val()<0) {
        $('input[id^=CheckAll]').each(function(){
            $(this).prop('checked', $(source).prop('checked'));
        });
        $('input[id^=Check_]').each(function(){
            $(this).prop('checked', $(source).prop('checked'));
        });
    }
    else {
        $(source).prop('checked', !$(source).prop('checked'));
        $('input[id^=Check_]').each(function(){
            if ($(this).prop('checked') == true)
                selected++;
            count++;
        });
        $('input[id^=CheckAll]').each(function(){
            $(this).prop('checked', selected == count);
        });
    }
}
function ChangeStatus(id,status) {
    var list='';
    if(id==null) {
        $('input[id^=Check_]').each(function(){
            if ($(this).prop('checked')) list=list+(list==''?'':',')+$(this).val();
        });
    }
    if(id!=null || list!='') {
        PopupShow('.mask-div');
        $('.mask-div .warm').show();
        $('.mask-div .universal').hide();
        $('.mask-div h3').prop('innerHTML',(id==null?'批量':'')+Enum('status',status));
        $('.mask-div .warm span').prop({'class':'','innerHTML':'正在处理中···'});
        $('.mask-div .mask-input').prop('innerHTML','&nbsp;');
        ConsumeObject('/keyword/status'+(id==null?'multi':''), {Id:id==null?list:id,Status:status}, CommonResult);
    }
}
function Delete(id) {
    var list='';
    if(id==null) {
        $('input[id^=Check_]').each(function(){
            if ($(this).prop('checked')) list=list+(list==''?'':',')+$(this).val();
        });
    }
    if(id!=null || list!='') {
        var input;
        PopupShow('.mask-div');
        $('.mask-div .warm').show();
        $('.mask-div .universal').hide();
        $('.mask-div h3').prop('innerHTML',(id==null?'批量':'')+'删除');
        $('.mask-div .warm span').prop({'class':'','innerHTML':'确认删除吗？'});
        $('.mask-div .mask-input').prop('innerHTML','');
        input=$('<input/>').prop({'type':'button','value':'确　认','class':'x-green_but'})
            .unbind('click').click(function () {$('.mask-div .mask-input .x-green_but').prop('disabled',true);ConsumeObject('/keyword/delete'+(id==null?'multi':''), {Id:id==null?list:id}, CommonResult);})
            .appendTo($('.mask-div .mask-input'));
        input=$('<input/>').prop({'type':'button','value':'取　消','class':'gray_but'})
            .unbind('click').click(function () {PopupHide('.mask-div');})
            .appendTo($('.mask-div .mask-input'));
    }
}
function CommonResult(r) {
    if (r.errcode == '0') {
        $('.mask-div .warm span').prop({'class':'success','innerHTML':'操作成功'});
    }
    else {
        $('.mask-div .warm span').prop({'class':'fail','innerHTML':'操作失败，请重试','title': 'errcode:'+ e.errcode+',errmsg:'+e.errmsg});
    }
    setTimeout(function () { PopupHide('.mask-div');QueryList(global.params.PageNo,global.params) }, 1000);
}
function ChangeSecondaryName(id,name,type,secondaryName) {
    PopupShow('.mask-div');
    var input;
    $('.mask-div .warm').hide();
    $('.mask-div .universal').show();
    $('.mask-div h3').prop('innerHTML','次关键词设置');
    $('#NameShow').prop('innerHTML',name);
    $('#TypeShow').prop('innerHTML',Enum('messageType',type));
    $('#ResultShow').prop('innerHTML','');
    $('#SecondaryNameShow').val(secondaryName);
    $('.mask-div .mask-input').prop('innerHTML','');
    input=$('<input/>').prop({'type':'button','value':'确　认','class':'x-green_but'})
        .unbind('click').click(function () {
            $('.mask-div .mask-input .x-green_but').prop({'disabled':true,'value':'处理中···'});
            ConsumeObject('/keyword/updatesecondaryname', {Id:id,SecondaryName: $.trim($('#SecondaryNameShow').val())}, ChangeSecondaryNameResult);
        })
        .appendTo($('.mask-div .mask-input'));
    input=$('<input/>').prop({'type':'button','value':'取　消','class':'gray_but'})
        .unbind('click').click(function () {PopupHide('.mask-div');})
        .appendTo($('.mask-div .mask-input'));
}
function ChangeSecondaryNameResult(r) {
    if (r.errcode == '0') {
        $('.mask-div .mask-input .x-green_but').prop({'disabled':true,'value':'操作成功'});
        setTimeout(function () { PopupHide('.mask-div');QueryList(global.params.PageNo,global.params) }, 1000);
    }
    else if(r.errcode == '2001') {
        $('.mask-div .mask-input .x-green_but').prop({'disabled':false,'value':'确　认'});
        $("#ResultShow").prop({'class':'fail','innerHTML':'关键词重复：'+ r.data,'title':'errcode:'+ r.errcode+',errmsg:'+r.errmsg});
    }
    else {
        $('.mask-div .mask-input .x-green_but').prop({'disabled':false,'value':'操作失败，请重试','title': 'errcode:'+ r.errcode+',errmsg:'+r.errmsg});
    }
}
/*----------------------update info begin-------------------*/
function QueryInfo(id) {
    PopupShow('.mask-div750');
    $('.mask-div750 h3').prop('innerHTML',(id>0?'':'新增')+'关键词'+(id>0?'修改':''));
    global.id=id;
    global.type=1;
    global.text='';
    global.arr=[];
    $('#Result').prop({'class':'success','innerHTML':'','title':''});
    $("#btnSave").prop({'disabled':false,'value':'保　存'});
    $('#Name').val('');
    RadioCheck('#Type1');
    RadioCheck('#MatchType1');
    RadioCheck('#Status1');
    $('#SecondaryName').val('');
    //$('#Content').val('');
    //editor.controlContent.innerHTML = "";
    CKEDITOR.instances.Content.setData('');
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
        //$('#Content').val(global.text);
        //editor.controlContent.innerHTML = global.text;
        CKEDITOR.instances.Content.setData(global.text);
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
            img=$('<img/>').prop({'width':373,'height':174,'src':global.mediaHost+item.PicUrl}).appendTo(span);
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
            input=$('<input/>').prop({'type':'text','class':'inpWidth460','id':'Url','value':'http://'}).appendTo(span);
            li=$('<li/>').appendTo(ul);
            label=$('<label/>').prop({'innerHTML':'上传图片：'}).appendTo(li);
            span=$('<span/>').appendTo(li);
            img=$('<img/>').prop({'width':373,'height':174,'src':'','id':'ImgUpload'}).hide().appendTo(span);
            input=$('<input/>').prop({'type':'file','class':'inpWidth460','id':'image_upload','name':'image_upload'}).appendTo(span);
            p=$('<p/>').prop({'class':'x-tips','innerHTML':'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;大小<5M 格式：bmp、png、jpeg、jpg、gif'}).appendTo(li);

            li=$('<li/>').appendTo(ul);
            label=$('<label/>').prop({'innerHTML':'&nbsp;'}).appendTo(li);
            span=$('<span/>').appendTo(li);
            input=$('<input/>').prop({'type':'button','class':'x-green_but','value':'保存此条'})
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
    if($.trim($('#Title').val())=='' || $.trim($('#Description').val())=='' || !$('#ImgUpload').is(':visible')) {
        $("#Result").prop({'class':'fail','innerHTML':'图文信息不完整','title':''});
    }
    else if($.trim($('#Url').val()).indexOf('http://')==-1)
        $("#Result").prop({'class':'fail','innerHTML':'地址链接必须以http://开头','title':''});
    else {
        var item={Title:$.trim($('#Title').val()),Description:$.trim($('#Description').val()),PicUrl:$('#ImgUpload').attr('src').substring(global.mediaHost.length),Url:$.trim($('#Url').val())};
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
    var find=false;
    var list=$('#SecondaryName').val().split(';');
    list.push($('#Name').val());
    for(var i=0;i<list.length;i++) {
        if(find) break;
        for(var j=i+1;j<list.length;j++)
            if($.trim(list[i])==$.trim(list[j])) {
                find=true;
                break;
            }
    }
    $('#ulAdd').length>0 && $('#ulAdd').is(':visible') && AddItem();
    if ($.trim($('#Name').val()) == '') {
        $("#Result").prop({'class':'fail','innerHTML':'请填写关键词名称','title':''});
    }
    else if (!ValidLength($.trim($('#Name').val()),300)) {
        $("#Result").prop({'class':'fail','innerHTML':'关键词名称最多输入300字符（中文=2个字符）','title':''});
    }
    else if($.trim($('#SecondaryName').val()) == ''){
        $("#Result").prop({'class':'fail','innerHTML':'请填写次关键词','title':''});
    }
    else if(find){
        $("#Result").prop({'class':'fail','innerHTML':'关键词、次关键词出现重复','title':''});
    }
    /*
    else if(GetValue('Type') == 1 && ($.trim(editor.ToString()) == '')){
        $("#Result").prop({'class':'fail','innerHTML':'请填写回复内容','title':''});
    }
    else if(GetValue('Type') == 1 && !ValidLength($.trim(editor.ToString()),200)){
        $("#Result").prop({'class':'fail','innerHTML':'回复内容最多输入200字符（中文=2个字符）','title':''});
    }*/
    else if(GetValue('Type') == 1 && ($.trim(CKEDITOR.instances.Content.document.getBody().getHtml()) == '')){
        $("#Result").prop({'class':'fail','innerHTML':'请填写回复内容','title':''});
    }
    else if(GetValue('Type') == 1 && !ValidLength($.trim(CKEDITOR.instances.Content.document.getBody().getHtml()),600)){
        $("#Result").prop({'class':'fail','innerHTML':'回复内容最多输入600字符（中文=3个字符）','title':''});
    }
    else if(GetValue('Type') != 1 && global.arr.length == 0){
        $("#Result").prop({'class':'fail','innerHTML':'请添加一条图文信息','title':''});
    }
    else {
        $("#Result").prop({'class':'success','innerHTML':''});
        $("#btnSave").prop({'disabled':true,'value':'保存中···'});
        ConsumeObject('/keyword/' + (global.id == 0 ? 'add' : 'update'), {Id: global.id, PublicAccountId: <?=$PublicAccountId?>, Name: $.trim($('#Name').val()), SecondaryName: $.trim($('#SecondaryName').val()), Type: GetValue('Type'), MatchType: GetValue('MatchType'), Status: GetValue('Status'), Content: JSON.stringify(GetValue('Type') == 1 ? {text: $.trim(CKEDITOR.instances.Content.document.getBody().getHtml())} : global.arr)}, SaveResult);
    }
}
function SaveResult(r) {
    var i = 0;
    if (r.errcode == '0') {
        $("#btnSave").prop({'value':'保存成功'});
        setTimeout(function () { PopupHide('.mask-div750');QueryList(global.id>0?global.params.PageNo:1,global.params) }, 1000);
    }
    else if(r.errcode == '2001') {
        $("#btnSave").prop({'disabled':false,'value':'保　存'});
        $("#Result").prop({'class':'fail','innerHTML':'关键词重复：'+ r.data,'title':'errcode:'+ r.errcode+',errmsg:'+r.errmsg});
    }
    else {
        $("#btnSave").prop({'disabled':false,'value':'保　存'});
        $("#Result").prop({'class':'fail','innerHTML':'保存失败，请重试','title':'errcode:'+ r.errcode+',errmsg:'+r.errmsg});
    }
}
/*----------------------update info end-------------------*/
</script>
<?php XP_Lib_Partial::includes('Navigation'); ?>
<h4 class="path-H">关键词列表：</h4>
<!--条件查询 start-->
<div class="query-list">
    <ul>
        <li><label for="">关键词名称：</label><span><input type="text" class="inpWidth150" id="SearchName"></span></li>
        <li>
            <label for="">关键词类型：</label>
                        <span class="inpWidth105">
                            <select class="ui_element inpWidth150" id="Type">
                                <option value="" selected>全部</option>
                                <option value="1">文本型</option>
                                <option value="2">图文型</option>
                            </select>
                        </span>
        </li>
        <li>
            <label for="">匹配类型：</label>
                        <span class="inpWidth105">
                            <select class="ui_element inpWidth150" id="MatchType">
                                <option value="" selected>全部</option>
                                <option value="1">精确匹配</option>
                                <option value="2">模糊匹配</option>
                            </select>
                        </span>
        </li>
        <li>
            <label for="">启用状态：</label>
                        <span class="inpWidth105">
                            <select class="ui_element inpWidth150" id="Status">
                                <option value="" selected>全部</option>
                                <option value="1">启用</option>
                                <option value="0">禁用</option>
                            </select>
                        </span>
        </li>
        <li id="list-box-bth"><input type="button" value="查　询" class="x-green_but" onclick="Query();" id="btnQuery">　<input type="button" value="新　增" class="gray_but" onclick="QueryInfo(0);" id="btnQuery"></li>
    </ul>
    <div class="clear"></div>
</div>
<!--条件查询 end-->
<div>
    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_gzh">
        <tr>
            <th width="6%"><label class="check-one"><!--<input type="checkbox" align="absmiddle" id="CheckAll1" value="-1"><label for="CheckAll1">全选-->选择</label></th>
            <th width="11%">关键词名称</th>
            <th width="7%">类型</th>
            <th width="19%">次关键词</th>
            <th width="10%">匹配类型</th>
            <th width="15%">最后修改时间</th>
            <th width="8%">启用状态</th>
            <th width="19%">操 作</th>
        </tr>
        <tr>
            <td><label class="check-one"><input type="checkbox">&nbsp;</label></td>
            <td>非同小客</td>
            <td>文字型</td>
            <td>飞拓,beijing,KTV</td>
            <td>精准匹配</td>
            <td>2013-10-01 12:5:59</td>
            <td>启用</td>
            <td>
                <span class="actionMenu">
                	<a href="">修改</a>
                    <a href="">启动</a>
                    <a href="">次关键词</a>
                    <a href="">设置</a>
                    <a href="">删除</a>
                    <a href="">预览</a>
                </span>
            </td>
        </tr>
        <tr class="pagetr">
            <th colspan="8" align="left">
                <ul class="list_bottom">
                </ul>
            </th>
        </tr>
    </table>
</div>
<div class="mask-div" id="batch_process" style="display: none;">
    <h3>批量操作</h3>
    <div class="warm">
        <img src="/images/warm.png"><span>确定要批量启动码？</span>
    </div>
    <div class="universal height40">
        <ul>
            <li>
                <label for="">次关键词名称：</label>
                <span id="NameShow">KTV欢唱</span>
            </li>
            <li>
                <label for="">关键词类型：</label>
                <span id="TypeShow">文字型</span>
            </li>
            <li><label for="">次关键词：</label><span><textarea id="SecondaryNameShow" name="" cols="" rows="6" style="width:300px;" class="x-texttarea440" ></textarea><p class="x-dy" style="padding:0;" >*多个用“；”隔开</p></span></li>
            <li>
                <label for="">&nbsp;</label>
                <span id="ResultShow"></span>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
    <div class="mask-input"><input type="button" value="确认" class="x-green_but"><input type="button" value="取消" class="gray_but"></div>
</div>

<div class="mask-div750" style="display: none;">
    <h3>修改</h3>
    <div class="universal_1">
        <ul id="male">
            <li><p>关键词名称：</p><span><input type="text" value="" class="inpWidth170" name="" id="Name"></span></li>
            <li id="key"><p>关键词类型：</p>
                <span><label id="Type1" class="" onclick="RadioCheck(this)"> 文字型</label></span>
                <span><label id="Type2" class="" onclick="RadioCheck(this)"> 图文型</label></span>

            </li>
            <li id="march"><p>匹配类型：</p>
                <span><label id="MatchType1" class="" onclick="RadioCheck(this)"> 精确&nbsp;&nbsp;&nbsp;</label></span>
                <span><label id="MatchType2" class="" onclick="RadioCheck(this)"> 模糊</label></span>
                <a href="" onclick="$('.x-assistant').show();return false;" class="link">不懂点我</a>
            </li>
            <li id="status"><p>启用状态：</p>
                <span><label id="Status1" class="" onclick="RadioCheck(this)"> 启用&nbsp;&nbsp;&nbsp;</label></span>
                <span><label id="Status0" class="" onclick="RadioCheck(this)"> 禁用</label></span>
            </li>
            <li><p>次关键词：</p><span><textarea id="SecondaryName" name="" cols="" rows="6" class="x-texttarea440" ></textarea><h4 class="x-dy" >*多个用“；”隔开</h4></span></li>
            <li id="TextContent">
                <p>回复内容：</p>
                <span class="ckeTXT">
					<textarea id="Content" name="Content" cols="" rows="6" class="x-texttarea440"></textarea>
                    <h4 class="x-dy">*最多输入600字符（中文=3个字符）</h4>
				</span>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
    <div id="ImageContent" class="x-list-liUP">
        <ol>
            <li><label for="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;回复内容：&nbsp;&nbsp;</label><span><input type="button" value="添加一条" class="gray_but" onclick="AddNew();"></span></li>
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
    <div class="x-assistant">
        <h5>小助手</h5>
        <p>1、精确型即当用户回复的内容与关键词完全一致时，才下发该关键词的内容，否则下发默认回复的内容;<br>2、模糊型即当用户回复的内容包含关键词，则下发该关键词的内容；若用户回复的内容包含多个关键词，则下发最近保存的关键词内容。</p>
        <div class="ac"><input type="button" value="知道了" class="x-green_but" onclick="$('.x-assistant').hide();"></div>
    </div>
    <div class="x-bthDIV"><input type="button" value="保　存" class="x-green_but" id="btnSave" onclick="Save();"/>&nbsp;&nbsp;&nbsp;<input type="button" value="取　消" class="gray_but" onclick="PopupHide('.mask-div750');"/><span id="Result"></span></div>

</div>
<script src="/js/ckeditor/ckeditor.js"></script>
