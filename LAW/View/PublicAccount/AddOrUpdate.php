<?php
$this->layout()->addJs('publicAccountAddOrUpdate.js');
$this->layout()->addJs('plugins/jquery.xpopup.js');
$this->layout()->addJs('plugins/jquery.simpleValidate.js');
$pid = 0;
$this->layout()->addJsCode(
	"var publicAccount_systemId=$systemAccountId;"
);

if (!empty($publicAccountModel)) {
	$pid  = $publicAccountModel->id;
	$code = "var publicAccountModel=" . json_encode($publicAccountModel) . ";";
	$this->layout()->addJsCode(
		$code
	);

}
$this->layout()->addJscode("var pid=$pid;");

//echo json_encode($replyModel);
?>
<form id="form1" class="required-form">
	<?php XP_Lib_Partial::includes('Navigation'); ?>
	<!--基本信息添加 start-->
	<div class="universal_1">
		<h4 class="path-H">基本信息：</h4>
		<ul id="male">
			<li><p>公众号名称：</p><span><input type="text" id="name" name="name" pid="<?= $pid ?>"
										  class="inpWidth160 required wexinname"></span></li>
			<li><p>公众号原始ID：</p><span><input type="text" id="originalId" name="originalId" pid="<?= $pid ?>"
											class="inpWidth160 required originalId"></span><span class="a-x lh28"><a
						href=""></a></span></li>
			<li><p>微信号：</p><span><input type="text" id="weixin" pid="<?= $pid ?>"
										class="inpWidth160 required weixin"></span></li>
			<li id="kind">
				<p>公众号类型：</p>
                <span>
                    <label id="weixintype1" class="" for="wexin_type_1" onclick="RadioCheck(this)">订阅号</label>
                </span>
                <span>
                   <label id="weixintype2" class="" for="wexin_type_1" onclick="RadioCheck(this)"> 服务号</label>
                </span>
			</li>
			<li id="WXpay">
				<p>微信支付：</p>
                <span>
                   <label id="weixinpay1" class="" for="weixin_pay_1" onclick="RadioCheck(this)">已开通</label>
                </span>
                <span>
                  <label id="weixinpay2" class="" for="weixin_pay_2" onclick="RadioCheck(this)"> 未开通</label>
                </span>
			</li>
			<li><p>有效时间：</p><span><input id="beginDateInput" type="text" class="inpWidth120 required date-x"> — <input
						type="text" id="endDateInput" class="inpWidth120 required date-x"></span></li>
			<li>
				<p>启动状态：</p>
            <span class="inpWidth105">
                <select class="ui_element" id="status">
					<option value="1" selected>启用</option>
					<option value="2">关闭</option>
				</select>
            </span>
			</li>
		</ul>
	</div>
	<!--基本信息添加 end-->
	<!--接口配置信息 start-->
	<div class="universal_1">
		<h4 class="path-H">接口配置信息：</h4>
		<ul>
			<li><p>URL：</p><span class="lh28"><?php echo empty($wxurl) ? "" : $wxurl ?></span></li>
			<li><p>Token：</p><span><input type="text" id="token" value="<?php echo empty($wxtoken) ? "" : $wxtoken ?>"
										  class="inpWidth460 required"></span></li>
		</ul>
	</div>
	<!--接口配置信息 end-->
	<div class="x-bthDIV">
		<input type="submit" id="saveButton" value="保存" class="x-green_but">
		<input type="button" id="cancelButton" value="取消" class="gray_but">
	</div>
</form>

