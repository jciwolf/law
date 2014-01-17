<?php

class XP_Controller_Ajax_Keyword extends XP_Controller_BasePublic
{
    public function listAction()
    {
        $publicAccountId = System_Lib_App::app()->getRequest('PublicAccountId', System_Lib_Request::TYPE_INT);
        $name = System_Lib_App::app()->getRequest('Name', System_Lib_Request::TYPE_STRING);
        $type = System_Lib_App::app()->getRequest('Type', System_Lib_Request::TYPE_STRING);
        $matchType = System_Lib_App::app()->getRequest('MatchType', System_Lib_Request::TYPE_STRING);
        $status = System_Lib_App::app()->getRequest('Status', System_Lib_Request::TYPE_STRING);
        $pageSize = System_Lib_App::app()->getRequest('PageSize', System_Lib_Request::TYPE_INT);
        $pageNo = System_Lib_App::app()->getRequest('PageNo', System_Lib_Request::TYPE_INT);

        $bll = new XP_BModel_Keyword();
        $result=$bll->getPage($publicAccountId,$name,$type,$matchType,$status,$pageSize,$pageNo);
        print_r($result);
    }
    public function detailAction()
    {
        $publicAccountId = System_Lib_App::app()->getRequest('PublicAccountId', System_Lib_Request::TYPE_INT);
        $id = System_Lib_App::app()->getRequest('Id', System_Lib_Request::TYPE_STRING);
        $bll = new XP_BModel_Keyword();
        $result=$bll->info($id);
        print_r($result);
    }
    public function addAction()
    {
        $r='';
        $model=$this->getParam();
        $model->note='';
        $model->createTime=date("Y-m-d H:i:s");
        $bll = new XP_BModel_Keyword();

        $keywordList=split(';',$model->secondaryName);
        array_splice($keywordList,0,0,$model->name);
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        $list=$bll->keywordExist($model->publicAccountId,$keywordList);
        if(!empty($list)) {
            $errcode="2001";
            $errmsg='关键词重复';
            $data='';
            foreach($list as $item)
                $data.=sprintf("%s%s",empty($data)?'':',',$item['name']);
            $r=XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
        }
        else
            $r=$bll->save($model);
        print_r($r);
    }
    public function updateAction()
    {
        $r='';
        $model=$this->getParam();
        $bll = new XP_BModel_Keyword();

        $keywordList=split(';',$model->secondaryName);
        array_splice($keywordList,0,0,$model->name);
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        $list=$bll->keywordExist($model->publicAccountId,$keywordList,$model->id);
        if(!empty($list)) {
            $errcode="2001";
            $errmsg='关键词重复';
            $data='';
            foreach($list as $item)
                $data.=sprintf("%s%s",empty($data)?'':',',$item['name']);
            $r=XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
        }
        else
            $r=$bll->save($model);
        print_r($r);
    }
    private function getParam() {
        $id = System_Lib_App::app()->getRequest('Id', System_Lib_Request::TYPE_INT);
        $publicAccountId = System_Lib_App::app()->getRequest('PublicAccountId', System_Lib_Request::TYPE_INT);
        $name = System_Lib_App::app()->getRequest('Name', System_Lib_Request::TYPE_STRING);
        $secondaryName = System_Lib_App::app()->getRequest('SecondaryName', System_Lib_Request::TYPE_STRING);
        $type = System_Lib_App::app()->getRequest('Type', System_Lib_Request::TYPE_INT);
        $matchType = System_Lib_App::app()->getRequest('MatchType', System_Lib_Request::TYPE_INT);
        $status = System_Lib_App::app()->getRequest('Status', System_Lib_Request::TYPE_INT);
        $content = System_Lib_App::app()->getRequest('Content', System_Lib_Request::TYPE_STRING);

        XP_Lib_Utility::log($content);

        $model = new XP_Model_Keyword();
        $model->id=$id;
        $model->publicAccountId=$publicAccountId;
        $model->name=$name;
        $model->secondaryName=$secondaryName;
        $model->matchType=$matchType;
        $model->content=$content;
        $model->status=$status;
        $model->type=$type;
        $model->operator=0;
        $model->updateTime=date("Y-m-d H:i:s");
        return $model;
    }
    public function updateSecondaryNameAction()
    {
        $r='';
        $id = System_Lib_App::app()->getRequest('Id', System_Lib_Request::TYPE_INT);
        $secondaryName = System_Lib_App::app()->getRequest('SecondaryName', System_Lib_Request::TYPE_STRING);

        $model= XP_Model_Keyword::dataAccess()->filterByOp(XP_Model_Keyword::ID,'=',$id)->findOne();
        $model->secondaryName=$secondaryName;
        $model->operator=0;
        $model->updateTime=date("Y-m-d H:i:s");

        $bll = new XP_BModel_Keyword();

        $keywordList=split(';',$model->secondaryName);
        array_splice($keywordList,0,0,$model->name);
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        $list=$bll->keywordExist($model->publicAccountId,$keywordList,$model->id);
        if(!empty($list)) {
            $errcode="2001";
            $errmsg='关键词重复';
            $data='';
            foreach($list as $item)
                $data.=sprintf("%s%s",empty($data)?'':',',$item['name']);
            $r=XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
        }
        else
            $r=$bll->save($model);
        print_r($r);
    }
    public function statusMultiAction()
    {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        $list = System_Lib_App::app()->getRequest('Id', System_Lib_Request::TYPE_STRING);
        $status = System_Lib_App::app()->getRequest('Status', System_Lib_Request::TYPE_INT);
        foreach(split(',',$list) as $id) {
            $result=json_decode($this->changeStatus($id,$status),true);
            if($result['errcode']!='0'){
                $errcode=$result['errcode'];
                $errmsg=$result['errmsg'];
                $data=$result['data'];
                break;
            }
        }
        print_r(XP_Lib_Utility::jsonResult($errcode,$errmsg,$data));
    }
    public function statusAction()
    {
        $id = System_Lib_App::app()->getRequest('Id', System_Lib_Request::TYPE_INT);
        $status = System_Lib_App::app()->getRequest('Status', System_Lib_Request::TYPE_INT);
        print_r($this->changeStatus($id,$status));
    }
    private function changeStatus($id,$status)
    {
        $model= XP_Model_Keyword::dataAccess()->filterByOp(XP_Model_Keyword::ID,'=',$id)->findOne();
        $model->status=$status;
        $model->operator=0;
        $model->updateTime=date("Y-m-d H:i:s");

        $bll = new XP_BModel_Keyword();
        return $bll->save($model);
    }

    public function deleteMultiAction()
    {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        $list = System_Lib_App::app()->getRequest('Id', System_Lib_Request::TYPE_STRING);
        foreach(split(',',$list) as $id) {
            $result=json_decode($this->delete($id),true);
            if($result['errcode']!='0'){
                $errcode=$result['errcode'];
                $errmsg=$result['errmsg'];
                $data=$result['data'];
                break;
            }
        }
        print_r(XP_Lib_Utility::jsonResult($errcode,$errmsg,$data));
    }
    public function deleteAction()
    {
        $id = System_Lib_App::app()->getRequest('Id', System_Lib_Request::TYPE_INT);
        print_r($this->delete($id));
    }
    private function delete($id)
    {
        $bll = new XP_BModel_Keyword();
        return $bll->delete($id);
    }
}
