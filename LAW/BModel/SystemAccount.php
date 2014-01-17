<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-11-7
 * Time: 上午11:41
 * To change this template use File | Settings | File Templates.
 */
class XP_BModel_SystemAccount extends System_Lib_Controller
{

	protected static $type;
	 const TYPE_GAOPENG=1;
	 const TYPE_DISTRIBUTOR=2;
	 const TYPE_OPERATOR=3;

	public static function getSystemAccount($name = "", $email = "")
	{
		/*
		return XP_Model_SystemAccount::dataAccess()
			->filter(!empty($name)?XP_Model_SystemAccount::NAME:XP_Model_SystemAccount::EMAIL, !empty($name)?$name:$email)
			->findOne();
		*/
		$dao = XP_Model_SystemAccount::dataAccess();
		if (!empty($name)) $dao->filter(XP_Model_SystemAccount::NAME, $name);
		if (!empty($email)) $dao->filter(XP_Model_SystemAccount::EMAIL, $email);

		return $dao->findOne();
	}

	public static function getOne($id)
	{
		//var_dump(static::$type);
		$dao = XP_Model_SystemAccount::dataAccess();
		if (!empty($id)) $dao->filter(XP_Model_SystemAccount::ID, $id);
		if (!empty(static::$type)) $dao->filter(XP_Model_SystemAccount::TYPE, static::$type);

		return $dao->findOne();
	}
	public static function getOneById($id)
	{
		//var_dump(static::$type);
		$dao = XP_Model_SystemAccount::dataAccess();
		if (!empty($id)) $dao->filter(XP_Model_SystemAccount::ID, $id);
		return $dao->findOne();
	}


	public static function save($model)
	{
		if ($model instanceof XP_Model_SystemAccount) {
			$model = get_object_vars($model);
		}

		if (!is_array($model)) {
			throw new Exception("the model must be a array ");
		}
		extract($model);
		//var_dump($model);
		$dao = XP_Model_SystemAccount::dataAccess();
		if (!empty($name)) {
			$dao->setField(XP_Model_SystemAccount::NAME, $name);
		}
		if (!empty($email)) {
			$dao->setField(XP_Model_SystemAccount::EMAIL, $email);
		}
		if (!empty($mobile)) {
			$dao->setField(XP_Model_SystemAccount::MOBILE, $mobile);
		}

		if (!empty($qq)) {
			$dao->setField(XP_Model_SystemAccount::QQ, $qq);
		}
		if (!empty($password)) {
			$dao->setField(XP_Model_SystemAccount::PASSWORD, $password);
		}
		if (!empty($beginDate)) {
			$dao->setField(XP_Model_SystemAccount::BEGIN_DATE, $beginDate);
		}

		if (!empty($endDate)) {
			$dao->setField(XP_Model_SystemAccount::END_DATE, $endDate);
		}
		if (!empty($parentId)) {
			$dao->setField(XP_Model_SystemAccount::PARENT_ID, $parentId);
		}
		if (!empty($note)) {
			$dao->setField(XP_Model_SystemAccount::NOTE, $note);
		}

		if (!empty($status)) {
			$dao->setField(XP_Model_SystemAccount::STATUS, $status);
		}

		if (!empty(static::$type)) {
			$dao->setField(XP_Model_SystemAccount::TYPE, static::$type);
		}

		if (!empty($operator)) {
			$dao->setField(XP_Model_SystemAccount::OPERATOR, $operator);
		}

		if (!empty($createTime)) {
			$dao->setField(XP_Model_SystemAccount::CREATE_TIME, $createTime);
		}

		if (!empty($updateTime)) {
			$dao->setField(XP_Model_SystemAccount::UPDATE_TIME, $updateTime);
		}

		if (!empty($id)) {
			$dao->filter(XP_Model_SystemAccount::ID, $id);

			return $dao->update();
		} else {
			return $dao->insert();
		}

	}

	public static function delete($id)
	{
		if (!is_int($id)) {
			throw new Exception('');
		}

		$dao = XP_Model_SystemAccount::dataAccess();
		$r   = $dao->filter(XP_Model_SystemAccount::ID, $id)
			->delete();

		return $r;
	}

