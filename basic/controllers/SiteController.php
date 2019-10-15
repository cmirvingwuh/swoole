<?php

namespace app\controllers;
use Yii;
use yii\httpclient\Client;
use yii\web\Controller;
class SiteController extends Controller
{
    const BASE_URL = 'http://sem.baidubce.com/v1/feed/cloud/';
    const MARKETING_USERS = [   //开发者配置
        'c0e00ed647214808ac8b465b6e7efcbf'  => [
            'bceid'         => 'c0e00ed647214808ac8b465b6e7efcbf',  //百度云ID
            'accesskey'     => 'da630cc07c114b12a1cd2457d7d5e911',  //百度云访问KEY
            'secretkey'     => '5357ba3f46c44f9ea204abfb1876fa63',  //百度云访问密钥
            'company'       => '广州游城网络科技有限公司',
            'username'      => '游城原生02',                         //管家登录账户名
            'password'      => 'iwg380YZR',                         //管家登录密码
            'aduser'        => '原生-l8游城A18KA2054'                //管家下属的任一子账户，管家级账号操作自身时，必须要有个被操作账户，蛋疼
        ],
        '946ebee8efb8483ba333875ba552023a'  => [
            'bceid'         => '946ebee8efb8483ba333875ba552023a',  //百度云ID
            'accesskey'     => '2a2371a619aa44ebaece5c918a49435e',  //百度云访问KEY
            'secretkey'     => '90d9260714844575ac280a67bf39dc9c',  //百度云访问密钥
            'company'       => '上海恺英网络科技有限公司',
            'username'      => '恺英SY原生管家',                         //管家登录账户名
            'password'      => 'Ab1234',                         //管家登录密码
            'aduser'        => '原生-SY01-B19KA02801'                //管家下属的任一子账户，管家级账号操作自身时，必须要有个被操作账户，蛋疼
        ],
        '4049ef6b680b479893f5df6bf42d4bad'  => [
            'bceid'         => '4049ef6b680b479893f5df6bf42d4bad',  //百度云ID
            'accesskey'     => '9bcd5542495a44e59114056ef8734e0f',  //百度云访问KEY
            'secretkey'     => '2ee84814692d404ea584952692aa93e6',  //百度云访问密钥
            'company'       => '上海畅梦移动网络科技有限公司',
            'username'      => '畅梦12345',                         //管家登录账户名
            'password'      => 'Ab1234',                         //管家登录密码
            'aduser'        => '原生-畅梦7-B18KA0816'                //管家下属的任一子账户，管家级账号操作自身时，必须要有个被操作账户，蛋疼
        ],
    ];
    const PROXY_SERVER      = '180.76.244.131:44446';             //百度智能云代理服务器


    public function actionTest()
    {
        echo "process-start-time:".date("Ymd H:i:s");
        $urls = [
            'http://baidu1.com',
            'http://baidu2.com',
            'http://baidu3.com',
            'http://baidu4.com',
            'http://baidu5.com',
            'http://baidu6.com',
            'http://baidu7.com',
        ];
        $workers = [];

        for($i = 0 ;$i < 7; $i++){
            //子进程

            /**
             * 创建子进程
             *
             * @param callback 子进程创建成功后要执行的函数
             * @param bool 重定向子进程的标准输入和输出。启用此选项后，在子进程内输出内容将不是打印屏幕，而是写入到主进程管道。读取键盘输入将变为从管道中读取数据。默认为阻塞读取
             */
            $process = new \Swoole\Process(function(\Swoole\Process $worker) use($i,$urls){
                //curl
                $content = $this->curlData($urls[$i]);
//        echo $content.PHP_EOL; 两种方法都行
                $worker->write($content.PHP_EOL); //写入管道中
            },false);
            $pid = $process->start();  //创建成功返回子进程的PID，创建失败返回false。可使用swoole_errno和swoole_strerror得到错误码和错误信息。
            $workers[$pid] = $process;
        }

        foreach ($workers as $process){
            //ps 记得清空子进程

            echo $process->read(); // 从管道中读取
        }

        echo "process-end-time:".date("Ymd H:i:s");
    }

    /**
     * 模拟url请求
     * @param $url
     * @return string
     */
    public function curlData($url)
    {
        sleep(1);
        return $url."success".PHP_EOL;
    }

