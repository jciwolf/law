<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>X平台</title>

	<meta name="description" content="">
	<meta name="keywords" content="">

	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="Shortcut Icon" type="image/x-icon" href="http://imgcache.qq.com/vipstyle/tuan/gaopeng/img/favicon.ico" />
    <link rel="stylesheet" href="/css/style.css?<?= $web_version; ?>"/>
	<link rel="stylesheet" name="addStyle" id="addStyle" href=""/>
	<?php foreach ($cssArr AS $css => $arg) { ?>
		<link rel="stylesheet" charset="utf-8" href="/css/<?= $arg; ?>" type="text/css" media="screen"/>
	<? } ?>
	<script type="text/javascript" charset="utf-8" src="/js/jquery-1.10.2.min.js"></script>
	<script src="/js/Gaopeng.Utility.js?<?= $web_version; ?>" type="text/javascript"></script>
	<script src="/js/xp.core.js" type="text/javascript"></script>

	<script src="/js/jquery-ui.js"></script>
	<link rel="stylesheet" href="/css/jquery-ui.css"/>
	<script src="/js/plugins/i18n/jquery.ui.datepicker-zh-CN.js"></script>
	<!--
	<script type="text/javascript" src="/js/jquery.scrollablecombo.js"></script>
	<script>
		$(function() {
			//$('.ui_element').scrollablecombo();
		});
	</script>
	-->
	<?php
	function startsWith($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}

	foreach ($jsArr as $js) {
		if (startsWith($js, "http://")) {
			?>
			<script type="text/javascript" src="<?= $js; ?>"></script>
		<?php
		}
		else
		{
		?>
			<script type="text/javascript" src="/js/<?= $js; ?>"></script>

		<?php
		}
	} ?>

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
<div class="x-section section_box">
	<div class="x_aside">
		<div class="x_asideDIV">
			<?php XP_Lib_Partial::includes('PublicAccountNavigation', array('publicAccountId' => 1)) ?>
		</div>
	</div>
	<div class="x_content" style="position:relative;">
		<?= $content ?>
	</div>
    <div class="clear"></div>
</div>
<footer></footer>
</body>
</html>