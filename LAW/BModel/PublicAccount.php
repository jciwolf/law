<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-11-5
 * Time: 下午2:42
 * To change this template use File | Settings | File Templates.
 */

class XP_BModel_PublicAccount
{
	public static function getPublicAccount($id, $originalId = null)
	{
		$dao = XP_Model_PublicAccount::dataAccess();
		if (!empty($id)) $dao->filter(XP_Model_PublicAccount::ID, $id);
		if (!empty($originalId)) $dao->filter(XP_Model_PublicAccount::ORIGINAL_ID, $originalId);

		return $dao->findOne();
	}
    public static function publicAccountStatus($originalId)
    {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        $model = XP_Model_PublicAccount::dataAccess()
            ->filter(XP_Model_PublicAccount::ORIGINAL_ID, $originalId)
            ->findOne();
        if(empty($model)) {
            $errcode="1001";
            $errmsg="公众号不存在";
        }
        elseif(!(strtotime($model->beginDate)<=strtotime(date("Y-m-d H:i:s")) && strtotime(date("Y-m-d H:i:s"))<=strtotime($model->endDate))) {
            $errcode="1002";
            $errmsg="公众号不在有效期内";
        }
        elseif($model->status != 1) {
            $errcode="1003";
            $errmsg="公众号被禁用";
        }
        return json_decode(XP_Lib_Utility::jsonResult($errcode,$errmsg,$data),true);
    }

	public function info($id)
	{
		$errcode = "0";
		$errmsg  = "操作成功";
		$data    = "";
		try {
			$data = XP_Model_PublicAccount::dataAccess()->filterByOp(XP_Model_PublicAccount::ID, '=', $id)->findOne();
		} catch (Exception $e) {
			$errcode = "9999";
			$errmsg  = sprintf("get public account error,params:%s,message:%s", json_encode(func_get_args()), $e->getMessage());
		}

		return XP_Lib_Utility::jsonResult($errcode, $errmsg, $data);
	}

	public static function save($model)
	{
		if ($model instanceof XP_BModel_PublicAccount) {
			$model = get_object_vars($model);
		}

		if (!is_array($model)) {
			throw new Exception("the model must be a array ");
		}
		extract($model);

		//var_dump($model);
		$dao = XP_Model_PublicAccount::dataAccess();
		if (!empty($name)) {
			$dao->setField(XP_Model_PublicAccount::NAME, $name);
		}
		if (!empty($originalId)) {
			$dao->setField(XP_Model_PublicAccount::ORIGINAL_ID, $originalId);
		}
		if (!empty($weixin)) {
			$dao->setField(XP_Model_PublicAccount::WEIXIN, $weixin);
		}
		if (!empty($systemAccountId)) {
			$dao->setField(XP_Model_PublicAccount::SYSTEM_ACCOUNT_ID, $systemAccountId);
		}
		if (!empty($weixinPay)) {
			$dao->setField(XP_Model_PublicAccount::WEIXIN_PAY, $weixinPay);
		}
		if (!empty($customerService)) {
			$dao->setField(XP_Model_PublicAccount::CUSTOMER_SERVICE, $customerService);
		}
		if (!empty($beginDate)) {
			$dao->setField(XP_Model_PublicAccount::BEGIN_DATE, $beginDate);
		}

		if (!empty($endDate)) {
			$dao->setField(XP_Model_PublicAccount::END_DATE, $endDate);
		}
		if (!empty($url)) {
			$dao->setField(XP_Model_PublicAccount::URL, $url);
		}
        if (!empty($token)) {
            $dao->setField(XP_Model_PublicAccount::TOKEN, $token);
        }
        if (!empty($appId)) {
            $dao->setField(XP_Model_PublicAccount::APP_ID, $appId);
        }
        if (!empty($appSecret)) {
            $dao->setField(XP_Model_PublicAccount::APP_SECRET, $appSecret);
        }
		if (!empty($note)) {
			$dao->setField(XP_Model_PublicAccount::NOTE, $note);
		}

		if (!empty($status)) {
			$dao->setField(XP_Model_PublicAccount::STATUS, $status);
		}

		if (!empty($type)) {
			$dao->setField(XP_Model_PublicAccount::TYPE, $type);
		}

		if (!empty($operator)) {
			$dao->setField(XP_Model_PublicAccount::OPERATOR, $operator);
		}

		if (!empty($updateTime)) {
			$dao->setField(XP_Model_PublicAccount::UPDATE_TIME, $updateTime);
		}

		if (!empty($id)) {

			$dao->filter(XP_Model_PublicAccount::ID, $id);

			return $dao->update();
		} else {
			if (!empty($createTime)) {
				$dao->setField(XP_Model_PublicAccount::CREATE_TIME, $createTime);
			}

			if ($dao->insert()) {
				$newid = $dao->lastInsertId();
				//add default permissible
				self::addDefaultPermission($newid);

				return $newid;
			}
		}

		return false;

	}

	public static function delete($id)
	{
		if (!is_int($id)) {
			throw new Exception('');
		}

		$dao = XP_Model_PublicAccount::dataAccess();
		$r   = $dao->filter(XP_Model_PublicAccount::ID, $id)
			->delete();

		return $r;
	}

