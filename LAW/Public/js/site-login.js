/**
 * Created with JetBrains PhpStorm.
 * User: kevin
 * Date: 13-11-13
 * Time: 上午10:12
 * To change this template use File | Settings | File Templates.
 */

function Login() {
    $('#Result').html('');
    $('#Result').attr('title', '');
    if ($('#Username').val() == '') {
        $('#Result').html('请输入登录名');
        $('#Username').focus();
    }
    else if ($('#Password').val() == '') {
        $('#Result').html('请输入密码');
        $('#Password').focus();
    }
    else {
        $("#Submit").attr('disabled', 'disabled');
        $("#Submit").html('登录中···');
        ConsumeObject('/login/process', { Username: $('#Username').val(), Password: $('#Password').val() }, LoginResult);
    }
}
function LoginResult(r) {
    $("#Password").focus();
    if (r.errcode != '0') {
        var message = "";
        $('#Result').attr('title', decodeURIComponent(r.errmsg));
        //$("#ValidateImage").click();
        switch (r.errcode) {
            case '1001':
                message = '用户名不正确，请重新输入';
                $("#Username").focus();
                break;
            case '1002':
                message = '密码不正确，请重新输入';
                $("#Password").focus();
                break;
            case '1003':
                message = '账号已过期';
                $("#Password").focus();
                break;
            case '1004':
                message = '账号被禁用';
                $("#Password").focus();
                break;
            default:
                message = '登陆失败，请重试';
                break;
        }
        $('#Result').html(message);
        $("#Submit").html('登 录');
        $("#Submit").removeAttr('disabled');
    }
    else {
        $("#Submit").html('登录成功');
        $(location).attr('href', '<?= $redirectUrl; ?>');
    }
}
function ValidateImageChange() {
    $('#ValidateImage').attr('src', '/ValidateImage?' + Math.random());
    return false;
}
$(document).ready(function () {
    //$('#ValidateImage').click();
    $('#Username').val(GetCookie('LoginInfoHistory', 'lastName'));
    if ($('#Username').val() == '')
        $('#Username').focus();
    else
        $('#Password').focus();
});