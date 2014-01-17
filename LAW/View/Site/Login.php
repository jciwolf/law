<script type="text/javascript">
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
            $("#Submit").val('登录中···');
            ConsumeObject('/login/process', { Username: $('#Username').val(), Password: $('#Password').val(),Remember:$('#Remember').prop('checked') }, LoginResult);
        }
    }
    function LoginResult(r) {
        $("#Password").focus();
        if (r.errcode != '0') {
            var message = "";
            $('#Result').prop('title', decodeURIComponent(r.errmsg));
            //$("#ValidateImage").click();
            switch (r.errcode) {
                case '1001':
                    message = '用户名不正确';
                    $("#Username").focus();
                    break;
                case '1002':
                    message = '密码不正确';
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
            $("#Submit").val('登 录');
            $("#Submit").removeAttr('disabled');
        }
        else {
            $("#Submit").val('登录成功');
            $(location).attr('href', '<?= $redirectUrl; ?>');
        }
    }
    function ValidateImageChange() {
        $('#ValidateImage').prop('src', '/ValidateImage?' + Math.random());
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
</script>
<!--登录 start-->
<div class="x-login">
    <div class="x-loginBOX">
        <div class="login-BG">
            <form>
            <div>
                <ul>
                    <li class="login-title">登 录</li>
                    <li class="login-inp">
                        <span><input id="Username" type="text" value="" class="login-id"></span>
                        <span><input id="Password" type="password" value="" class="login-password"></span>
                    </li>
                    <li class="login_password">
                        <span class="fl"><input type="checkbox" id="Remember"><label for="Remember" title="勾选后，七天内不用再登录">七天内免登录</label></span>
                        <a href="javascript:void(0);" class="fr"><!--无法登录？--></a>
                    </li>
                    <li class="clear"><input id="Submit" type="submit" onclick="Login();return false;" value="登 录" class="x-green_lenght" style="padding:"><span class="fail" id="Result"></span></li>
                </ul>
            </div>
            </form>
        </div>
    </div>
</div>
<!--登录 end-->