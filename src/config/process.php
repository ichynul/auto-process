<?php

if (DIRECTORY_SEPARATOR === '/') {
    return [];
}

$count = config('server.count', 4);
$listen = config('server.listen', 'http://0.0.0.0:8787');
$port = explode(':', $listen)[2] ?? 8787;
$process = [];

for ($i = 1; $i < $count; $i += 1) {
    $process[$i] = [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:' . ($port + $i),
        'count' => 1, // 进程数
        'constructor' => [
            'request_class' => \support\Request::class, // request类设置
            'logger' => \support\Log::channel('default'), // 日志实例
            'app_path' => app_path(), // app目录位置
            'public_path' => public_path() // public目录位置
        ]
    ];
}

return $process;
