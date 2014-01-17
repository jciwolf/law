<?php
$this->layout()->addJs('operator.js');
$this->layout()->addJs('plugins/jquery.xpagination.js');
$this->layout()->addJs('plugins/jquery.xpopup.js');
$this->layout()->addJs('plugins/jquery.simpleValidate.js');
$this->layout()->addJsCode(

	(empty($operatorId) ? 'var operatorId=0;' : "var operatorId=$operatorId;")
);

if (!empty($operatorModel)) {
	$code = "var operatorModel=" . json_encode($operatorModel) . ";";
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
			<li><label for="">邮件地址：</label><span><input type="text" id="search_email" class="inpWidth150"></span></li>

			<li id="list-box-bth" style="margin-top:10px;"><input type="button" value="搜索" class="x-green_but queryTrigger"></li>
		</ul>
	</div>

	<div class="query-boxR">
		<a href="" id="newoperator"><img src="/images/add.png" alt="" align="absmiddle" style="margin-right:15px;">新增操作员</a>
	</div>

</div>
<!--条件查询 end-->
<div class="clear"><div id="search_navigation">邮箱地址包含<span></span>的搜索结果</div></div>
<!--数据列表 start-->

<div class="list-table">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_gzh">
		<tr id="header">
			<th width="20%">代理商名称</th>
			<th width="15%">邮箱</th>
			<th width="10%">启用状态</th>
			<th width="32%">操作</th>
		</tr>

		<tr class="lasttr">
			<th colspan="7" align="left">
				<ul class="list_bottom">

				</ul>
			</th>
		</tr>
	</table>

	<!--数据列表 end-->

<div class="mask-div600" id="modify_operator">
	<h3>修改</h3>

	<form id="form1" class="required-form">
		<!--商家添加 start-->
		<div class="universal">
			<ul>
				<li><label for="">账号名称：</label><span><input type="text" id="namepop" mid="<?= $operatorId ?>"
															class="inpWidth160 required operatorName"></span></li>
				<li><label for="">邮箱：</label><span><input type="text" id="emailpop" mid="<?= $operatorId ?>"
															 class="inpWidth160 required  operatorEmail"></span>
					<!--	<span class="a-x"><a href="">获取方法</a></span> -->
				</li>
				<li><label for="">手机号：</label><span><input type="text" id="mobilepop"
															  class="inpWidth160 required mobile"></span></li>
				<li><label for="">QQ号：</label><span><input type="text" id="qqpop"
															  class="inpWidth160 required qq"></span>
				</li>
				<li><label for="">密码：</label><span><input type="password" id="passwordpop" class="inpWidth160 required pass"></span>
				</li>
				<li><label for="">重复密码：</label><span><input type="password" for='passwordpop' id="passwordpop2"  class="compare_password required"></span></li>

				<li><label for="">启动状态：</label>
					<span>
						<select id="statuspop" class="ui_element inpWidth150">
							<option value="1">开启</option>
							<option value="2" selected>关闭</option>
						</select>
					</span>
				</li>

				<li><label for="">所属代理商：</label>
					<span>
						<select id="distributorpop" class="ui_element inpWidth150">
							<?php if(!empty($distributorList)) {
								foreach ($distributorList as $d) {?>

							<option value="<?=$d->id?>"><?=$d->name?></option>
							<?php }} ?>
						</select>
					</span>
				</li>
			</ul>
		</div>
		<!--商家添加 end-->
		<div class="x-bthDIV">
			<input type="submit" id="saveButton" value="保　存" class="x-green_but">　
			<input type="button" id="cancelButton" value="取　消" class="gray_but">
		</div>
	</form>

</div>