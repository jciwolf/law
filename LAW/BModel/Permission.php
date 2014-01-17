<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-30
 * Time: 上午10:50
 * To change this template use File | Settings | File Templates.
 */

class XP_BModel_Permission extends System_Lib_Controller {

    /**
     * @return 匹配上的关键词记录（按时间倒序，取第一个）
     * @param $originalId：公众号ID
     * @param $keyword：关键字
     */
    public function permissionList($accountId,$type) {
        $sql = "select c.*
                from permission a
                inner join module b on a.moduleId=b.id and a.accountId=? and b.type=?
                left join module c on b.id=c.parentid or b.id=c.id
                where b.parentId=0
                order by b.showIndex,b.id,c.parentId,c.showIndex,c.id";
        $param = array($accountId,$type);
        $obj  = XP_Model_Permission::dataAccess();
        $result = $obj->nativeSql($sql, $param);
        return $result;
    }
}