    public function actionIndex()
    {

//        echo phpinfo();die;
//      $rst = file_put_contents('/tmp/test.mp4' , file_get_contents('https://nbres-dev.oss-cn-shanghai.aliyuncs.com/Res/1dltgsrla1o221ta816rp4pf1ll24.mp4'));
//      var_export($rst);die;

        // 视频预上传
//        /data/wwwroot/wuh.suv3.changmeng.com/components/tf/BaiduAPI.php:127:string '946ebee8efb8483ba333875ba552023a' (length=32)
//        /data/wwwroot/wuh.suv3.changmeng.com/components/tf/BaiduAPI.php:127:string '原生-SY24-B19KA04241' (length=22)
//        /data/wwwroot/wuh.suv3.changmeng.com/components/tf/BaiduAPI.php:127:string '投放-RO-H5-075-吴浩-148-沙静-双屏+字幕跳' (length=51)
//        /data/wwwroot/wuh.suv3.changmeng.com/components/tf/BaiduAPI.php:127:string '/tmp/tfres/b4a9810a2111fef1d7f792c4aa2d321a.mp4' (length=47)
//        /data/wwwroot/wuh.suv3.changmeng.com/components/tf/BaiduAPI.php:127:int 99463552

        $makeUpload = self::videoMakeUpload('946ebee8efb8483ba333875ba552023a' , '原生-SY24-B19KA04241' , 'test' , '/tmp/test.mp4' , 99463552);

        var_dump($makeUpload);die;
    }

    /**
     * 视频预上传
     *
     * @param $bceid
     * @param $aduser
     * @param $filename
     * @param $filepath
     * @return bool|void
     */
    public static function videoMakeUpload($bceid, $aduser, $filename, $filepath , $byte) {
        if(!file_exists($filepath)) return;

        $params = [
            'prepareUploadVideoFeedRequest' => [
                [
                    'fileDesc'      => $filename,
                    'videoName'     => $filename,
                    'fileType'      => 2,
                    'source'        => 2,
                    'format'        => 'mp4',
                    'md5'           => md5_file($filepath),
                    'fileLength'    => $byte
                ]
            ]
        ];

        $json = self::requestAPI($bceid, $aduser, 'VideoFeedService/prepareUploadVideoFeed/', $params);
        if($json['header']['status'] === 0) {
            Yii::info(__METHOD__.__LINE__.json_encode($json));
            return $json['body']['data'][0];
        } else {
            Yii::warning(__METHOD__.__LINE__.json_encode($json));
            return false;
        }
    }

    /**
     * 视频分片上传
     *
     * @param $bceid
     * @param $aduser
     * @param $filename
     * @param $filepath
     * @param $byte
     * @return bool|void
     */
    public static function videoBurstUpload($bceid, $aduser, $filepath , $makeUpload)
    {

        Yii::info('百度上传开始时间 : ' .date('Y-m-d H:i:s'), 'tf_api');

        if (!file_exists($filepath)) return;

        $partNum = ceil(filesize($filepath) / $makeUpload['uploadPartSize']);

        $file = fopen($filepath,"r");

        for ($i = 1; $i <= $partNum; $i++) {

            Yii::info('百度上传第'.$i.'片开始时间 : ' .date('Y-m-d H:i:s'), 'tf_api');

            /**
             * videoid 视频ID
             * format  视频格式，默认取值"mp4"
             * fileMd5 整个视频的MD5
             * uploadId 视频上传id，同一个视频的多个分片上传时，都固定取值本视频上传之前通过预上传操作获取的uploadId
             * base64Content  视频分片的base64字符串
             * partNo 视频分片编号
             * partMd5 视频分片的md5值
             * partSize 视频分片的字节长度
             * maxPartNo 最大视频分片编号
             * endFlag 是否是最后一个分片，1：是，0：否
             */
            $s = $i == 1 ? 0 : ($i - 1) * $makeUpload['uploadPartSize'];

            $partSize = $i == $partNum ? filesize($filepath) - $s :  $makeUpload['uploadPartSize'];

            $params = [
                'uploadBySliceVideoFeedRequest' => [
                    [
                        'videoid'   => $makeUpload['videoid'],
                        'format'    => 'mp4',
                        'fileMd5'   => md5_file($filepath),
                        'uploadId'  => $makeUpload['uploadId'],
                        'base64Content' => base64_encode(self::filePart($file , $s , $partSize)),  //mark ???  base64_encode(file_get_contents($mackUpload['url']))
                        'partNo'    => $i,
                        'partMd5'   => md5(self::filePart($file , $s , $partSize)), // 某一段分片的MD5值
                        'partSize'  => $partSize,
                        'maxPartNo' => $partNum,
                        'endFlag'   => $i == $partNum ? 1 : 0
                    ]
                ]
            ];

            $json = self::requestAPI($bceid, $aduser, 'VideoFeedService/uploadBySliceVideoFeed', $params, 'POST', Client::FORMAT_JSON, 5, 20);

            Yii::info('百度上传第'.$i.'片结束时间 : ' .date('Y-m-d H:i:s'), 'tf_api');

            if ($i == $partNum) {

                fclose($file);

                if ($json['header']['status'] === 0) {

                    Yii::info('百度上传结束时间 : ' .date('Y-m-d H:i:s'), 'tf_api');

                    $json['body']['data'][0]['code'] = SdkResponse::RESP_SUCC;
                    return $json['body']['data'][0];

                } else {
                    Yii::warning(__METHOD__.__LINE__.$json, 'tf_api');
                    return ['code' => $json['header']['failures'][0]['code'] , 'message' => $json['header']['failures'][0]['message']];
                }
            }

        }

    }

