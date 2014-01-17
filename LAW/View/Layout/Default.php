<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>X平台</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="Shortcut Icon" type="image/x-icon" href="http://imgcache.qq.com/vipstyle/tuan/gaopeng/img/favicon.ico" />
    <link href="/css/style.css?v=<?= $web_version; ?>" rel="stylesheet" type="text/css" />
    <script src="/js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="/js/Gaopeng.Utility.js?<?= $web_version; ?>" type="text/javascript"></script>
    <script src="/js/xp.core.js" type="text/javascript"></script>
    <?php foreach ($cssArr as $css) { ?>
        <link rel="stylesheet" type="text/css" href="/css/<?= $css; ?>"/>
    <? } ?>
    <?php foreach ($jsArr as $js) { ?>
        <script type="text/javascript" src="/js/<?= $js; ?>"></script>
    <? } ?>
    <?php if (!empty($jsCodeArr)): ?>
        <script type="text/javascript">
            <?php foreach ($jsCodeArr as $code) { ?>
            <?=$code."\n";?>
            <? } ?>
        </script>
    <?php endif; ?>
</head>
<body class="x-body">
<?php XP_Lib_Partial::includes('Header'); ?>
<section>
    <?php echo $content; ?>
    <!--服务项目 start-->
    <div class="function-box">
        <div class="function-title">
            <h2 class="">功能介绍</h2>
        </div>
        <!--服务内容 start-->
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon1"></h3></span>
            <span class="function-txt">
            	<h2>微网站</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <!--服务内容 end-->
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon2"></h3></span>
            <span class="function-txt">
            	<h2>微信自定义菜单</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon3"></h3></span>
            <span class="function-txt">
            	<h2>微信会员卡</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon4"></h3></span>
            <span class="function-txt">
            	<h2>微信大转盘</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon5"></h3></span>
            <span class="function-txt">
            	<h2>微信刮刮卡</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon6"></h3></span>
            <span class="function-txt">
            	<h2>微信优惠券</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon7"></h3></span>
            <span class="function-txt">
            	<h2>微投票</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon8"></h3></span>
            <span class="function-txt">
            	<h2>微商城</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon9"></h3></span>
            <span class="function-txt">
            	<h2>微信LBS位置服务</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon10"></h3></span>
            <span class="function-txt">
            	<h2>微相册</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon11"></h3></span>
            <span class="function-txt">
            	<h2>微订单</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="function-div">
            <span class="function-icon"><h3 class="login-icon12"></h3></span>
            <span class="function-txt">
            	<h2>微统计</h2>
                <p>快速简历一个精美的企业手机网，展示企业相关信息，让微信公众号的信息展示得更加丰富更加完善，吸引更多的粉丝关注。</p>
            </span>
        </div>
        <div class="clear"></div>
    </div>
    <!--服务项目 end-->
</section>
<footer class="x-footer">
    <div class="footer-box">
        <ul>
            <li><img src="/images/2wm.jpg"></li>
            <li class="x-address">
                <address>
                    北京网罗天下生活科技有限公司版权所有<br />
                    地址;北京网罗天下生活科技有限公司<br />
                    西大望路通惠国际传媒广场2号楼<br />
                    电话：<br />
                    京ICP备2079366号-1
                </address>
            </li>
            <li class="footer-a">
                <a href="">乐享首页</a><br />
                <a href="">关于我们</a><br />
                <a href="">加盟合作</a><br />
                <a href="">账号管理</a><br />
            </li>
            <li class="footer-a">
                <a href="">功能介绍</a><br />
                <a href="">配置接口</a><br />
                <a href="">相关自费</a><br />
                <a href="">帮助教程</a><br />
                <a href="">论坛</a>
            </li>
            <li class="footer-a ml-40">
                <span>QQ在线咨询</span><br />
				<!--   <a href=""><img src="/images/QQ1.jpg"></a><br />
				<a href=""><img src="/images/QQ2.jpg"></a>
				-->
			</li>
			<li class="footer-a ml-40">
				<span>加盟合作咨询</span><br />
			  <!--  王经理：123 <a href=""><img src="/images/QQ1.jpg"></a><br />
				李经理：123123 <a href=""><img src="/images/QQ2.jpg"></a>
				-->
            </li>
        </ul>
        <div class="clear"></div>
    </div>
</footer>
</body>
</html>