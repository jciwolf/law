<?php

class XP_Controller_Ajax_Login extends System_Lib_Controller
{
    public function processAction()
    {
        $username = System_Lib_App::app()->getRequest('Username', System_Lib_Request::TYPE_STRING);
        $password = System_Lib_App::app()->getRequest('Password', System_Lib_Request::TYPE_STRING);
        $remember = System_Lib_App::app()->getRequest('Remember', System_Lib_Request::TYPE_STRING);
        $result=XP_BModel_Login::loginInfo(trim($username),trim($password));
        //XP_Lib_Utility::log("$result");

        /*----cookie、session设置 begin----*/
        $expire=($remember=='true'? 7:1)*24*60*60;
        $r=json_decode($result,true);
        if($r['errcode']=='0') {
            $id=$r['data']['id'];
            $name=$r['data']['name'];
            $email=$r['data']['email'];

            $secret=System_Lib_App::app()->getConfig('secret');
            setcookie('LoginInfo[id]', System_Lib_AES::encrypt($id,$secret['cookie']),time()+$expire,'/');
            setcookie('LoginInfo[name]', System_Lib_AES::encrypt($name,$secret['cookie']),time()+$expire,'/');
            setcookie('LoginInfo[email]', System_Lib_AES::encrypt($email,$secret['cookie']),time()+$expire,'/');
            setcookie('LoginInfo[token]',md5(sprintf('%s|%s|%s|%s',$id,$name,$email,$secret['cookie'])),time()+$expire,'/');

            //XP_Lib_Utility::log($_COOKIE['LoginInfo']['id'].'|id:'.System_Lib_AES::decrypt($_COOKIE['LoginInfo']['id'],$secret['cookie']));
            //XP_Lib_Utility::log($id.'|token:'.$_COOKIE['LoginInfo']['token']);
            //XP_Lib_Utility::log('des:'.System_Lib_AES::decrypt($_COOKIE['LoginInfo']['token'],$secret['cookie']));

            setcookie('LoginInfoHistory[lastName]',$name,time()+365*$expire,'/');

            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
        }
        /*----cookie、session设置 end----*/
        print_r($result);
    }
    public function permissionAction()
    {
        $accountId = System_Lib_App::app()->getRequest('accountId', System_Lib_Request::TYPE_INT);
        $type = System_Lib_App::app()->getRequest('type', System_Lib_Request::TYPE_INT);
        $result=XP_BModel_Login::permission($accountId,$type);
        print_r($result);
    }
}
