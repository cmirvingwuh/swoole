<?php

//进程? 就是运行的程序的一个实例



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
//原始方案
//foreach ($urls as $url){
//    $content[] = file_get_contents($url);
//}


for($i = 0 ;$i < 7; $i++){
    //子进程

    $process = new Swoole\Process(function(Swoole\Process $worker) use($i,$urls){
        //curl
        $content = curlData($urls[$i]);
//        echo $content.PHP_EOL; 两种方法都行
        $worker->write($content.PHP_EOL);
    },true);
    $pid = $process->start();
    $workers[$pid] = $process;
}

foreach ($workers as $process){
    echo $process->read();
}
/**
 * 模拟url请求
 * @param $url
 * @return string
 */
function curlData($url)
{
    sleep(1);
    return $url."success".PHP_EOL;
}
echo "process-end-time:".date("Ymd H:i:s");