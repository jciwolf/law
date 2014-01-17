<?php
	$uri = $_SERVER['REQUEST_URI'];
?>
<div class="list-nav">
	<ul id="navigation-bar">
        <!--
		<li <?php echo $uri == '/publicAccount/index/' ? 'class="nac"' : '' ?> data-url="/publicAccount/index/">公众号管理 html demo</li>
		<li <?php echo $uri == '/distributor/index/' ? 'class="nac"' : '' ?> data-url="/distributor/index/">代理商管理</li>
		<li <?php echo $uri == '/merchant/index/' ? 'class="nac"' : '' ?>  data-url="/merchant/index/">商家管理</li>
		<li <?php echo $uri == '/operator/index/' ? 'class="nac"' : '' ?>  data-url="/operator/index/">操作员管理</li>
		<li <?php echo $uri == '/profile/index/' ? 'class="nac"' : '' ?>  data-url="/profile/index/">我的资料</li>
		-->
	</ul>
	<div class="clear"></div>
</div>

<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        var accountId=<?=$_SESSION['id']?>,type=1;
        ConsumeObject('/login/permission', { accountId: accountId, type: type }, PermissionResult);
    });
    function PermissionResult(r) {
        var menu=$('#navigation-bar');
        $('#navigation-bar li').each(function () {$(this).remove();});
        if (r.errcode == '0') {
            //data=jQuery.parseJSON(r.data);
            $.each(r.data, function (index, item) {
                if(item.isVisible!='1') return;
                var li,span;
                if(item.parentId=='0') {
                    li=$('<li/>').attr({'class': Current(item.id,item.url, r.data)?'nac':'','data-url':GetUrlByPid(item.url)}).appendTo(menu);
                    li.prop('innerHTML',item.alias!=''?item.alias:item.name);
                }
            });
        }
        BindDataClick();
    }
</script>