<?php

declare(strict_types=1);

return [
    "key" => env("ALERT_CLIENT_KEY"),
    "server_url" => env("ALERT_SERVER_URL", ""),
    "log_dir" => env("ALERT_CLIENT_LOG_DIR", "/data/log"),
];
