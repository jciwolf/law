<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-11-5
 * Time: 下午2:42
 * To change this template use File | Settings | File Templates.
 */

class XP_BModel_Login
{
    public static function loginInfo($username,$password)
    {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        $secret=System_Lib_App::app()->getConfig('secret');
        $password=md5($password+$secret['password']);
        //XP_Lib_Utility::log("password:$password");
        $account = XP_BModel_SystemAccount::getSystemAccount($username);
        if(empty($account))$account = XP_BModel_SystemAccount::getSystemAccount("",$username);
        if(empty($account)) {
            $errcode="1001";
            $errmsg="用户名不对";
        }
        elseif($account->password != $password) {
            $errcode="1002";
            $errmsg="密码不对";
        }
        elseif(!(strtotime($account->beginDate)<=strtotime(date("Y-m-d H:i:s")) && strtotime(date("Y-m-d H:i:s"))<=strtotime($account->endDate))) {
            $errcode="1003";
            $errmsg="账号已过期";
        }
        elseif($account->status != 1) {
            $errcode="1004";
            $errmsg="账号被禁用";
        }
        else {
            /*
            $bll=new XP_BModel_Permission();
            $permission=$bll->permissionList($account->id,XP_Lib_Enum::AccountType_SystemAccount);
            $permission=json_encode($permission);
            */
            $data=array(
                'id'=>$account->id,
                'name'=>$account->name,
                'email'=>$account->email,
                'mobile'=>$account->mobile,
                'qq'=>$account->qq
            );
        }
        return XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
    }

    public static function permission($accountId,$type)
    {
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        try {
            $bll=new XP_BModel_Permission();
            $data=$bll->permissionList($accountId,$type);
            //$data=json_encode($data);
        }
        catch(Exception $e) {
            $errcode="9999";
            $errmsg=sprintf('get permission error,accountId:%s,type:%s,message:%s',$accountId,$type,$e->getMessage());
        }
        return XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
    }
}