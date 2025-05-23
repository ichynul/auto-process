<?php

$process_count = config('plugin.auto.port.app.process_count', 8);
$user = config('server.user', '');
$group = config('server.group', '');
$listen = config('server.listen', 'http://0.0.0.0:8787');
$port = explode(':', $listen)[2] ?? 8787;
$event_loop = config('server.event_loop', '');
$max_package_size = config('server.max_package_size');
$process = [];

for ($i = 1; $i < $process_count; $i += 1) {
    $process[$i] = [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:' . ($port + $i),
        'count' => 1, // 进程数
        'user' => $user,
        'group' => $group,
        'event_loop' => $event_loop,
        'max_package_size' => $max_package_size,
        'constructor' => [
            'request_class' => \support\Request::class, // request类设置
            'logger' => \support\Log::channel('default'), // 日志实例
            'app_path' => app_path(), // app目录位置
            'public_path' => public_path() // public目录位置
        ]
    ];
}

return $process;
