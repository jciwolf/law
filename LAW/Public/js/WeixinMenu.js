$(function () {
        $(".add_submenu").click(function () {
            WeixinMenu.addSubMenu(this);
        });
        $(".add_mainmenu").click(function(){
            WeixinMenu.addMainMenu(this);
        });
        $(".deleteSubMenu").click(function(){
            WeixinMenu.deleteSubMenu(this);
        });
        $(".deleteMainMenu").click(function(){
            WeixinMenu.deleteMainMenu(this);
        });
        $("#cancle").click(function(){
            WeixinMenu.cancle(this);
        });
        $("#savemenu").click(function(){
            WeixinMenu.saveMenu(this);
        });
        $("#edit").click(function(){
                WeixinMenu.editMenu(this);
            }
        );
    }
);
WeixinMenu = {};
WeixinMenu.addSubMenu = function (o) {
    $("#Result").prop({'class':'success','innerHTML':''});
    var trObj = $(o).parent().parent().parent().parent().parent();
    var i = trObj.attr("data-subtotal");
    var j = parseInt(i);
    if(j>=5){
        $("#Result").prop({'class':'fail','innerHTML':'您最多可以创建5个子菜单'});
        return;
    }
    j+=1;
    trObj.attr("data-subtotal",j);
    trObj.children().first().next().next().children();
    trObj.children().first().next().next().children().children().attr("disabled", "disabled");
    var subLast = trObj;
    while(subLast.next().attr("class") == "submenu"){
        subLast = subLast.next();
    }
    subLast.after(" <tr class=\"submenu\" data-xpindex="+j+">\
        <td><label><input type=\"text\" class=\"inputstyle inpWidth40 ac\" value="+j+"></label></td>\
        <td><span class=\"ar\"><label class=\"chart_level\">&nbsp;</label><label><input placeholder='菜单名' type=\"text\" class=\"inpWidth190 inputstyle\" value=\"\"></label></span></td>\
        <td><label><input placeholder='关键词或url' type=\"text\" class=\"inpWidth310 inputstyle\" value=\"\"></label></td>\
        <td><span class=\"\"><a class='deleteSubMenu' onclick='javascript:WeixinMenu.deleteSubMenu(this);' href=\"javascript:;\">删除</a></span></td>\
    </tr>");
}

WeixinMenu.addMainMenu = function(o){
    $("#Result").prop({'class':'success','innerHTML':''});
    var i = $(o).attr("data-mainMenuTotal");
    var j = parseInt(i);
    if(j>=3){
        $("#Result").prop({'class':'fail','innerHTML':'您最多可以创建3个主菜单'});
        return;
    }
    j+=1;
    $(o).attr("data-mainMenuTotal",j);
    $("tbody").children().last().after("<tr class=\"parentMenu\" data-xpindex="+j+" data-subtotal=\"0\">\
            <td><label><input type=\"text\" class=\"inputstyle inpWidth40 ac\" value="+j+"></label></td>\
            <td><span class=\"ar\"><label><input placeholder='菜单名' type=\"text\" value=\"\" class=\"inpWidth190 inputstyle\" </label>\
            <label><input type=\"button\" value=\"添加子菜单\" class=\"gray_but add_submenu\" onclick='javascript:WeixinMenu.addSubMenu(this);'></label>\
            </span></td>\
            <td><label><input placeholder=\"关键词或url\" type=\"text\" class=\"inpWidth310 inputstyle\" value=\"\"></label></td>\
                <td><span class=\"\"><a class='deleteMainMenu' onclick='javascript:WeixinMenu.deleteMainMenu(this);' href=\"javascript:;\">删除</a></span></td>\
            </tr>");
}
WeixinMenu.cancle = function(o){
    window.location.reload();
}

