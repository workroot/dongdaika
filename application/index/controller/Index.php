<?php
namespace app\index\controller;

use app\common\base\AuthController;
use app\common\base\Controllers;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use app\index\logic\AgentPriceLogicQuery;
use app\index\logic\IndexLogicQuery;
use app\index\logic\LogoLogicQuery;
use think\Db;

class Index extends Controllers
{


    /**
     * 查询页面
     * @return mixed|string
     */
    public function index()
    {
        //$params = $this->request->param();
        $pid = input('pid');
        $uid = input('uid');
        try{
            $gonggao = LogoLogicQuery::getInstance()->gonggao(['0','3']);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        if(!isset($pid) && !empty($pid)){
            $pid = PublicMethod::encryption($pid);
            $uid = PublicMethod::encryption($uid);
        }
        $this->assign('pid',$pid);
        $this->assign('uid',$uid);
        $this->assign('gonggao',$gonggao);
        return $this->fetch();
    }


    public function index1()
    {
        $post = $this->request->param();
        $this->assign('price',$post['prices']);
        return $this->fetch();
    }


    /**
     * 跳转页面
     * @return mixed|void
     */
    public function paid(){
        try{
            $post = $this->request->param();
            $par = IndexLogicQuery::getInstance()->condition_paid($post);
        }catch(\Exception $e) {
            return $this->html_404($this->renderError($e));
        }
        $this->assign('data',$post);
        $this->assign('price',$par);
        return $this->fetch();
    }


    /**
     * 数据查询
     * @return mixed|string
     */
    public function findOne(){
        try{
            $post = $this->request->param();
            $data = IndexLogicQuery::getInstance()->condition($post);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        if(isset($data) && !empty($data)){
            return $this->renderSuccess('',['redirect'=>'/index/index/paid?cid='.$data['cid'].'&cname='.$data['name'].'&pid='.$data['pid'].'&logo='.$data['logo']]);
        }else{
            return $this->renderSuccess('',['redirect'=>'/index/index/del']);
        }
    }


    /**
     * 详情
     * @return mixed|string
     */
    public function detail(){
        try{
            $get = $this->request->param();
            $get['out_trade_no'] = 'NDI3MzYwMA==';
            $data = IndexLogicQuery::getInstance()->findOne(['id'=>$get['out_trade_no']],'c.*');
            $comment = Db::name('Comment')->where(['cid'=>$data['id'],'isstop'=>1])->select();
            $this->assign('data',$data);
            $this->assign('comment',$comment);
            $this->assign('count',count($comment));
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch();
    }


    /**
     * 详情
     * @return mixed|string
     */
    public function details(){
        try{
            $get = $this->request->param();
            $get['out_trade_no'] = 'NDI3MzYwMA==';
            $data = IndexLogicQuery::getInstance()->findOne(['id'=>$get['out_trade_no']],'c.*');
            $comment = Db::name('Comment')->where(['cid'=>$data['id']])->select();
            $this->assign('data',$data);
            $this->assign('comment',$comment);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch();
    }

    /**
     * 详情
     * @return mixed
     */
    public function del(){
        return $this->fetch();
    }


    /**
     * 订单查询
     * @return mixed|string
     */
    public function one(){
        try{
            $get = $this->request->param();
            $get['out_trade_no'] = '201909065197100509';
            $order = Db::name('Order')->where(['number_order'=>$get['out_trade_no'],'status'=>1])->find();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        if(!empty($order)){
            return $this->renderSuccess('',['id'=>PublicMethod::encryption($order['cid'])]);
        }
    }



}
