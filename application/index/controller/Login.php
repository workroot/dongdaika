<?php
namespace app\index\controller;

use app\common\base\Controllers;
use app\index\logic\LogoLogicQuery;

class Login extends Controllers{


    public function registered($pid=''){
        $this->assign('pid',$pid);
        return $this->fetch();
    }


    /**
     * 登录页面
     * @return mixed|string
     */
    public function login(){
        try{
            $logo = LogoLogicQuery::getInstance()->logo();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('logo',$logo);
        return $this->fetch();
    }


}