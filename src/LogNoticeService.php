<?php
namespace Aishan\LaravelLogNotice;

use Aishan\LaravelLogNotice\Service\LogManageService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Created by PhpStorm.
 * User: aishan
 * Date: 17-6-29
 * Time: 上午9:33
 */
class LogNoticeService
{
    public function configureMonolog($app, $monolog)
    {
        //记录日志到本地
        if (config('log-notice.log_local')) {
            $monolog->pushHandler(new StreamHandler(storage_path() . config('log-notice.log_local_dir'), Logger::DEBUG));
        }

        //记录日志到logStash,将异常信息发送邮件
        $monolog->pushProcessor(function ($record) {
            $logManager = new LogManageService($record);
            //非debug的日志记录到logStash
            $level = $record['level'];
            if (config('log-notice.log_rmq')) {
                if ($level >= 200) {
                    $logManager->logToRMQ();
                }
            }
            if (config('log-notice.mail_enable')) {
                //ERROR 以上的日志信息发送至邮件
                if ($level >= 400) {
                    $logManager->sendEmail();
                }
            }
            return $record;
        });
    }
}