	public static function batchDelete($ids)
	{
		if (is_array($ids)) {
			//delete config
			return XP_Model_PublicAccount::dataAccess()
				->filter(XP_Model_PublicAccount::ID, $ids)
				->delete();

		}
	}

	public static function batchUpdateStatus($ids, $status, $operator)
	{

		$r = XP_Model_PublicAccount::dataAccess()
			->filter(XP_Model_PublicAccount::ID, $ids)
			->setField(XP_Model_PublicAccount::STATUS, $status)
			->setField(XP_Model_PublicAccount::OPERATOR, $$operator)
			->setField(XP_Model_PublicAccount::UPDATE_TIME, date('Y-m-d H:i:s'))
			->update();

		return $r;
	}

	public static function addDefaultPermission($accountId)
	{
		$sql   = "insert into permission(`accountId`, `moduleId`, `note`, `status`, `type`, `operator`, `createTime`, `updateTime`)"
			. "select a.id,b.id,'',2,1,0,now(),now()"
			. "from public_account a join module b where b.type=2 and a.id=?";
		$value = array();
		if (!empty($accountId)) {
			$value[] = $accountId;
		}

		$r = XP_Model_PublicAccount::dataAccess()->nativeSql($sql, $value);

		if ($r) {
			return true;
		}

	}

	public static function get($systemAccountName = '', $name = '', $weixin = '', $beginDate = 0, $endDate = 0, $type = 0, $status = 0, $rows = 30, $page = 1)
	{
		//var_dump("ksdjfksdjfksdf");
		$args = func_get_args();
		//var_dump($args);
		if (empty($args)) {
			throw new Exception('at least require one parameter');
		}

		$sql   = "SELECT  `p`.`id` , p.`name` as pulicAccountName , p.`weixin` , s.`name` as merchantName ,  `p`.`beginDate` ,  `p`.`endDate` ,  `p`.`status` FROM `public_account` AS `p` JOIN `system_account` AS `s` ON `p`.`systemAccountId`=`s`.id ";
		$sql2  = "SELECT  COUNT( * ) as c  FROM `public_account` AS `p` JOIN `system_account` AS `s` ON `p`.`systemAccountId`=`s`.id ";
		$where = $value = array();
		if (!empty($systemAccountName)) {
			$where[] = "`s`.`name` like ?";
			$value[] = "%" . $systemAccountName . "%";
		}
		if (!empty($name)) {
			$where[] = "`p`.`name` like ?";
			$value[] = "%" . $name . "%";
		}
		if (!empty($weixin)) {
			$where[] = "`p`.`weixin`like ?";
			$value[] = "%" . $weixin . "%";
		}

        $where[] = "not (ifnull(?,`p`.`beginDate`)<`p`.`beginDate` or `p`.`endDate`<ifnull(?,`p`.`endDate`))";
        $value[] = $endDate;
        $value[] = $beginDate;

		if (!empty($type)) {
			$where[] = "`p`.`type`=?";
			$value[] = $type;
		}
		if (!empty($status)) {
			$where[] = "`p`.`status`=?";
			$value[] = $status;
		}
		$sql .= "WHERE ";
		$sql2 .= "WHERE ";
		if (!empty($where)) {
			$sql .= join(' AND ', $where) . " \n";
			$sql2 .= join(' AND ', $where) . " \n";
		} else {
			$sql .= "1=1 \n";
			$sql2 .= "1=1 \n";
		}
		$sql .= "ORDER BY `p`.`id` DESC \n";
		$start = $rows * ($page - 1);
		$sql .= "LIMIT " . $rows;
		$sql .= " OFFSET " . $start;

        //var_dump($sql);
        //var_dump($value);

		$r     = XP_Model_PublicAccount::dataAccess()->nativeSql($sql, $value);
		$count = XP_Model_PublicAccount::dataAccess()->nativeSql($sql2, $value);

		//var_dump($count);

		return array(
			'count' => $count[0]['c'],
			'list'  => $r
		);

	}

	public static function checkExist($weixinName, $id)
	{
		if (!empty($weixinName)) {
			$r = XP_Model_PublicAccount::dataAccess()
				->filter(XP_Model_PublicAccount::NAME, $weixinName)
				->findOne();
			if ($r->id == $id)
				return false;

		}

		return $r;
	}

	public static function checkOriginalExist($originalId, $id)
	{
		if (!empty($originalId)) {
			$r = XP_Model_PublicAccount::dataAccess()
				->filter(XP_Model_PublicAccount::ORIGINAL_ID, $originalId)
				->findOne();
			if ($r->id == $id)
				return false;

		}

		return $r;
	}

	public static function checkWeixinExist($weixin, $id)
	{
		if (!empty($weixin)) {
			$r = XP_Model_PublicAccount::dataAccess()
				->filter(XP_Model_PublicAccount::WEIXIN, $weixin)
				->findOne();
			if ($r->id == $id)
				return false;

		}

		return $r;
	}
}