WeixinMenu.saveMenu = function(o){
    var obj = $("tbody").children().first().next();
    var datas=[];

    while(obj.attr("class")=="parentMenu"||obj.attr("class")=="submenu"){
        var data = WeixinMenu.menuJson(obj);
        if(data == null)
        {
            return;
        }
        obj = obj.next();
        data["subButton"]=[];
        while(obj.attr("class")=="submenu"){
            var dataSub = WeixinMenu.menuJson(obj);
            if(dataSub == null)
            {
                return;
            }
            data["subButton"].push(dataSub);
            obj = obj.next();
        }
        datas.push(data);
    }

    var result={};
    result["menuList"]=datas;

    /*
    $(o).hide();
    $("#edit").show();
    WeixinMenu.disable($("tbody").first());
    $(".add_mainmenu").attr("disabled", "disabled");
    */
    $(o).prop({'disabled':true,'value':'保存中···'});
    ConsumeObject('/menu/createMenu/'+GetPublicAccountId(), result, function (r) {
        if (r.errcode == 0) {
            $("#Result").prop({'class':'success','innerHTML':'菜单创建成功'});
        }else{
            $("#Result").prop({'class':'fail','innerHTML':'保存失败，请重试','title':'errcode:'+ r.errcode+',errmsg:'+r.errmsg});
        }
        $(o).prop({'disabled':false,'value':'保　存'});
    });

}
WeixinMenu.editMenu = function(o){
    $("#Result").prop({'class':'success','innerHTML':''});
    WeixinMenu.enable($("tbody").first());
    $(".add_mainmenu").removeAttr("disabled");
    $("#savemenu").show();
    $("#edit").hide();
}
WeixinMenu.disable = function(o) {
/// <summary>
/// 屏蔽所有元素
/// </summary>
/// <returns type="jQuery" />
    $(o).attr("disabled", "disabled");
    return $(o).find("*").each(function() {
        WeixinMenu.disable($(this));
    });
}
WeixinMenu.enable = function(o) {
/// <summary>
/// 使得所有元素都有效
/// </summary>
/// <returns type="jQuery" />
    $(o).removeAttr("disabled");
    return $(o).find("*").each(function() {
        WeixinMenu.enable($(this));
    });
}
WeixinMenu.menuJson = function(obj){
    $("#Result").prop({'class':'success','innerHTML':''});
    var data = {};
    var td = obj.children().first();
    data["showIndex"] = $.trim(td.children().children().val());
    td = td.next();
    if(obj.attr("class")=="submenu"){
        data["name"] = td.children().children().first().next().children().val();
    }else{
        data["name"] = td.children().children().children().val();
    }
    td = td.next();
    var urlOrKey = td.children().children().val();
    if(urlOrKey.indexOf("http://")>=0){
        data["type"] = 1;
        data["keyword"] = urlOrKey;
    }else{
        data["type"] = 0;
        data["keyword"] = urlOrKey;
    }
    if(data["name"].length == 0|| data["showIndex"].length == 0){
        $("#Result").prop({'class':'fail','innerHTML':'请完善你的菜单后再提交'});
        return null;
    }
    else if(!/^\d+$/ig.test(data["showIndex"])) {
        $("#Result").prop({'class':'fail','innerHTML':'显示顺序必须是数字'});
        return null;
    }
    else if(obj.attr("class")=="parentMenu" && !ValidLength(data["name"],16)) {
        $("#Result").prop({'class':'fail','innerHTML':'主菜单长度不能超过16个字符（中文是3个字符）'});
        return null;
    }
    else if(obj.attr("class")=="submenu" && !ValidLength(data["name"],40)) {
        $("#Result").prop({'class':'fail','innerHTML':'子菜单长度不能超过40个字符（中文是3个字符）'});
        return null;
    }
    if(obj.attr("class")=="submenu"||
        (obj.attr("class")=="parentMenu" && obj.next().attr("class")!="submenu")){
        if(urlOrKey.length==0||
            data["keyword"].length == 0||
            data["type"].length == 0){
            $("#Result").prop({'class':'fail','innerHTML':'请完善你的菜单后再提交'});
            return null;
        }
    }
    return data;
}
WeixinMenu.strLengVerification = function(o){
    return o.length>0
}
WeixinMenu.deleteMainMenu = function(o){
    PopupShow('.mask-div');
    $('.mask-div h3').prop('innerHTML','删除主菜单');
    $('.mask-div span').prop('innerHTML','确定删除该主菜单及其子菜单吗？');
    $('.mask-div .mask-input').prop('innerHTML','');
    input=$('<input/>').prop({'type':'button','value':'确　认','class':'x-green_but'})
        .unbind('click').click(function () {
            var trObj = $(o).parent().parent().parent();
            var obj = trObj.next();
            while(obj.attr("class")=="submenu"){
                obj.remove();
                obj = trObj.next();
            }
            trObj.remove();
            var i = $(".add_mainmenu").attr("data-mainMenuTotal");
            var j = parseInt(i);
            j-=1;
            $(".add_mainmenu").attr("data-mainMenuTotal",j);

            $('.mask-div .mask-input .x-green_but').prop({'disabled':true,'value':'操作成功'});
            setTimeout(function () { PopupHide('.mask-div');QueryList(global.params.PageNo,global.params) }, 1000);
        })
        .appendTo($('.mask-div .mask-input'));
    input=$('<input/>').prop({'type':'button','value':'取　消','class':'gray_but'})
        .unbind('click').click(function () {PopupHide('.mask-div');})
        .appendTo($('.mask-div .mask-input'));
}
WeixinMenu.deleteSubMenu = function(o){
    PopupShow('.mask-div');
    $('.mask-div h3').prop('innerHTML','删除子菜单');
    $('.mask-div span').prop('innerHTML','确认删除吗？');
    $('.mask-div .mask-input').prop('innerHTML','');
    input=$('<input/>').prop({'type':'button','value':'确　认','class':'x-green_but'})
        .unbind('click').click(function () {
            var trObj = $(o).parent().parent().parent();
            var mainMenu = trObj.prev();
            while(mainMenu.attr("class") == "submenu"){
                mainMenu = mainMenu.prev();
            }
            var i = mainMenu.attr("data-subtotal");
            var j = parseInt(i);
            j-=1;
            mainMenu.attr("data-subtotal",j);
            if(j==0){
                WeixinMenu.enable(mainMenu);
            }
            trObj.remove();

            $('.mask-div .mask-input .x-green_but').prop({'disabled':true,'value':'操作成功'});
            setTimeout(function () { PopupHide('.mask-div');QueryList(global.params.PageNo,global.params) }, 1000);
        })
        .appendTo($('.mask-div .mask-input'));
    input=$('<input/>').prop({'type':'button','value':'取　消','class':'gray_but'})
        .unbind('click').click(function () {PopupHide('.mask-div');})
        .appendTo($('.mask-div .mask-input'));
}