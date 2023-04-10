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
    #ip_hash;
    least_conn;
    #__process__;
    keepalive 10240;
}
#其他参照webman官方文档
#https://www.workerman.net/doc/webman/others/nginx-proxy.html
server {
    server_name localhost;         #请修改站点域名
    listen 8081;                   #请修改端口
    access_log off;
    root /to/your/webman/public;   #请修网站根目录，以使nginx直接处理静态资源

    location ^~ / {
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header Host \$host;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        if (!-f \$request_filename){
            proxy_pass http://webman;
            break;
        }
    }
}

EOT;
        $res = file_put_contents(runtime_path() . '/webman.conf', str_replace('#__process__', implode(';' . PHP_EOL . '    ', $ports), $nginx));

        if ($res) {
            echo '[auto-process]Writing nginx config file at runtime/webman.conf succeed.' . "\n";
        } else {
            echo '[auto-process]Writing nginx config file failed.' . "\n";
        }
    }
}
