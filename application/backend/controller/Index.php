<?php
namespace app\backend\controller;

use app\backend\base\AuthController;
use app\common\model\Mobile;
use think\Db;

class Index extends AuthController
{




    public function index()
    {
        $version = Db::query('SELECT VERSION() AS ver');
        $config  = [
            'url'             => $_SERVER['HTTP_HOST'],
            'document_root'   => $_SERVER['DOCUMENT_ROOT'],
            'server_os'       => PHP_OS,
            'server_port'     => $_SERVER['SERVER_PORT'],
            'server_soft'     => $_SERVER['SERVER_SOFTWARE'],
            'php_version'     => PHP_VERSION,
            'mysql_version'   => $version[0]['ver'],
            'max_upload_size' => ini_get('upload_max_filesize')
        ];
        return $this->fetch('index', ['config' => $config]);
    }

    public static $arr = [
        176,177,178,
        139,138,137,136,135,134,159,158,157,152,151,150,187,188,182,183,184,//147,
        130,131,132,155,156,185,186,
        133, 153 ,173, 177, 180 ,181 ,189 ,199,
    ];

    public function addfoo($num = 1000000){
        $t1=microtime(true);
        for($i = 1; $i < $num; $i++) {
            $mobile = self::$arr[array_rand(self::$arr)].mt_rand(1000,9999).mt_rand(1000,9999);
            $url = 'http://mobsec-dianhua.baidu.com/dianhua_api/open/location?tel='.$mobile;
            $pone = Db::name('Mobile')->where('mobile','=',$mobile)->find();
            if(!empty($pone)){
                continue;
            }else{
                //$content = file_get_contents($url);
                $res = $this->curlRequest($url,'','GET');
                $phone = json_decode($res,true);
                $data['city'] = isset($phone['response'][$mobile]['detail']['area'][0]['city'])&&!empty($phone['response'][$mobile]['detail']['area'][0]['city'])?$phone['response'][$mobile]['detail']['area'][0]['city']:0;
                $data['province'] = isset($phone['response'][$mobile]['detail']['province'])&&!empty($phone['response'][$mobile]['detail']['province'])?$phone['response'][$mobile]['detail']['province']:0;
                $data['operator'] = isset($phone['response'][$mobile]['detail']['operator'])&&!empty($phone['response'][$mobile]['detail']['operator'])?$phone['response'][$mobile]['detail']['operator']:0;
                $data['location'] = isset($phone['response'][$mobile]['location'])&&!empty($phone['response'][$mobile]['location'])?$phone['response'][$mobile]['location']:0;
                $data['mobile'] = $mobile;
                $data['isstop'] = 1;
                $data['createAt'] = time()+$i;
                Db::name('Mobile')->insertGetId($data,true);
            }
        }
        $t2=microtime(true);
        echo '耗时'.round(($t2-$t1)/60,3).'秒<br>';
    }



    /**
     * 发起CURL请求
     * @param string $url 请求地址
     * @param string $data 请求数据
     * @param string $method 请求方式
     * @return array 一维数组
     */
    public function curlRequest($url,$data = '',$method = 'POST')
    {
        $ch = curl_init(); //初始化CURL句柄
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而s不是直接输出
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式

        curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: $method"));//设置HTTP头信息
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置提交的字符串
        $document = curl_exec($ch);//执行预定义的CURL
        curl_close($ch);
        return $document;
    }







    public function save($data){
        $num = 500;
        $limit = ceil(count($data)/500);
        for($i=1;$i<=$limit;$i++){
            $offset = ($i-1)*$num;
            $data = array_slice($data,$offset,$num);
            Db::name('Mobile')->insertAll($data,true);
        }
    }


}
