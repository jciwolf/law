<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-30
 * Time: 上午10:50
 * To change this template use File | Settings | File Templates.
 */

class XP_BModel_Menu extends System_Lib_Controller {
    const  menuInfo='{"button":[%s]}';
    const  subbuttonInfo='%s{"name":"%s","sub_button":[%s]}';
    const  buttonInfo='%s{"type":"%s","name":"%s","%s":"%s"}';

    /**
     * @return 用户菜单列表
     */
    public function getMenu($publicAccountId) {
        $list=XP_Model_Menu::dataAccess()->filterByOp(XP_Model_Menu::PUBLIC_ACCOUNT_ID,'=',$publicAccountId)->filterByOp(XP_Model_Menu::STATUS,'=',1)->sort(XP_Model_Menu::PARENT_ID,'asc')->sort(XP_Model_Menu::SHOW_INDEX,'asc')->find();
        return $list;
    }
    public function getMenuByKey($publicAccountId,$key) {
        $model=XP_Model_Menu::dataAccess()
            ->filterByOp(XP_Model_Menu::PUBLIC_ACCOUNT_ID,'=',$publicAccountId)
            ->filterByOp(XP_Model_Menu::ID,'=',$key)
            ->findOne();
        return $model;
    }
    public function updateMenu($publicAccountId,$menuList) {

        /*
            XP_Model_Menu::dataAccess()->filterByOp(XP_Model_Menu::PUBLIC_ACCOUNT_ID,'=',$publicAccountId)
                ->setField(XP_Model_Menu::STATUS,'=',0)
                ->setField(XP_Model_Menu::UPDATE_TIME, date('Y-m-d H:i:s'))
                ->update();
        */
        XP_Model_Menu::dataAccess()->filterByOp(XP_Model_Menu::PUBLIC_ACCOUNT_ID,'=',$publicAccountId)
            ->delete();

        foreach($menuList as $menuItem )
        {

            XP_Model_Menu::dataAccess()
                ->setField(XP_Model_Menu::PUBLIC_ACCOUNT_ID, $publicAccountId)
                ->setField(XP_Model_Menu::NAME, $menuItem["name"])
                ->setField(XP_Model_Menu::KEYWORD, $menuItem["keyword"])
                ->setField(XP_Model_Menu::SHOW_INDEX, $menuItem["showIndex"])
                ->setField(XP_Model_Menu::STATUS, 1)
                ->setField(XP_Model_Menu::TYPE, $menuItem["type"])
                ->setField(XP_Model_Menu::CREATE_TIME, date('Y-m-d H:i:s'))
                ->setField(XP_Model_Menu::UPDATE_TIME, date('Y-m-d H:i:s'))
                ->insert();


//            XP_Model_Menu::dataAccess()->filterByOp(XP_Model_Menu::PUBLIC_ACCOUNT_ID,'=',$publicAccountId)
//                ->setField(XP_Model_Menu::KEY,'=',$item.id)
//                ->setField(XP_Model_Menu::UPDATE_TIME, date('Y-m-d H:i:s'))
//                ->update();

            if(!empty($menuItem["subButton"])){

                $item=XP_Model_Menu::dataAccess()
                    ->filterByOp(XP_Model_Menu::PUBLIC_ACCOUNT_ID,'=',$publicAccountId)
                    ->filterByOp(XP_Model_Menu::STATUS,'=',1)
                    ->filterByOp(XP_Model_Menu::NAME,'=',$menuItem["name"])
                    ->filterByOp(XP_Model_Menu::KEYWORD,'=', $menuItem["keyword"])
                    ->findOne();

                foreach($menuItem["subButton"] as $subItem ){

                    XP_Model_Menu::dataAccess()
                        ->setField(XP_Model_Menu::PUBLIC_ACCOUNT_ID, $publicAccountId)
                        ->setField(XP_Model_Menu::NAME, $subItem["name"])
                        ->setField(XP_Model_Menu::PARENT_ID, $item->id)
                        ->setField(XP_Model_Menu::KEYWORD, $subItem["keyword"])
                        ->setField(XP_Model_Menu::SHOW_INDEX, $subItem["showIndex"])
                        ->setField(XP_Model_Menu::STATUS, 1)
                        ->setField(XP_Model_Menu::TYPE, $subItem["type"])
                        ->setField(XP_Model_Menu::CREATE_TIME, date('Y-m-d H:i:s'))
                        ->setField(XP_Model_Menu::UPDATE_TIME, date('Y-m-d H:i:s'))
                        ->insert();
                }

            }

        }

    }

    public function getMenuJsonForHtml($list,$parentId=0) {
        $first=true;$content='';
        foreach($list as $item1) {
            if($item1->parentId==$parentId) {
                $find=false;
                foreach($list as $item2) {
                    if($item1->id===$item2->parentId) {
                        $find = true;
                        break;
                    }
                }
                if(!$find) {
                    $content.=sprintf(self::buttonInfo,$first?'':',',$item1->type==1?'view':'click',str_replace('"', '\"',$item1->name),$item1->type==1?'url':'key',$item1->type==1?str_replace('"', '\"',$item1->keyword):str_replace('"', '\"',$item1->keyword));
                }
                else {
                    $content.=sprintf(self::subbuttonInfo,$first?'':',', str_replace('"', '\"',$item1->name),$this->getMenuJsonForHtml($list,$item1->id));
                }
                $first=false;
            }
        }
        if($parentId==0) $content=sprintf(self::menuInfo,$content);
        return $content;
    }
    /**
     * @param $list：用户菜单列表
     * @param int $parentId：当前父ID
     * @return string：用户菜单列表XML
     */
    public function getMenuJson($list,$parentId=0) {
        $first=true;$content='';
        foreach($list as $item1) {
            if($item1->parentId==$parentId) {
                $find=false;
                foreach($list as $item2) {
                    if($item1->id===$item2->parentId) {
                        $find = true;
                        break;
                    }
                }
                if(!$find) {
                    $content.=sprintf(self::buttonInfo,$first?'':',',$item1->type==1?'view':'click',str_replace('"', '\"',$item1->name),$item1->type==1?'url':'key',$item1->type==1?str_replace('"', '\"',$item1->keyword):str_replace('"', '\"',$item1->id));
                }
                else {
                    $content.=sprintf(self::subbuttonInfo,$first?'':',', str_replace('"', '\"',$item1->name),$this->getMenuJson($list,$item1->id));
                }
                $first=false;
            }
        }
        if($parentId==0) $content=sprintf(self::menuInfo,$content);
        return $content;
    }
}