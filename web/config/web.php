<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => '/frontend/site/index',
    // dektrium用户系统
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin']
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'tI6tV_g6JWEQb6f2XKOJn9yWgYnl8DYW',
        ],
        /* 'session' => [ */
        /*     'class' => 'yii\web\CacheSession', */ 
        /* ], */
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        /* 'user' => [ */
        /*     'identityClass' => 'app\models\User', */
        /*     'enableAutoLogin' => true, */
        /* ], */
        'errorHandler' => [
            'errorAction' => 'frontend/site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            //'useFileTransport' => true,
            // dektrium用户系统使用的邮件发送
            'viewPath' => '@app/mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.live.com',
                'username' => 'b40750428@hotmail.com',
                'password' => '719639268',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // gantt
                "GET frontend/gantt-api/data" => "frontend/gantt-api/data",
                "POST frontend/gantt-api/task" => "frontend/gantt-api/task-add",
                "PUT frontend/gantt-api/task/<taskid:\d+>" => "frontend/gantt-api/task-update",
                "DELETE frontend/gantt-api/task/<taskid:\d+>" => "frontend/gantt-api/task-del",

                "POST frontend/gantt-api/link" => "frontend/gantt-api/link-add",
                "PUT frontend/gantt-api/link/<linkid:\d+>" => "frontend/gantt-api/link-update",
                "DELETE frontend/gantt-api/link/<linkid:\d+>" => "frontend/gantt-api/link-del",
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.*', '10.18.121.240'],
    ];

    $config['modules']['gridview'] = [
        'class' => '\kartik\grid\Module',
        // 'downloadAction' => 'export',
    ];
}

return $config;
