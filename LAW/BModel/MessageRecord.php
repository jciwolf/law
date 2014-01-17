<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-31
 * Time: 下午3:58
 * To change this template use File | Settings | File Templates.
 */

class XP_BModel_MessageRecord
{
	public static function save($model)
	{
        return XP_Model_MessageRecord::dataAccess()
            ->setField(XP_Model_MessageRecord::FROM_USER, $model->fromUser)
            ->setField(XP_Model_MessageRecord::TO_USER, $model->toUser)
            ->setField(XP_Model_MessageRecord::MESSAGE_TYPE, $model->messageType)
            ->setField(XP_Model_MessageRecord::CONTENT, $model->content)
            ->setField(XP_Model_MessageRecord::NOTE, $model->note)
            ->setField(XP_Model_MessageRecord::STATUS, $model->status)
            ->setField(XP_Model_MessageRecord::TYPE, $model->type)
            ->setField(XP_Model_MessageRecord::OPERATOR, $model->operator)
            ->setField(XP_Model_MessageRecord::CREATE_TIME, $model->createTime)
            ->setField(XP_Model_MessageRecord::UPDATE_TIME, $model->updateTime)
            ->insert();
	}
}
?>