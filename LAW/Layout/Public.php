<?php
/**
 * @author: deliliu
 * Date: 12-9-23
 * Time: 下午2:01
 */
class XP_Layout_Public extends System_Lib_Layout
{
    private $jsArr = array();
    private $cssArr = array();
    private $_title = '';
    private $_bodyClass = '';
    private $jsCodeArr = array();

    public function defaultAction()
    {
        $this->assignData('pageTitle', $this->_title);
        $this->assignData('bodyClass', $this->_bodyClass);
        $this->assignData('cssArr', $this->cssArr);
        $this->assignData('jsArr', $this->jsArr);
        $this->assignData("jsCodeArr", $this->jsCodeArr);
        $this->assignData('web_version', '1.0');
    }

    public function addJs($path)
    {
        $this->jsArr[] = $path;
    }

    public function addJsCode($code)
    {
        $this->jsCodeArr[] = $code;
    }

    public function addCss($path)
    {
        $this->cssArr[] = $path;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function setBodyClass($class)
    {
        $this->_bodyClass = $class;
    }

}
