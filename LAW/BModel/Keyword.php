<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-30
 * Time: 上午10:50
 * To change this template use File | Settings | File Templates.
 */

class XP_BModel_Keyword extends System_Lib_Controller {
    /**
     * @return 匹配上的关键词记录（按时间倒序，取第一个）
     * @param $originalId：公众号ID
     * @param $keyword：关键字
     */
    public function getUniqueKeyword($originalId,$keyword) {
        $sql = "select a.*
                from keyword a
                inner join keyword_list b on a.id=b.keywordId and b.name like concat(case a.matchType when 1 then '' else '%' end,?,case a.matchType when 1 then '' else '%' end)
                inner join public_account c on a.publicAccountId=c.id and c.originalId=? and a.status=1
                order by a.createTime desc,a.id desc
                limit 1";
        $param = array($keyword,$originalId);
        $obj  = XP_Model_Keyword::dataAccess();
        $result = $obj->nativeSql($sql, $param);
        if(!empty($result)) $result=$result[0];
        return $result;
    }
    /*查询重复的关键词*/
    public function keywordExist($publicAccountId,$keywordList,$id=0) {
        $param = array($publicAccountId,$id);
        $sql='1=0';
        foreach($keywordList as $item) {
            $param[]=$item;
            $sql.=' or b.name=?';
        }
        $sql = "select b.name
                from keyword a
                inner join keyword_list b on a.id=b.keywordId and a.publicAccountId=? and a.id!=? and ($sql)";
        $obj  = XP_Model_Keyword::dataAccess();
        $result = $obj->nativeSql($sql, $param);
        return $result;
    }

    public function getPage($publicAccountId,$name,$type,$matchType,$status,$pageSize,$pageNo) {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        try {
            $count=$this->getCount($publicAccountId,$name,$type,$matchType,$status);
            $list=$this->getList($publicAccountId,$name,$type,$matchType,$status,$pageSize,$pageNo);
            $data['amount']=$count;
            $data['list']=$list;
        }
        catch(Exception $e) {
            $errcode="9999";
            //$errmsg=sprintf("get keyword page error,publicAccountId:$publicAccountId,name:$name,type:$type,matchType:$matchType,status:$status,pageSize:$pageSize,pageNo:$pageNo,message:%s",$e->getMessage());
            $errmsg=sprintf("get keyword page error,params:%s,message:%s",json_encode(func_get_args()),$e->getMessage());
        }
        return XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
    }
    private function getCount($publicAccountId,$name,$type,$matchType,$status)
    {
        $sql='select count(1) amount from keyword a where 1=1';
        $param = $this->param($sql,$publicAccountId,$name,$type,$matchType,$status);
        $obj  = XP_Model_Keyword::dataAccess();
        $list=$obj->nativeSql($sql, $param);
        return $list[0]['amount'];
    }
    private function getList($publicAccountId,$name,$type,$matchType,$status,$pageSize,$pageNo)
    {
        $sql='select * from keyword a where 1=1';
        $param = $this->param($sql,$publicAccountId,$name,$type,$matchType,$status);
        $sql.=sprintf(' order by a.createTime desc,a.id desc limit %d,%d',($pageNo-1)*$pageSize,$pageSize);
        $obj  = XP_Model_Keyword::dataAccess();
        return $obj->nativeSql($sql, $param);
    }
    private function param(&$sql,$publicAccountId,$name,$type,$matchType,$status) {
        $param = array();
        if($publicAccountId!='') {
            $param[]=$publicAccountId;
            $sql.=' and a.publicAccountId=?';
        }
        if($name!='') {
            $param[]='%'.$name.'%';
            $param[]='%'.$name.'%';
            $sql.=' and (a.name like ? or a.secondaryName like ?)';
        }
        if($type!='') {
            $param[]=$type;
            $sql.=' and a.type=?';
        }
        if($matchType!='') {
            $param[]=$matchType;
            $sql.=' and a.matchType=?';
        }
        if($status!='') {
            $param[]=$status;
            $sql.=' and a.status=?';
        }
        return $param;
    }

