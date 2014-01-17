var global = { count: 0, current: 0, pagesize: 10, params: null, find: false, first: false, load: false, params: null,maxNewsCount:8 };
function ConsumeObject(url, params, callback, i) {
    var r = {};
    $.ajax({
        type: "POST",
        async: true,
        url: url,
        data: $.param(params),
        success: function (msg) {
            try {
                r = jQuery.parseJSON(msg);
            }
            catch (err) {
                r.errcode = '9998';
                r.errmsg = err.description;
            }
            r.i = i;
            callback(r);
        },
        error: function (msg) {
            r.errcode = '9999';
            r.errmsg = msg.Message;
            r.i = i;
            callback(r);
        }
    });
    return r;
}

function GetCookie(name, key) {
    var r = "";
    name = name + '[' + key + ']';
    try {
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split('; ');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    r = decodeURIComponent(cookie.substring(name.length + 1));
                    //cookieValue = cookie.substring(name.length + 1);
                    //var reg = new RegExp(key + '=([^&]*)', 'i');
                    //r = decodeURIComponent((cookieValue + '&').match(reg)[1]);
                    break;
                }
            }
        }
    }
    catch (err) {
    }
    return r;
}
function SetCookie(name, key, value) {
    var r = '';
    var result = '';
    try {
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split('; ');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    var cookieValue = cookie.substring(name.length + 1);
                    var reg = new RegExp(key + '=([^&]*)', 'i');
                    var content = key + '=' + encodeURIComponent(value);
                    if (reg.test(cookieValue))
                        cookieValue = cookieValue.replace(reg, content);
                    else
                        cookieValue = cookieValue + (jQuery.trim(cookieValue) == '' ? '' : '&') + content;

                    var days = 3000;
                    var expires = new Date();
                    expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
                    result = name + '=' + cookieValue + ";expires=" + expires.toGMTString();
                    document.cookie = result;
                    break;
                }
            }
        }
    }
    catch (err) {
    }
    return;
}

