<?php

declare(strict_types=1);

namespace AlertClient;

use WLib\Lib\HttpClient;
use WLib\WConfig;
use WLib\WCtx;

class Alert
{
    public static function send(
        string $alertId,
        array $data,
        string $ip = '',
        string $serviceName = '',
        string $trackId = '',
        string $developerName = '',
    ): void {
        $json = [
            'trackId' => $trackId,
            'alertId' => $alertId,
            'ip' => $ip,
            'time_ms' => intval(microtime(true) * 1000),
            'serviceName' => $serviceName,
            'developerName' => $developerName,
            'data' => $data,
        ];

        self::log($json);
        $str = json_encode($json);
        $sign = md5(config('alert_client.key') . $str);

        go(function () use ($sign, $str) {
            try {
                self::request($sign, $str);
            } catch (\Throwable $e) {
                self::log('alert 没配置url ' . $e->getMessage());
            }
        });
    }

    protected static function request(string $sign, string $data): void
    {
        $url = config('alert_client.server_url');

        if (!$url) {
            self::log('alert 没配置url');
            return;
        }

        $client = new HttpClient($url);
        $client->setHeaders(['sign' => $sign]);
        $client->setData($data);
        $client->execute();
        if ($client->getResponseStatus() != 200) {
            self::log('alert 服务端响应非200状态');
            return;
        }

        $res = $client->getResponseBody();
        $data = json_decode($res, true) ?: [];
        if (arr_get($data, 'code') !== 0) {
            self::log(['message' => 'alert 响应失败:', 'response' => $data, 'src' => $res]);
        }

    }

    protected static function log($str): void
    {
        $dateTime = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        $file = config('alert_client.log_dir') . '/alert-log-' . $dateTime->format('Y-m-d') . '.log';
        $requestId = WCtx::requestId();
        $date = $dateTime->format("Y-m-d H:i:s");
        $category = 'alert';
        $serviceName = WConfig::get('app_name') ?: '';
        $tag = '';
        $time = intval(microtime(true) * 1000);
        $msg = is_string($str) ? json_encode(['message' => $str], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) :
            json_encode($str, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $line = sprintf("[%s] [%s] [%s] [%s] [%s] [%s] %s\n",
            $date,
            $serviceName,
            $category,
            $tag,
            $requestId,
            $time,
            $msg);

        file_put_contents($file, $line, FILE_APPEND);
    }
}
