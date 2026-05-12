<?php

$webman = config('process.webman');
if (!$webman) {
    return [];
}
$process_count = config('plugin.auto.port.app.process_count', 8);
$process = [];
$port = explode(':', $webman['listen'])[2] ?? '8787';

for ($i = 1; $i < $process_count; $i += 1) {
    $process[$i] = array_merge($webman, ['listen' => 'http://0.0.0.0:' . ($port + $i), 'count' => 1]);
}

return $process;
