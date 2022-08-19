<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\Cases;

use AlertClient\Alert;

/**
 * @internal
 * @coversNothing
 */
class ExampleTest extends AbstractTestCase
{
    public function testExample()
    {

        // composer install dongfenghulian/alert-data-client
        // .env 文件里加入
        // ALERT_CLIENT_KEY=d5u0c0c5a2a5d0j5g0n0a4i1j2d3k0b3
        // ALERT_SERVER_URL=http://am.88488848.net/api/api/receive
        // 复制  publish/alert_client.php 到 config/autoload/alert_client.php

        $alertId = "50NZA-DISBURSE";
        $alertData = [
            "idNumber" => "赋值",
            "mobile" => "赋值",
        ];
        Alert::send($alertId, $alertData);
        $this->assertTrue(true);
    }
}
