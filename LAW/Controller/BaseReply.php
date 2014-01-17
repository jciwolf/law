<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jerry Qian (QianXufeng)
 * Date: 13-10-30
 * Time: 上午10:21
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_BaseReply extends XP_Controller_Base
{
	protected $layoutName = 'XP_Layout_Public';

	/*
	public function baseReplyAction()
	{
		$type            = System_Lib_App::get("type", System_Lib_Request::TYPE_STRING, "");
		$PublicAccountId = System_Lib_App::get("pid", System_Lib_Request::TYPE_INT, 0);

		$replyModel = XP_BModel_Reply::getReply($type, $PublicAccountId);
		$this->assignData("replyModel", $replyModel);
		$this->assignData("type", $type);
		$this->assignData("PublicAccountId", $PublicAccountId);
		$this->render('BaseReply');
	}
	*/

	public function replyAction()
	{

		$type            = System_Lib_App::get("type", System_Lib_Request::TYPE_STRING, "");
		$PublicAccountId = System_Lib_App::get("pid", System_Lib_Request::TYPE_INT, 0);
		//var_dump($type);
		if ($type == "follow") {
			$itype = XP_BModel_Reply::REPLY_TYPE_FOLLOW;
		} else if ($type == "default") {
			$itype = XP_BModel_Reply::REPLY_TYPE_DEFAULT;
		} else {
			exit('invalidate parameter');
		}

		if (empty($PublicAccountId)) {
			exit("invalidate parameter");
		}
        $image=System_Lib_App::app()->getConfig('attachment');
		$replyModel         = XP_BModel_Reply::getReply($itype, $PublicAccountId);
		$publicAccountModel = XP_BModel_PublicAccount::getPublicAccount($PublicAccountId);
		$this->assignData("replyModel", $replyModel);
		$this->assignData("type", $itype);
		$this->assignData("mediaHost", $image['path']);
		$this->assignData("PublicAccountId", $PublicAccountId);
		$this->assignData("publicAccountModel", $publicAccountModel);
		$this->render('PublicAccount/BaseReply');
	}

	public function  TestAction()
	{
		$this->layoutName = null;
		echo 'query test ....';
		$r = XP_BModel_Reply::getReply(1);
		if ($r instanceof System_Lib_DataObject) {
			echo 'query test ok ';
			//var_dump($r);
		} else {
			echo 'query test failed';
		}

		echo "\n";

		$r = array('text' => "html"
		);

		$s = array(
			array(
				'Title'       => 't',
				'Description' => 't',
				'PicUrl'      => 'd',
				'Url'         => "http://sian.com",
			),

		);

		//XP_BModel_Reply::save($r, 1, 1);
		//XP_BModel_Reply::save($s, 2, 2);
		//echo dirname("/data/develop/qianxuefeng/v2/trunk/XP/Public/abc/abc.txt");
		//mkdir(dirname("/data/develop/qianxuefeng/v2/trunk/XP/View/abc/abc.txt"));
		//System_Lib_File::createDir(dirname("/data/develop/qianxuefeng/v2/trunk/XP/Public/abc/abc.txt"));

		$server = System_Lib_App::app()->getConfig('redis');
		//var_dump(System_Lib_Redis::Set("qiantestkey", "ksjdkfsjdf", $server, false, 36000 - 100));

		//var_dump(System_Lib_Redis::Get("qiantestkey", $server, false));
		$o = System_Lib_AES::encrypt("abc");
		var_dump($o);
		$w = System_Lib_AES::decrypt($o);

	}

}

?>