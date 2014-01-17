<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>X平台</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/style.css?<?= $web_version; ?>"/>
    <link rel="Shortcut Icon" type="image/x-icon" href="http://imgcache.qq.com/vipstyle/tuan/gaopeng/img/favicon.ico" />
    <link href="/css/style.css?v=<?= $web_version; ?>" rel="stylesheet" type="text/css"/>
	<script src="/js/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script src="/js/Gaopeng.Utility.js?<?= $web_version; ?>" type="text/javascript"></script>
	<script src="/js/xp.core.js" type="text/javascript"></script>
	<script src="/js/jquery-ui.js"></script>
	<script src="/js/plugins/i18n/jquery.ui.datepicker-zh-CN.js"></script>
	<link rel="stylesheet" href="/css/jquery-ui.css"/>
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
<section class="x-section">
	<?php XP_Lib_Partial::includes('SystemNavigation'); ?>
	<div class="list-box">
		<?= $content ?>
	</div>
</section>
</body>
</html>