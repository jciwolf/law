<?php
$this->layout()->addJs('merchantAddOrUpdate.js');
$this->layout()->addJs('plugins/jquery.xpopup.js');
$this->layout()->addJs('plugins/jquery.simpleValidate.js');

$this->layout()->addJsCode(
	"var distributorId=$distributorId;" .
	(empty($merchantId) ? 'var merchantId=0;' : "var merchantId=$merchantId;")
);

if (!empty($merchantModel)) {
	$code = "var merchantModel=" . json_encode($merchantModel) . ";";
	$this->layout()->addJsCode(
		$code
	);

}
//echo json_encode($replyModel);
?>
<?php XP_Lib_Partial::includes('Navigation'); ?>

<form id="form1" class="required-form">
	<!--商家添加 start-->
	<div class="universal">
		<ul>
			<li><label for="">商家名称：</label><span><input type="text" id="name" mid="<?= $merchantId ?>"
														class="inpWidth160 required merchantName"></span></li>
			<li><label for="">联系人邮箱：</label><span><input type="text" id="email" mid="<?= $merchantId ?>"
														 class="inpWidth160 required  merchantEmail"></span>
				<!--	<span class="a-x"><a href="">获取方法</a></span> -->
			</li>
			<li><label for="">联系人手机号：</label><span><input type="text" id="mobile"
														  class="inpWidth160 required mobile"></span></li>
			<li><label for="">联系人QQ号：</label><span><input type="text" id="qq" class="inpWidth160 required qq"></span>
			</li>
			<li><label for="">密码：</label><span><input type="password" id="password" class="inpWidth160 required"></span>
			</li>
			<li><label for="">重复密码：</label><span><input type="password" for='password' id="password2"
														class="inpWidth160 required compare_password"></span></li>
			<li><label for="">有效时间：</label><span><input id="beginDateInput" type="text"
														class="inpWidth120 date-x required"> — <input id="endDateInput"
																									  type="text"
																									  class="inpWidth120 date-x required"></span>
			</li>
			<li><label for="">启动状态：</label>
            <span>
                <select id="status">
					<option value="1">开启</option>
					<option value="2">关闭</option>
				</select>
            </span>
		</ul>
	</div>
	<!--商家添加 end-->
	<div class="x-bthDIV">
		<input type="submit" id="saveButton" value="保存" class="x-green_but">
		<input type="button" id="cancelButton" value="取消" class="gray_but">
	</div>
</form>


