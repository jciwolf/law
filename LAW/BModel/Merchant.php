<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-11-7
 * Time: 上午11:41
 * To change this template use File | Settings | File Templates.
 */
class XP_BModel_Merchant extends XP_BModel_SystemAccount
{

	protected static $type = 3;

	public static function checkMerchantNameExist($merchantName, $id)
	{
		//var_dump($merchantName);
		if (!empty($merchantName)) {
			$r = XP_Model_SystemAccount::dataAccess()
				->filter(XP_Model_SystemAccount::NAME, $merchantName)
				->findOne();
			//var_dump($r);
			if (empty($r))
				return false;

			if ($r->id == $id)
				return false;

		}

		return $r;
	}

	public static function checkMerchantEmailExist($merchantEmail, $id)
	{
		if (!empty($merchantEmail)) {
			$r = XP_Model_SystemAccount::dataAccess()
				->filter(XP_Model_SystemAccount::EMAIL, $merchantEmail)
				->findOne();
			if (empty($r))
				return false;

			if ($r->id == $id)
				return false;

		}

		return $r;
	}

}