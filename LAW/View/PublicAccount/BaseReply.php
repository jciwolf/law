<?php
$this->layout()->addJs('plugins/jquery.uploadify.js');
$this->layout()->addJs('ckeditor/ckeditor.js"');
$this->layout()->addJs('basereply.js');
$this->layout()->addCss('uploadify.css');

//debug($Type);
//echo json_encode($replyModel);
if (!empty($replyModel)) {

	$this->layout()->addJsCode(
		"var replyModel=" . json_encode($replyModel) . ";"
	);
} else {
	$this->layout()->addJsCode(
		"var replyModel={};"
	);
}
$this->layout()->addJsCode(
	"var publicAccountId=" . $PublicAccountId . ";" . "var mediaHost='" . $mediaHost . "'; var type=$type;"
);
?>
<?php XP_Lib_Partial::includes('Navigation'); ?>
<h4 class="path-H"><?php echo ($type == XP_BModel_Reply::REPLY_TYPE_DEFAULT) ? "默认回复" : "关注回复" ?> ：</h4>


<div class="x-list-li" id="contentContainer">

	<ol id="textView" style="display: none">
		<li><label for="">回复类型：</label><span id="replayTypeDisplay">文字型</span></li>
		<li><label for="">回复内容：</label><span id="textContent"></span></li>
	</ol>

	<ol id="edit" style="display: none">
		<li>
			<label for="">回复类型：</label>
            <span>
                <select id="replayType" class="ui_element inpWidth150">
					<option value="1">文本型</option>
					<option value="2">图文型</option>
					<option value="3">语音型</option>
					<option value="4">视频型</option>
				</select>
            </span>
		</li>
		<li><label for="">回复内容：</label><span id="newsAdd" style="display: none"><input type="button" id="newAddButton" value="添加一条" class="gray_but"></span>
			<span id="textEdit" class="ckeTXT">
                <p>最大600个字符</p>
				<textarea name="textContentTextarea" id="textContentTextarea" style="" cols="" rows="6" class="x-texttarea440"></textarea>
			<!--	<p class="x-dy">*多个用“；”隔开</p>-->
			</span>
		</li>
	</ol>
	<ul id="voiceEdit" style="display: none">
			<li><label for="">上传语音：</label>
			<span id="PreVoice"></span>
            <span id="span_voice_edit">
                <input id="voice_upload" name="voice_upload" type="file">
                <input id="voice_upload_hidden" name="voice_upload_hidden" type="hidden">
                <p class="x-dy">256K，播放长度不超过60s，支持AMR\MP3格式</p>
            </span>
		</li>
	</ul>

	<ul id="videoEdit" style="display: none">
		<li><label for="">上传视频：</label>
			<span id="PreVideo"></span>
            <span id="span_video_edit">
                <input id="video_upload" name="video_upload" type="file">
                <input id="video_upload_hidden" name="video_upload_hidden" type="hidden">
                <p class="x-dy">1MB，支持MP4格式</p>
            </span>
		</li>
	</ul>

	<ul id="newsEdit" style="display: none">
		<li><label for="">标题：</label><span><input type="text" value="" class="inpWidth170" name=""	  id="newTitleInput"></span></li>
		<li><label for="">描述：</label><span><input type="text" value="" class="inpWidth170" name=""	  id="newDescriptionInput"></span></li>
		<li><label for="">地址链接：</label><span><input type="text" value="http://" class="inpWidth460" name=""  id="newUrlInput"></span></li>
		<li><label for="">上传图片：</label>
			<span id="PreImg"></span>
            <span>
                <input id="image_upload" name="image_upload" type="file">
                <input id="image_upload_hidden" name="image_upload_hidden" type="hidden">
                <p class="x-dy">大小<5M 格式：bmp、png、jpeg、jpg、gif</p>
            </span>
		</li>
		<li><label for="">&nbsp;</label><span><input type="button" id="sureButton" value="保存此条" class="x-green_but"></span><label id="errorDisplayLabel" style="width: 200px; display:block; height:28px;" for="">&nbsp;</label></li>
	</ul>


	<div class="butbox"><input type="button" id="editButton" value="编　辑" class="x-green_but"></div>

	<div class="butbox" style="display: none"><input type="button" id="addNewsButton" value="添　加" class="x-green_but">
	</div>
	<div class="butbox" id="save_cancel_bar" style="display: none">
		<input type="button" id="saveButton" value="保　存" class="x-green_but">　
		<input type="button" id="cancelSaveButton" value="取　消" class="gray_but" style="display: none;"></div>
</div>

<div class="clear"></div>
