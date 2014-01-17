<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-31
 * Time: 下午3:58
 * To change this template use File | Settings | File Templates.
 */

class XP_BModel_Reply
{

	const REPLY_TYPE_FOLLOW       = 1;
	const REPLY_TYPE_DEFAULT      = 2;
	const REPLY_MESSAGE_TYPE_TEXT = 1;
	const REPLY_MESSAGE_TYPE_NEWS = 2;

	public static function getReply($ReplyType = self::REPLY_TYPE_DEFAULT, $publicAccountId)
	{
		$dao = XP_Model_Reply::dataAccess();

		$r = $dao
			->filter(XP_Model_Reply::REPLY_TYPE, $ReplyType)
			->filter(XP_Model_Reply::PUBLIC_ACCOUNT_ID, $publicAccountId)
			->findOne();
		//var_dump($r);
		if ($r) {
			return $r;
		} else
			return false;

	}

	public static function save($content = array(), $replyType, $type = self::REPLY_MESSAGE_TYPE_TEXT, $accountId)
	{

		if (empty($content)) {
			throw new Exception("the content parameter is required!");
		}
		if (!is_array($content)) {
			throw new Exception("the content parameter must be array!");
		}
		if($replyType==3||$replyType==4)
		{
			$content["type"]=$replyType;

		}

		$dao = XP_Model_Reply::dataAccess();
		$dao->setField(XP_Model_Reply::CONTENT, json_encode($content))
			->setField(XP_Model_Reply::NOTE, "")
			->setField(XP_Model_Reply::OPERATOR, "")
			->setField(XP_Model_Reply::REPLY_TYPE, $replyType)
			->setField(XP_Model_Reply::STATUS, 1)
			->setField(XP_Model_Reply::PUBLIC_ACCOUNT_ID, $accountId)
			->setField(XP_Model_Reply::TYPE, $type);
		$dao2 = XP_Model_Reply::dataAccess();

		try {
			if ($dao2->filter(XP_Model_Reply::REPLY_TYPE, $replyType)->filter(XP_Model_Reply::PUBLIC_ACCOUNT_ID, $accountId)->findOne()) {
				$dao->filter(XP_Model_Reply::REPLY_TYPE, $replyType)
					->filter(XP_Model_Reply::PUBLIC_ACCOUNT_ID, $accountId)
					->setField(XP_Model_Reply::UPDATE_TIME, date("Y-m-d H:i:s"))
					->setField(XP_Model_Reply::TYPE, $type)
					->update();
			} else {
				$dao->setField(XP_Model_Reply::CREATE_TIME, date("Y-m-d H:i:s"));
				$dao->insert();
			}

			return true;
		} catch (Exception $e) {

			return false;

		}

	}

}

?>