<?php

namespace app\index\controller;

use app\common\base\AuthController;
use app\common\base\Mapper;
use app\index\logic\PromotionLogicQuery;
use think\Db;

class Promotion extends AuthController{




    public function index(){
        return $this->fetch();
    }


    /**
     * 设置推广平台价格
     * @return mixed
     */
    public function addPrice(){
        $price = Db::name('AgentPrice')->where(['uid'=>Session('user_id'),'product_type' => Mapper::PRODUCT_TYPE])->select();
        return $this->fetch('addprice',['data'=>$price]);
    }


    /**
     *  添加支付连接
     * @return mixed
     */
    public function add(){
        try{
            $params = $this->request->param();
            $params['type'] = 1;
            $params['uid'] = Session('user_id');
            $params['rename'] = '平台';
            $params['price'] = 5;
            $price = PromotionLogicQuery::getInstance()->price($params);
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->renderSuccess($price);
    }



    public function addmany(){
        try{
            $params = $this->request->param();
            $price = PromotionLogicQuery::getInstance()->price($params);
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->renderSuccess($price);
    }


    /**
     * 律师设置价格
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function layPrice(){
        $price = Db::name('AgentPrice')->where(['uid'=>Session('user_id'),'product_type' => Mapper::PRODUCT_TYPE_TWO])->select();
        return $this->fetch('layprice',['data'=>$price]);
    }

}