# laravel-log-notice
本项目支持自定义laravel框架的日志行为，提供异常日志邮件自动发送功能和推送异常日志到RabbitMQ中。

## 安装
*** 目前只在laravel5.1及以上版本测试通过
```bash
composer install aishan/laravel-log-notice
```
## 使用
1.  在config/app.php的“providers”中加入：
```php
Aishan\LaravelLogNotice\LogNoticeServiceProvider::class,
```
2. 拷贝配置文件到config目录：

  在根目录执行：
```bash 
php artisan vendor:publish --provider="Aishan\LaravelLogNotice\LogNoticeServiceProvider" --tag="config"
```

3. 框架中加入配置Monolog代码
 

在bootstrap/app的`return $app;`之前加入一下代码：
```php
/**
 * 配置日志行为
 */
$app->configureMonologUsing(function($monolog) use ($app) {
    $configureLogging = new \Aishan\LaravelLogNotice\LogNoticeService();
    $configureLogging->configureMonolog($app ,$monolog);
});
```

*** 注意：使用这个扩展后，Laravel原本的日志配置将失效，譬如`config/app.php`文件中关于关于日志的配置都将失效，而我们在此刻可以启用新的日志配置文件`config/log-notice.php`

4.配置

配置文件为`config/log-notice.php`:
```php
<?php
return [
    //日志RMQ连接配置
    'log_rmq'=>env('LOG_RMQ_ENABLE',false),//是否开启日志推送到队列
    'log_rmq_connect'=>[
        'host'=>env('RMQ_LOG_HOST'),
        'port'=>env('RMQ_LOG_PORT'),
        'user'=>env('RMQ_LOG_USER'),
        'pass'=>env('RMQ_LOG_PASS'),
        'vhost'=>env('RMQ_LOG_VHOST')
    ],
    'log_rmq_source'=>env('RMQ_SOURCE','pvm-center'),
    'log_rmq_job_queue'=>env('RMQ_QUEUE','default'),//发送消息到rmq的动作使用的job所需指定的laravel队列
    //日志RMQ的队列配置
    'log_rmq_queue'=>[
        'exchange' => 'e.log.frontend',
        'routingKey' => 'r.log.frontend',
        'exchangeType' => 'direct',
        'queueName' => 'q.log.frontend'
    ],

    //邮件配置
    'mail_enable'=>env('LOG_MAIL_ENABLE',false),//是否异常报错邮件发送
    'mail_subject'=>env('LOG_MAIL_SUBJECT','Error Notice'),//通知邮件标题
    'mail_receiver'=>env('LOG_MAIL_RECEIVER',''),//邮件接收者，多个收件人用逗号分隔
    'mail_job_queue'=>env('LOG_MAIL_QUEUE','default'),//发送邮件job所所需指定的laravel队列
    
    //本地日志文件
    'log_local'=>env('LOG_LOCAL_ENABLE',true),//本地是否开启日志
    'log_local_dir'=>'/logs/laravel.log',//基于storage目录
];
```
*** 其中，默认开启的为本地日志文件，邮件通知和推送消息至RabbitMQ的动作均需手动开启。
1. 为什么消息会推送至RabbitMQ？

因为，团队生产环境用的Logstash是通过RMQ通信转发的，所以实际应用场景是将日志消息最终转入日志服务器；

2.什么样的日志会被邮件发送？

八种日志等级在系统的定义：
```php
"DEBUG" => 100
"INFO" => 200
"NOTICE" => 250
"WARNING" => 300
"ERROR" => 400
"CRITICAL" => 500
"ALERT" => 550
"EMERGENCY" => 600
```
本扩展中的实现：
```php
if($level >= 400){//ERROR 以上的日志信息发送至邮件
    $logManager->sendEmail();
}
```
所以，debug和info的日志都不会以邮件形式发送。


