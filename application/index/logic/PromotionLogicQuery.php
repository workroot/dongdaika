<?php

namespace app\index\logic;


use app\common\base\LogicQuery;
use app\common\base\Mapper;
use app\common\model\AgentPrice;
use think\Db;
use think\Exception;

class PromotionLogicQuery extends LogicQuery{


    /**
     * 添加支付连接
     * @param null $params
     * @return int|string
     * @throws \Exception
     */
        public function price($params = null){
            try{
                if(empty($params)){
                    $params = $this->request->param();
                }
                $user = Db::name('User')->field('id,names,agent_class,mobile')->where('id',$params['uid'])->find();

                if(empty($user)){
                    throw new Exception('参数错误',0);
                }

                //判断用户是否添加代理
                $authAgent = Db::name('authAgent')->where(['aid'=>$user['agent_class'],'pid'=>$params['type']])->find();

                if(empty($authAgent)){
                    throw new Exception('请联系客服添加代理',0);
                }

                if(!isset($params['price']) && empty($params['price'])){
                    throw new Exception('金额不能为空',0);
                }

                if(!is_numeric($params['price'])){
                    throw new Exception('金额只能为数字',0);
                }

                if(!in_array($user['id'],Mapper::$TEST_ID) && $params['price'] < $authAgent['price']){
                    throw new Exception('金额不能低于成本价格',0);
                }

                if($params['price'] > $authAgent['highestprice']){
                    throw new Exception('金额不能高于平台设置价格',0);
                }

                $data = [
                    'a_p_id' => $authAgent['id'],
                    'product_type' => $params['type'],
                    'rename' => $params['rename'],
                    'price' => $params['price'],
                    'ticheng' => $params['price'] - $authAgent['price'],
                    'uid' => $params['uid'],
                    'define' => 1,
                    'isdel' => 1,
                    'createdAt' => time(),
                ];
                $model = Db::name('AgentPrice')->insertGetId($data);
            }catch(\Exception $e){
                $this->log($e);
                throw $e;
            }
            return $model;
        }
}