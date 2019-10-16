<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
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
//                $content = $this->curlData($urls[$i]);
//        echo $content.PHP_EOL; 两种方法都行

                // 封装 exec 系统调用
                // 绝对路径
                // 参数必须分开放到数组中
                $content = $worker->exec('/usr/local/php/bin/php', ['/data/wwwroot/test.com/swoole/basic/yii hello/index']); // exec 系统调用


                $worker->write($content.PHP_EOL); //写入管道中
            },true);
            $pid = $process->start();  //创建成功返回子进程的PID，创建失败返回false。可使用swoole_errno和swoole_strerror得到错误码和错误信息。



            $workers[$pid] = $process;
        }

        foreach ($workers as $process){
            //ps 记得清空子进程

            echo $process->read(); // 从管道中读取
        }
        \Swoole\Process::wait();  //子进程结束必须要执行wait进行回收，否则子进程会变成僵尸进程

        echo "process-end-time:".date("Ymd H:i:s");

    }
}
