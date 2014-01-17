<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zyh
 * Date: 13-11-12
 * Time: 上午10:34
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_Menu extends XP_Controller_Base {
    protected $layoutName = 'XP_Layout_Public';

    public function getMenuAction(){

        $publicAccountId = System_Lib_App::app()->getRequest('pid', System_Lib_Request::TYPE_INT);
        $menu = new XP_BModel_Menu();
        $list  = $menu->getMenu($publicAccountId);
        $menusXml  = $menu->getMenuJsonForHtml($list);
        $menuList = json_decode($menusXml);
        //print_r($publicAccountId);
       // print_r($menusXml);

        if (!empty($menuList)){
            $this->assignData('menus', $menuList);
            $this->assignData('publicAccontId', $publicAccountId);
        }
        $this->render('PublicAccount/Menu');
    }
    public function deleteMenuAction(){


    }
}