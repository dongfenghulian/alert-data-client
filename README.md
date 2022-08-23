发布配置


测试地址 http://am.atdev.top/api/api/receive
正式地址 http://am.88488848.net/api/api/receive

密钥 key=

签名  签名内容放到 header里 字段  sign
```
 $str = json_encode($json);
 $sign = md5(config('alert_client.key') . $str);
```

请求数据 data 为对应的字段信息
```
{
	"trackId": "用于跟踪数据",
	"alertId": "告警标识",
	"ip": "",
	"time_ms": "毫秒时间戳",
	"serviceName": "服务名称",
	"developerName": "开发者",
	"data": {
		"message": "自定义字段"
	}
}
```

curl  示例
curl -s -H "sign: aacef04fbeaf3bc898af2d8c3a581aba" -X POST -d '{"trackId":"","alertId":"disk-usage","ip":"","time_ms":1660197046971,"serviceName":"DISK_CHECK","developerName":"","data":{"hostname":"id-cq-market","local_ip":"172.31.69.4","public_ip":"8.215.77.167","disk_usage_percent":"54"}}' http://am.atdev.top/api/api/receive


```
 php bin/hyperf.php vendor:publish dongfenghulian/alert-data-client
```
