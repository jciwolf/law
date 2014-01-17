<div class="x-CustomerInformation">
    <!--头像遮罩 start-->
    <div class="Customer-logo">
        <!--商户logo-->
        <img src="/images/default_logo.png" width="106" height="106">
        <!--遮罩图层-->
        <img src="/images/Picture-bg.png" class="Picture-bg">
    </div>
    <!--头像遮罩 end-->
    <p><span id="PublicAccountName"><!--真功夫连锁店--></span><span class="green_498"><a href="/publicAccount/index/">【切换】</a></span></p>
</div>
<dl class="x-menu-l php-publicaccount-nav" id="Menu">
    <!--
	<dt><span>基础回复 html demo</span></dt>
	<dd <?php echo preg_match('/^\/reply\/follow\/\d+\//', $_SERVER['REQUEST_URI']) ? 'class="cur"' : '' ?> data-url="/reply/follow/<?php echo $P_publicAccountId ?>/"><span>关注回复</span></dd>
	<dd <?php echo preg_match('/^\/reply\/default\/\d+\//', $_SERVER['REQUEST_URI']) ? 'class="cur"' : '' ?> data-url="/reply/default/<?php echo $P_publicAccountId ?>/"><span>默认回复</span></dd>
	<dt><span>自定义菜单</span></dt>
	<dd <?php echo preg_match('/^\/wxmenu\/\d+\//', $_SERVER['REQUEST_URI']) ? 'class="cur"' : '' ?>  data-url="/wxmenu/<?php echo $P_publicAccountId ?>/"><span>自定义菜单</span></dd>
	<dt><span>关键词管理</span></dt>
	<dd <?php echo preg_match('/^\/keyword\/add\/\d+\//', $_SERVER['REQUEST_URI']) ? 'class="cur"' : '' ?>  data-url="/keyword/add/<?php echo $P_publicAccountId ?>/"><span>新建关键词</span></dd>
	<dd <?php echo preg_match('/^\/keyword\/list\/\d+\//', $_SERVER['REQUEST_URI']) ? 'class="cur"' : '' ?>  data-url="/keyword/list/<?php echo $P_publicAccountId ?>/"><span>关键词列表</span></dd>
	<dt><span>用户回复消息</span></dt>
	<dd <?php echo preg_match('/^\/user\/replies\/\d+\//', $_SERVER['REQUEST_URI']) ? 'class="cur"' : '' ?>  data-url="/user/replies/<?php echo $P_publicAccountId ?>/"><span>用户回复消息列表</span></dd>
    -->
</dl>
<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        //var accountId=<?=$_SESSION['id']?>,type=1;
        var id=GetPublicAccountId(),type=2;
        if (id != '') {
            ConsumeObject('/publicAccount/info', { Id: id}, LayoutPublicAccountResult);
        }
        ConsumeObject('/login/permission', { accountId: id, type: type }, PermissionResult);
    });
    function LayoutPublicAccountResult(r) {
        if (r.errcode == '0') {
            $('#PublicAccountName').prop('innerHTML', r.data.name);
        }
    }
    function PermissionResult(r) {
        var id=GetPublicAccountId();
        var menu=$('#Menu');
        $('#Menu dt').each(function () {$(this).remove();});
        $('#Menu dd').each(function () {$(this).remove();});
        if (r.errcode == '0') {
            //data=jQuery.parseJSON(r.data);
            $.each(r.data, function (index, item) {
                if(item.isVisible!='1') return;
                var d,span;
                if(item.parentId=='0') {
                    d=$('<dt/>').appendTo(menu);
                }
                else {
                    d=$('<dd/>').attr({'class': Current(item.id,item.url, r.data)?'cur':'','data-url':GetUrlByPid(item.url)}).appendTo(menu);
                }
                span=$('<span/>').prop('innerHTML',item.alias!=''?item.alias:item.name).appendTo(d);
            });
        }
        BindDataClick();
    }
</script>