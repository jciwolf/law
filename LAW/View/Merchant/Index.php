<?php
$this->layout()->addJs('merchant.js');
$this->layout()->addJs('plugins/jquery.xpagination.js');
$this->layout()->addJs('plugins/jquery.xpopup.js');
$this->layout()->addJsCode(
	"var distributorId=$distributorId;"
);


$this->layout()->addJs('merchantAddOrUpdate.js');
$this->layout()->addJs('plugins/jquery.simpleValidate.js');

$this->layout()->addJsCode(

	(empty($merchantId) ? 'var merchantId=0;' : "var merchantId=$merchantId;")
);

if (!empty($merchantModel)) {
	$code = "var merchantModel=" . json_encode($merchantModel) . ";";
	$this->layout()->addJsCode(
		$code
	);

}
?>


<?php XP_Lib_Partial::includes('Navigation'); ?>
<!--条件查询 start-->
<div class="query-box">
	<div class="query-boxL">
		<ul>
			<li><label for="">商家名称：</label><span><input type="text" id="name" class="inpWidth150"></span></li>
			<li><label for="">代理商名称：</label><span><input type="text" id="distributor" class="inpWidth150"></span></li>
			<li><label for="">有效时间：</label><span><input type="text" class="inpWidth120 date-x" id="beginDate"> — <input
						type="text" id="endDate" class="inpWidth120 date-x"></span></li>
			<li><label for="">启用状态：</label>
                <span>
                    <select id="status">
						<option value="0">全部</option>
						<option value="1">启用</option>
						<option value="2">关闭</option>
					</select>

                </span>
			</li>
			<li id="list-box-bth"><input type="button" value="搜　索" class="x-green_but queryTrigger"></li>
		</ul>
	</div>
	<div class="query-boxR">
		<a href="/merchant/add/" id="newMerchant"><img src="/images/add.png" alt="" align="absmiddle"
													   style="margin-right:15px;">新增商家</a>
	</div>
    <div class="clear"></div>
</div>
<!--条件查询 end-->
<div class="clear"></div>
<!--数据列表 start-->
<div class="list-table">
	<table width="100%" border="0" cellspacing="0" style="position: relative" cellpadding="0" class="table_gzh">
		<tr id="header">
			<th width="6%">
				<label class="check-one">
					<span>选择</span>
				</label>
			</th>
			<th width="16%">商家名称</th>
			<th width="16%">所属代理商</th>
			<th width="12%">开始时间</th>
			<th width="12%">结束时间</th>
			<th width="8%">启用状态</th>
			<th width="30%">操作</th>
		</tr>

		<tr class="lasttr">
			<th colspan="7" align="left">
				<ul class="list_bottom">

				</ul>
			</th>
		</tr>
	</table>
</div>
<!--数据列表 end-->

<div class="mask-div600" id="modify_Merchant">
	<h3>修改</h3>

	<form id="form1" class="required-form">
		<!--商家添加 start-->
		<div class="universal">
			<ul>
				<li><label for="">商家名称：</label><span><input type="text" id="namepop" mid="<?= $merchantId ?>"
															class="inpWidth160 required merchantName"></span></li>
				<li><label for="">联系人邮箱：</label><span><input type="text" id="emailpop" mid="<?= $merchantId ?>"
															 class="inpWidth160 required  merchantEmail"></span>
					<!--	<span class="a-x"><a href="">获取方法</a></span> -->
				</li>
				<li><label for="">联系人手机号：</label><span><input type="text" id="mobilepop"
															  class="inpWidth160 required mobile"></span></li>
				<li><label for="">联系人QQ号：</label><span><input type="text" id="qqpop"
															  class="inpWidth160 required qq"></span>
				</li>
				<li><label for="">密码：</label><span><input type="password" id="passwordpop" class="inpWidth160 required"></span>
				</li>
				<li><label for="">重复密码：</label><span><input type="password" for='passwordpop' id="passwordpop2"
															class="inpWidth160 required compare_password"></span></li>
				<li>
					<label for="">有效时间：</label><span><input id="beginDateInputpop" type="text"
															class="inpWidth120 date-x required"> — <input
							id="endDateInputpop" type="text" class="inpWidth120 date-x required compare_date"
							compareto="beginDateInputpop"></span>
				</li>
				<li><label for="">启动状态：</label>
            <span>
                <select id="statuspop" class="ui_element inpWidth150">
					<option value="1">开启</option>
					<option value="2" selected>关闭</option>
				</select>
            </span>
			</ul>
		</div>
		<!--商家添加 end-->
		<div class="x-bthDIV">
			<input type="submit" id="saveButton" value="保　存" class="x-green_but">　
			<input type="button" id="cancelButton" value="取　消" class="gray_but">
		</div>
	</form>

</div>