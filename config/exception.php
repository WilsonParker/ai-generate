<?php

use AIGenerate\Services\Exceptions\Loggers\DatabaseLogger;
use AIGenerate\Services\Exceptions\Models\Exception;

return [
    'logger' => DatabaseLogger::class,
    'model'  => Exception::class,

    'notifications' => [
        'slack' => [
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
            'channel'     => 'error-logs', // Replace with your desired Slack channel
        ],
    ],
];