    public static function filePart($file , $s , $uploadPartSize){

        fseek($file,$s);
//        echo '文件指针移动前位置 :' . ftell($file) . '分片编号 : ' . $i . '分片字节数 : '.$uploadPartSize . PHP_EOL;
        return fread($file,$uploadPartSize);
    }

    /*
 * 向百度API服务器发起请求
 * 百度API必须在百度云中发起请求，因此所有接口前方都有代理服务器
 */
    public static function requestAPI($bceid, $aduser, $api, $params = [], $method = 'POST', $format = Client::FORMAT_JSON, $ctime = 5, $timeout = 10) {
        if(!array_key_exists($bceid, self::MARKETING_USERS)) return ['code' => 999, 'bceid not config'];

        $url = self::BASE_URL . $api;
        $method = strtoupper($method);

        $timezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $timestamp = date('Y-m-d\TH:i:s\Z');
        date_default_timezone_set($timezone);

        $params = [
            'body'  => empty($params) ? (object)[] : $params,
            'header' => [
                'opUsername'    => self::MARKETING_USERS[$bceid]['username'],
                'opPassword'    => self::MARKETING_USERS[$bceid]['password'],
                'tgSubname'     => $aduser,
                'bceUser'       => $bceid
            ]
        ];

        /**** 生成认证参数Start ****/
        $authStringPrefix = implode('/', [
            'bce-auth-v1',
            self::MARKETING_USERS[$bceid]['accesskey'],
            $timestamp,
            1800
        ]);
        $SigningKey = hash_hmac('sha256', $authStringPrefix, self::MARKETING_USERS[$bceid]['secretkey']);

        $urlInfo = parse_url($url);
        $queryArr = isset($urlInfo['query']) ? parse_str($urlInfo['query']) : [];
        if(array_key_exists('authorization', $queryArr)) unset($queryArr['authorization']);
        ksort($queryArr);
        $CanonicalRequest = [
            $method,
            str_replace('%2F', '/', urlencode($urlInfo['path'])),
            http_build_query($queryArr),
            'host:' . urlencode($urlInfo['host'])
        ];

        $signature = hash_hmac('sha256', implode("\n", $CanonicalRequest), $SigningKey);
        $authorization = $authStringPrefix . '/host/' . $signature;
        /**** 生成认证参数End ****/

        try {
            $client = new Client([
                'transport' => 'yii\httpclient\CurlTransport',
                'requestConfig' => ['format' => $format],
                'responseConfig' => ['format' => Client::FORMAT_JSON]
            ]);

            $response = $client->createRequest()->setMethod($method)->setUrl($url)->setData($params)
                ->setHeaders([
                    'Authorization' => $authorization,
                    'x-bce-date' => $timestamp
                ])->setOptions([
                    CURLOPT_PROXY => self::PROXY_SERVER,
                    CURLOPT_CONNECTTIMEOUT => $ctime,
                    CURLOPT_TIMEOUT => $timeout,
                    CURLOPT_FOLLOWLOCATION => true
                ])->send();

            if($response->isOK) {
                return $response->data;
            } else {
                Yii::warning($response->toString(), 'tf_api');
                return ['header' => ['status' => $response->statusCode, 'desc' => $response->toString()]];
            }
        } catch (\yii\base\InvalidConfigException $e) {
            Yii::warning($e->getMessage(), 'tf_api');
            return ['code' => 998, 'httpClient Request Exception'];
        }
    }


}