    public function info($id) {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        try {
            $data= XP_Model_Keyword::dataAccess()->filterByOp(XP_Model_Keyword::ID,'=',$id)->findOne();
        }
        catch(Exception $e) {
            $errcode="9999";
            $errmsg=sprintf("get keyword page error,params:%s,message:%s",json_encode(func_get_args()),$e->getMessage());
        }
        return XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
    }
    public function delete($id) {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        $data=XP_Model_Keyword::dataAccess()->filterByOp(XP_Model_Keyword::ID,'=',$id)->delete();
        if($data==0) {
            $errcode="2001";
            $errmsg=sprintf("delete keyword error,params:%s,message:%s",json_encode(func_get_args()),'error not captured');
        }
        return XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
    }
    public function save($model) {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        try {
            $dao=XP_Model_Keyword::dataAccess();
            if($model->id==0) {
                $dao
                    ->setField(XP_Model_Keyword::PUBLIC_ACCOUNT_ID, $model->publicAccountId)
                    ->setField(XP_Model_Keyword::NOTE, $model->note)
                    ->setField(XP_Model_Keyword::CREATE_TIME, $model->createTime);
            }
            else {
                $dao->filterByOp(XP_Model_Keyword::ID,'=',$model->id);
            }
            $dao
                ->setField(XP_Model_Keyword::NAME, $model->name)
                ->setField(XP_Model_Keyword::SECONDARY_NAME, $model->secondaryName)
                ->setField(XP_Model_Keyword::MATCH_TYPE, $model->matchType)
                ->setField(XP_Model_Keyword::CONTENT, $model->content)
                ->setField(XP_Model_Keyword::STATUS, $model->status)
                ->setField(XP_Model_Keyword::TYPE, $model->type)
                ->setField(XP_Model_Keyword::OPERATOR, $model->operator)
                ->setField(XP_Model_Keyword::UPDATE_TIME, $model->updateTime);

            if($model->id==0) {
                $data=$dao->insert();
                $id=$dao->lastInsertId();
            }
            else {
                $data=$dao->update();
                $id=$model->id;
            }
            if($data==0) {
                $errcode="2001";
                $errmsg=sprintf("save or update keyword error,params:%s,message:%s",json_encode(func_get_args()),'error not captured');
            }
            else {
                /*-------------------keyword list maintain begin--------------------------*/
                $list=XP_Model_KeywordList::dataAccess()->filterByOp(XP_Model_KeywordList::KEYWORD_ID,'=',$id)->find();
                $arr=split(';',$model->secondaryName);
                array_splice($arr,0,0,$model->name);
                //if(!empty($list)) {
                    foreach($list as $item) {
                        $find=false;
                        foreach($arr as $item1) {
                            if($item->name==$item1) {
                                $find=true;break;
                            }
                        }
                        if(!$find)XP_Model_KeywordList::dataAccess()->filterByOp(XP_Model_KeywordList::ID,'=',$item->id)->delete();
                    }
                    foreach($arr as $item1) {
                        $find=false;
                        foreach($list as $item) {
                            if($item->name==$item1) {
                                $find=true;break;
                            }
                        }
                        if(!$find) XP_Model_KeywordList::dataAccess()
                            ->setField(XP_Model_KeywordList::KEYWORD_ID,$id )
                            ->setField(XP_Model_KeywordList::NAME,$item1 )
                            ->setField(XP_Model_KeywordList::NOTE,'' )
                            ->setField(XP_Model_KeywordList::STATUS,1 )
                            ->setField(XP_Model_KeywordList::TYPE,1)
                            ->setField(XP_Model_KeywordList::CREATE_TIME, date("Y-m-d H:i:s"))
                            ->setField(XP_Model_KeywordList::UPDATE_TIME, date("Y-m-d H:i:s"))
                            ->insert();
                    }
                //}
                /*-------------------keyword list maintain end--------------------------*/
            }
        }
        catch(Exception $e) {
            $errcode="9999";
            $errmsg=sprintf("save or update keyword error,params:%s,message:%s",json_encode(func_get_args()),$e->getMessage());
        }
        return XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
    }
}