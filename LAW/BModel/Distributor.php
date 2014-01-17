<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-11-7
 * Time: 上午11:41
 * To change this template use File | Settings | File Templates.
 */
class XP_BModel_Distributor extends XP_BModel_SystemAccount
{

	protected static $type = 2;

	public static function checkDistributorNameExist($DistributorName, $id)
	{
		//var_dump($DistributorName);
		if (!empty($DistributorName)) {
			$r = XP_Model_SystemAccount::dataAccess()
				->filter(XP_Model_SystemAccount::NAME, $DistributorName)
				->findOne();
			//var_dump($r);
			if (empty($r))
				return false;

			if ($r->id == $id)
				return false;

		}

		return $r;
	}

	public static function checkDistributorEmailExist($DistributorEmail, $id)
	{
		if (!empty($DistributorEmail)) {
			$r = XP_Model_SystemAccount::dataAccess()
				->filter(XP_Model_SystemAccount::EMAIL, $DistributorEmail)
				->findOne();
			if (empty($r))
				return false;

			if ($r->id == $id)
				return false;

		}

		return $r;
	}

}