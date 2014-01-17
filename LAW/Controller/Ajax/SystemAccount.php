<?php

class XP_Controller_Ajax_SystemAccount extends System_Lib_Controller
{
    public function selfInfoAction()
    {
        $id = $_SESSION['id'];
        $bll = new XP_BModel_SystemAccount();
        $result=$bll->info($id);
        print_r($result);
    }
    public function updateSelfInfoAction()
    {
        $mobile = System_Lib_App::app()->getRequest('Mobile', System_Lib_Request::TYPE_STRING);
        $qq = System_Lib_App::app()->getRequest('QQ', System_Lib_Request::TYPE_STRING);
        $password = System_Lib_App::app()->getRequest('Password', System_Lib_Request::TYPE_STRING);

        $id = $_SESSION['id'];
        $secret=System_Lib_App::app()->getConfig('secret');

        $errcode="0";
        $errmsg="操作成功";
        $data="";
        try {
            $model=XP_BModel_SystemAccount::getOne($id);
            $model->mobile=$mobile;
            $model->qq=$qq;
            $model->password=$password==''?$model->password:md5($password+$secret['password']);
            $model->operator=0;
            $model->updateTime=date("Y-m-d H:i:s");
            $data=XP_BModel_SystemAccount::save($model);
            if($data==0) {
                $errcode="2001";
                $errmsg=sprintf("save or update SystemAccount error,params:%s,message:%s",json_encode(func_get_args()),'error not captured');
            }
        }
        catch(Exception $e) {
            $errcode="9999";
            $errmsg=sprintf("save or update SystemAccount error,params:%s,message:%s",json_encode(func_get_args()),'error not captured');
        }
        print_r(XP_Lib_Utility::jsonResult($errcode,$errmsg,$data));
    }
}