Date.prototype.format = function (fmt) {
    var o = {
        "M+": this.getMonth() + 1,
        "d+": this.getDate(),
        "h+": this.getHours(),
        "H+": this.getHours(),
        "m+": this.getMinutes(),
        "s+": this.getSeconds(),
        "q+": Math.floor((this.getMonth() + 3) / 3),
        "S": this.getMilliseconds()
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}
function TableRowspan(tableid, tablecolnum) {
    tablefirsttd = "";
    tablecurrenttd = "";
    tableSpanNum = 0;
    tableObj = $(tableid + " tr td:nth-child(" + (tablecolnum + 1) + ")");
    tableObj.each(function (i) {
        if (i == 0) {
            tablefirsttd = $(this);
            tableSpanNum = 1;
        } else {
            tablecurrenttd = $(this);
            if (tablefirsttd.text() == tablecurrenttd.text()) {
                tableSpanNum++;
                tablecurrenttd.hide(); //remove();  
                tablefirsttd.attr("rowSpan", tableSpanNum);
            } else {
                tablefirsttd = $(this);
                tableSpanNum = 1;
            }
        }
    });
}

function Current(id, url, list) {
    var r = false;
    if ((location.pathname + '?').indexOf(url + '?') == 0)  r = true;
    if (!r) {
        $.each(list, function (item) {
            if (item.parentId == id && (location.pathname + '?').indexOf(item.url + '?') == 0) {
                r = true;
                return false;
            }
        });
    }
    return r;
}
function Enum(type, key) {
    r = '';
    key = parseInt(key);
    switch (type) {
        case 'status':
            switch (key) {
                case 1:
                    r = '启用';
                    break;
                default:
                    r = '禁用';
                    break;
            }
            break;
        case 'messageType':
            switch (key) {
                case 1:
                    r = '文字型';
                    break;
                default:
                    r = '图文型';
                    break;
            }
            break;
        case 'matchType':
            switch (key) {
                case 1:
                    r = '精确匹配';
                    break;
                default:
                    r = '模糊匹配';
                    break;
            }
            break;
        default:
            r = '';
            break;
    }
    return r;
}

function RadioCheck(obj) {
    var id = $(obj).prop('id');
    $('#' + id).parent().parent().find('label').each(function (index, item) {
        $(item).prop('class', $(item).prop('id') == id ? 'checked' : '');
    });
}
function RadioGetValue(id) {
    var r = '';
    $('label[id^=' + id + ']').parent().parent().find('label').each(function (index, item) {
        if ($(item).prop('class') == 'checked') r = $(item).prop('id').replace(/[^0-9]/ig, '');

    });
    return r;
}

function PopupShow(classname) {
    $(classname).fadeIn().prepend('');
    var popMargTop = ($(classname).height()) / 2;
    var popMargLeft = ($(classname).width() + 80) / 2;
    $(classname).css({
        'margin-top': -popMargTop,
        'margin-left': -popMargLeft
    });
    var div = $('<div/>').prop({'class': 'divpop'}).appendTo($('body'));
    div.css({'filter': 'alpha(opacity=65)'}).fadeIn();
}
function PopupHide(classname) {
    $('.divpop, ' + classname).fadeOut(function () {
        $('.divpop').remove();
    });
}

function getAllCheck() {
    var list = '';

    $('input[id^=Check_]').each(function () {
        if ($(this).prop('checked'))
            list = list + (list == '' ? '' : ',') + $(this).val();
    })

    return list;
}


function tableCheck(source) {
    var selected = 0, count = 0;
    if ($(source).val() < 0) {
        $('input[id^=CheckAll]').each(function () {
            $(this).prop('checked', $(source).prop('checked'));
        });
        $('input[id^=Check_]').each(function () {
            $(this).prop('checked', $(source).prop('checked'));
        });
    }
    else {
        $(source).prop('checked', !$(source).prop('checked'));
        $('input[id^=Check_]').each(function () {
            if ($(this).prop('checked') == true)
                selected++;
            count++;
        })
        $('input[id^=CheckAll]').each(function () {
            $(this).prop('checked', selected == count);
        });
    }
}
function GetPublicAccountId() {
    var r = '';
    var reg = /(\?|&)pid=(\d+)(&|$)/gi;
    var arr;
    if ((arr = reg.exec(location.href)) != null) {
        r = arr[2];
    }
    return r;
}
function GetUrlByPid(url, id) {
    if (id == null) id = GetPublicAccountId();
    return url + (id == '' ? '' : (url.indexOf('?') == -1 ? '?' : '&') + 'pid=' + GetPublicAccountId());
}


function popupModify(popID, popURL) {


    //Pull Query & Variables from href URL
    var query = popURL.split('?');
    var dim = query[1].split('&');
    var popWidth = dim[0].split('=')[1]; //Gets the first query string value

    //Fade in the Popup and add close button
    $('#' + popID).fadeIn().css({ 'width': Number(popWidth) }).prepend('');
    var div = $('<div/>').prop({'class': 'divpop'}).appendTo($('body'));
    div.css({'filter': 'alpha(opacity=80)'}).fadeIn();
    //Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
    var popMargTop = ($('#' + popID).height() ) / 2;
    var popMargLeft = ($('#' + popID).width() + 80) / 2;

    //Apply Margin to Popup
    $('#' + popID).css({
        'margin-top': -popMargTop,
        'margin-left': -popMargLeft
    });

}
function popupclose() {
    $('#DIVgb , .mask-div , .mask-div600').fadeOut(function () {
        $('#DIVgb, a.close').remove();
    });
    $(".divpop").remove();
}
function ValidLength(str,max,min) {
    var len=str.replace(/[^\x00-\xff]/g,"***").length;
    var r=(typeof(min)=='undefined'?true:min<=len) && len<=max;
    return r;
}