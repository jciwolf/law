<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        Query();
    });
    function Query() {
        ConsumeObject('/systemaccount/selfinfo', {}, QueryResult);
    }
    function QueryResult(r) {
        if (r.errcode == '0') {
            $('#LableName').prop('innerHTML', r.data.name);
            $('#LableEmail').prop('innerHTML',r.data.email);
            $('#LableMobile').prop('innerHTML',r.data.mobile);
            $('#LableQQ').prop('innerHTML',r.data.qq);
            $('#Name').prop('innerHTML', r.data.name);
            $('#Email').prop('innerHTML',r.data.email);
            $('#Mobile').val(r.data.mobile);
            $('#QQ').val(r.data.qq);
            $('#Password').val('');
        }
    }
    function Update() {
        PopupShow('.mask-div600');
        $('.mask-div600 .x-bthDIV .x-green_but').prop({'disabled':false,'value':'确　认'})
        .unbind('click').click(function () {
            $("#Result").prop({'class':'success','innerHTML':''});
            if(!/^1\d{10}$/gi.test($.trim($('#Mobile').val())))
                $("#Result").prop({'class':'fail','innerHTML':'请输入正确的手机号'});
            else if(!/^\d+$/gi.test($.trim($('#QQ').val())))
                $("#Result").prop({'class':'fail','innerHTML':'请输入正确的QQ号'});
            else if(!($.trim($('#Password').val())=='' || 2<=$.trim($('#Password').val()).length && $.trim($('#Password').val()).length<=16))
                $("#Result").prop({'class':'fail','innerHTML':'请输入合理的密码，2~16个字符'});
            else　{
                $('.mask-div600 .x-bthDIV .x-green_but').prop({'disabled':true,'value':'保存中···'});
                ConsumeObject('/systemaccount/updateselfinfo', {Mobile: $.trim($('#Mobile').val()),QQ: $.trim($('#QQ').val()),Password: $.trim($('#Password').val())}, UpdateResult);
            }
        });
        $('.mask-div600 .x-bthDIV .gray_but').unbind('click').click(function () {PopupHide('.mask-div600');});
    }
    function UpdateResult(r) {
        if (r.errcode == '0') {
            $('.mask-div600 .x-bthDIV .x-green_but').prop({'disabled':true,'value':'操作成功'});
            setTimeout(function () { PopupHide('.mask-div600');Query(); }, 1000);
        }
        else {
            $('.mask-div .x-bthDIV .x-green_but').prop({'disabled':false,'value':'操作失败，请重试','title': 'errcode:'+ r.errcode+',errmsg:'+r.errmsg});
        }
    }
</script>
<!--已登录 start-->
<div class="login-already">
    <!--我的资料 start-->
    <div class="loginInfo-box">
        <h2>我的资料</h2>
        <ul>
            <li><label for="">名称：</label><span id="LableName"></span></li>
            <li><label for="">Email：</label><span id="LableEmail"></span></li>
            <li><label for="">手机：</label><span id="LableMobile"></span></li>
            <li><label for="">QQ：</label><span id="LableQQ"></span></li>
            <li><label for="">角色：</label><span></span></li>
        </ul>
        <div class="clear loggin-bth" align="right">
            <input type="button" value="修　改" class="gray_but" onclick="Update();">
        </div>
    </div>
    <!--我的资料 end-->
    <?php XP_Lib_Partial::includes('SystemDashboard'); ?>
</div>
<!--已登录 end-->
<!--修改资料 start-->
<div class="mask-div600" style="display: none;">
    <h3>我的资料修改</h3>
    <div class="universal">
        <ul>
            <li><label for="">名称：</label><span id="Name"></span></li>
            <li><label for="">Email：</label><span id="Email"></span></li>
            <li><label for="">手机：</label><span><input type="text" class="inpWidth160" id="Mobile"/></span></li>
            <li><label for="">QQ：</label><span><input type="text" class="inpWidth160" id="QQ"/></span></li>
            <li><label for="">密码：</label><span><input type="password" class="inpWidth160" id="Password" title="不需要修改密码时，请不要输入内容"/></span><span class="tips">不修改密码时此框可不输入</span></li>
            <li><label for="">角色：</label><span></span></li>
        </ul>
    </div>
    <div class="clear"></div>
    <div class="x-bthDIV"><input type="button" value="确　认" class="x-green_but"/>　<input type="button" value="取　消" class="gray_but"/><span id="Result"></span></div>
</div>
<!--修改资料 end-->