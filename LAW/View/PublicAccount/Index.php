<?php
$this->layout()->addJs('publicAccount.js');
$this->layout()->addJs('plugins/jquery.xpagination.js');
$this->layout()->addJs('plugins/jquery.xpopup.js');
//echo json_encode($replyModel);
$this->layout()->addJsCode((empty($merchantId) ? 'var merchantId=0;' : "var merchantId=$merchantId;"));
// move from addorupdate.php
$this->layout()->addJs('publicAccountAddOrUpdate.js');
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


?>

<?php XP_Lib_Partial::includes('Navigation'); ?>
<!--条件查询 start-->
<div class="query-box">
	<div class="query-boxL">
		<ul>
			<li><label for="">公众号名称：</label><span><input type="text" id="name" class="inpWidth150"></span></li>
			<li><label for="">微信号：</label><span><input type="text" id="weixin" class="inpWidth150"></span></li>
			<li><label for="">有效时间：</label><span><input type="text" id="beginDate" class="inpWidth120 date-x"> — <input
						type="text" id="endDate" class="inpWidth120 date-x"></span>
			</li>
			<li><label for="">所属商家：</label><span><input type="text" id="merchantName"
														class="inpWidth150"></span></li>
			<li>
				<label for="">公众号类型：</label>
                <span class="inpWidth105">
                    <select id="type" class="ui_element inpWidth150">
						<option value="0" selected>全部</option>
						<option value="1">订阅号</option>
						<option value="2">服务号</option>
					</select>
                </span>

			</li>
			<li><label for="">启动状态：</label>
                <span>
                    <select id="status" class="ui_element inpWidth150">
						<option value="0" selected>全部</option>
						<option value="1">启用</option>
						<option value="2">停用</option>
					</select>
                </span>
			</li>
			<li id="list-box-bth"><input type="button" value="搜　索" class="x-green_but queryTrigger"></li>
		</ul>
	</div>
	<div class="query-boxR">
		<a href="" id="newPublicAccount" title="#?w=800" class="poplight" data-reveal-id="modify_Public">
			<img src="/images/add.png" alt="" align="absmiddle" style="margin-right:15px;">
			新增公众号
		</a>
	</div>
    <div class="clear"></div>
</div>
<!--条件查询 end-->
<div class="clear"></div>
<!--数据列表 start-->
<div class="list-table">
	<table id="ResultList" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_gzh">
		<tr>
			<th width="6%">
				<label class="check-one">
					<span>选择</span>
				</label>
			</th>
			<th width="10%">公众号名称</th>
			<th width="14%">微信号</th>
			<th width="10%">所属商家</th>
			<th width="10%">开始时间</th>
			<th width="10%">结束时间</th>
			<th width="8%">启用状态</th>
			<th width="32%">操作</th>
		</tr>


		<tr class="lasttr">
			<th colspan="8" align="left">
				<ul class="list_bottom">

				</ul>
			</th>
		</tr>
	</table>
</div>
<!--数据列表 end-->

<div class="mask-div600" id="modify_Public">
	<form id="form1" class="required-form">
		<h3>新增公众号</h3>

		<!--基本信息添加 start-->
		<div class="universal_1">
			<ul id="male">
				<li><p>公众号名称：</p><span><input type="text" id="namepop" name="namepop" pid="<?= $pid ?>"
											  class="inpWidth160 required wexinname"></span></li>
				<li><p>公众号原始ID：</p><span><input type="text" id="originalId" name="originalId" pid="<?= $pid ?>"
												class="inpWidth160 required originalId"></span><span class="a-x lh28"><a
							href=""></a></span></li>
				<li><p>微信号：</p><span><input type="text" id="weixinpop" pid="<?= $pid ?>"
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
				<li id="CUSTOMER">
					<p>客服接口：</p>
                <span>
                   <label id="customerservice1" class="" for="customer_service_1" onclick="RadioCheck(this)">有</label>
                </span>
                <span>
                  <label id="customerservice2" class="" for="customer_service_2" onclick="RadioCheck(this)"> 无</label>
                </span>
				</li>
				<li><p>有效时间：</p><span><input id="beginDateInputpop" type="text"
											 class="inpWidth120 required date-x"> — <input
							type="text" id="endDateInputpop" class="inpWidth120 required date-x compare_date"
							compareto="beginDateInputpop"></span></li>
				<li>
					<p>启动状态：</p>
                    <span>
                        <select class="ui_element inpWidth150" id="statuspop">
							<option value="1" >启用</option>
							<option value="2" selected>关闭</option>
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
				<li><p>Token：</p><span><input type="text" id="token"
											  value="<?php echo empty($wxtoken) ? "" : $wxtoken ?>"
											  class="Width460 required"></span></li>
				<li><p>AppId：</p><span><input type="text" id="AppId" class="inpWidth160 required "></span></li>
				<li><p>AppSecret：</p><span><input type="text" id="AppSecret" class="inpWidth160 required "></span>
				</li>
			</ul>
		</div>
		<!--接口配置信息 end-->
		<div class="x-bthDIV">
			<input type="submit" id="saveButton" value="保　存" class="x-green_but">　
			<input type="button" id="cancelButton" value="取　消" class="gray_but">
		</div>
	</form>
</div>