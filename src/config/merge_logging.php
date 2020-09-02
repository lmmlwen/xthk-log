<?php

return [
    'xthklog' => [
        // 日志驱动模式：
        'driver' => 'daily',
        // 日志存放路径
        'path' => storage_path('logs/xthk.log'),

        'tap' => [Lmmlwen\Xthklog\Logging\XthkLogFormatter::class],
        // 日志等级：
        'level' => env('LOG_LEVEL', 'notice'),
        // 日志分片周期，多少天一个文件
        'days' => 1,
    ],
];
