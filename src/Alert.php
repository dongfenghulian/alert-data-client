<?php

declare(strict_types=1);

namespace AlertClient;

use WLib\Lib\HttpClient;

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
        self::log($data);
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
        }
    }

    protected static function log(string $str)
    {
        $dateTime = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        $file = config('alert_client.log_dir') . '/alert-log-' . $dateTime->format('Y-m-d') . '.log';
        file_put_contents($file, $dateTime->format('Y-m-d H:i:s') . ' ' . $str . "\n", FILE_APPEND);
    }
}
