<?php
/**
 * RMQ配置
 * User: aishan
 * Date: 16-12-14
 * Time: 下午6:03
 */
return [
    //日志RMQ连接配置
    'log_rmq'=>env('LOG_RMQ_ENABLE',true),//是否开启日志推送到队列
    'log_rmq_connect'=>[
        'host'=>env('RMQ_LOG_HOST'),
        'port'=>env('RMQ_LOG_PORT'),
        'user'=>env('RMQ_LOG_USER'),
        'pass'=>env('RMQ_LOG_PASS'),
        'vhost'=>env('RMQ_LOG_VHOST')
    ],
    'log_rmq_source'=>env('RMQ_SOURCE','pvm-center'),
    'log_rmq_queue'=>env('RMQ_QUEUE','default'),
    //日志RMQ配置
    'log'=>[
        'exchange' => 'e.log.frontend',
        'routingKey' => 'r.log.frontend',
        'exchangeType' => 'direct',
        'queueName' => 'q.log.frontend'
    ],


    //邮件配置
    'mail_enable'=>env('LOG_MAIL_ENABLE',false),//是否异常报错邮件发送
    'mail_subject'=>env('LOG_MAIL_SUBJECT','Error Notice'),
    'mail_receiver'=>env('LOG_MAIL_RECEIVER',''),
    'mail-queue'=>env('LOG_MAIL_QUEUE','default'),


    //本地日志文件
    'log_local'=>env('LOG_LOCAL_ENABLE',true),//本地是否开启日志
    'log_local_dir'=>'/logs/laravel.log',//基于storage目录
];