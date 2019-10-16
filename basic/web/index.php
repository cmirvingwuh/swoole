<?php
// comment out the following two lines when deployed to production
function getIP() {
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }

//    return empty($ip) ? $_SERVER['REMOTE_ADDR'] : $ip;
}

if(!defined('YII_DEBUG') && getIP() == "210.22.94.146") define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';


$http = new Swoole\Http\Server("127.0.0.1", 9501);
$http->on('request', function ($request, $response) use ($config) {

    (new yii\web\Application($config))->run();
//    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();

