<?php

namespace auto\process;

class BootStrap implements \Webman\Bootstrap
{
    public static function start($worker)
    {
        if ($worker->name != 'monitor') {
            return;
        }

        static::makeConfig();
    }

    public static function makeConfig()
    {
        $process = config('plugin.auto.port.process', []);
        $listen = config('server.listen', 'http://0.0.0.0:8787');

        $ports = [str_replace('http://0.0.0.0', 'server 127.0.0.1', $listen)];
        foreach ($process as $pr) {
            $ports[] = str_replace('http://0.0.0.0', 'server 127.0.0.1', $pr['listen']);
        }

        $nginx = <<<EOT
upstream webman {
    ip_hash;
    least_conn;
    #__process__;
    keepalive 10240;
}
#其他参照webman官方文档
#https://www.workerman.net/doc/webman/others/nginx-proxy.html
server {
    #...
}

EOT;
        $res = file_put_contents(runtime_path() . '/nginx.conf', str_replace('#__process__', implode(';' . PHP_EOL . '    ', $ports), $nginx));

        if ($res) {
            echo '[auto-process]Writing nginx config file at runtime/nginx.conf succeed.' . "\n";
        } else {
            echo '[auto-process] Writing nginx config file failed.' . "\n";
        }
    }
}