	public static function batchDelete($ids)
	{
		if (is_array($ids)) {
			//delete config
			return XP_Model_SystemAccount::dataAccess()
				->filter(XP_Model_SystemAccount::ID, $ids)
				->delete();

		}
	}

	public static function batchUpdateStatus($ids, $status, $operator)
	{

		$r = XP_Model_SystemAccount::dataAccess()
			->filter(XP_Model_SystemAccount::ID, $ids)
			->setField(XP_Model_SystemAccount::STATUS, $status)
			->setField(XP_Model_SystemAccount::OPERATOR, $$operator)
			->setField(XP_Model_SystemAccount::UPDATE_TIME, date('Y-m-d H:i:s'))
			->update();

		return $r;
	}

	public static function get($name = '', $distributor = '', $beginDate = 0, $endDate = 0, $status = 0, $rows = 30, $page = 1,$parentId=0,$email='')
	{
		$args = func_get_args();
		//var_dump($name);
		if (empty($args)) {
			throw new Exception('at least require one parameter');
		}

		$sql   = "SELECT  `s`.`id` ,  `s`.`name` ,  `c`.`name` as `distributor` ,  `s`.`email`,  `s`.`beginDate` ,  `s`.`endDate` ,  `s`.`status` FROM  `system_account` AS  `s` JOIN  `system_account` AS  `c` ON  `s`.parentId =  `c`.id ";
		$sql2  = "SELECT  COUNT(*) AS m FROM  `system_account` AS  `s` JOIN  `system_account` AS  `c` ON  `s`.parentId =  `c`.id ";
		$where = $value = array();
		if (!empty($name)) {
			$where[] = "`s`.`name` like ? ";
			$value[] = "%" . $name . "%";
		}
		if (!empty($distributor)) {
			$where[] = "`c`.`name` like ? ";
			$value[] = "%" . $distributor . "%";
		}

		if (!empty($beginDate)) {
			$where[] = "`s`.`beginDate`>=? ";
			$value[] = $beginDate . ' 00:00:00';
		}
		if (!empty($endDate)) {
			$where[] = "`s`.`endDate`<=? ";
			$value[] = $endDate . ' 23:59:59';
		}
		if (!empty(static::$type)) {
			$where[] = "`s`.`type`=? ";
			$value[] = static::$type;
		}
		if (!empty($status)) {
			$where[] = "`s`.`status`=? ";
			$value[] = $status;
		}
		if (!empty($parentId)) {
			$where[] = "`s`.`parentId`=? ";
			$value[] = $parentId;
		}
		if (!empty($email)) {
			$where[] = "`s`.`email` like ? ";
			$value[] ="%" . $email . "%"; ;
		}
		$sql .= "WHERE ";
		$sql2 .= "WHERE ";
		if (!empty($where)) {
			$sql .= join(' AND ', $where) . " \n";
			$sql2 .= join(' AND ', $where) . " \n";
		} else {
			$sql .= "`s`.`type`=" . static::$type . " \n";
			$sql2 .= "`s`.`type`=" . static::$type . " \n";
		}
		$sql .= "ORDER BY `s`.`id` DESC \n";
		$start = $rows * ($page - 1);
		$sql .= "LIMIT " . $rows;
		$sql .= " OFFSET " . $start;

		//var_dump($sql2);

		//var_dump($sql);

		$r     = XP_Model_SystemAccount::dataAccess()->nativeSql($sql, $value);
		$count = XP_Model_SystemAccount::dataAccess()->nativeSql($sql2, $value);

		//var_dump($count);

		return array(
			'count' => $count[0]['m'],
			'rows'  => $r
		);
	}

    public function info($id) {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        try {
            $data= XP_Model_SystemAccount::dataAccess()->filterByOp(XP_Model_SystemAccount::ID,'=',$id)->findOne();
            $data->password='';
        }
        catch(Exception $e) {
            $errcode="9999";
            $errmsg=sprintf("get systemaccount info error,params:%s,message:%s",json_encode(func_get_args()),$e->getMessage());
        }
        return XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
    }

}