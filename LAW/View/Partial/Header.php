<div class="x-header">
    <div class="headerDIV">
        <h1 class="x-logo"><a href="/"><img src="/images/x_logo.png"/></a></h1>
        <?php if (!empty($_SESSION)) { ?>
            <div class="h-other">
                <ul>
                    <li>欢迎回来，<?php echo array_key_exists('name', $_SESSION) ? $_SESSION['name'] : ''; ?></li>
                    <li><a href="/" class="a-info">管理中心</a></li>
                    <li class="last"><a href="/logout" class="a-close">退出</a></li>
                </ul>
            </div>
        <?php } ?>
    </div>
</div>