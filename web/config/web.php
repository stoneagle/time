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
                "GET frontend/project-api/data"             => "frontend/project-api/data",
                "POST frontend/project-api/task"            => "frontend/project-api/task-add",
                "PUT frontend/project-api/task/<id:\d+>"    => "frontend/project-api/task-update",
                "DELETE frontend/project-api/task/<id:\d+>" => "frontend/project-api/task-del",

                "POST frontend/project-api/link"                => "frontend/project-api/link-add",
                "PUT frontend/project-api/link/<linkid:\d+>"    => "frontend/project-api/link-update",
                "DELETE frontend/project-api/link/<linkid:\d+>" => "frontend/project-api/link-del",

                "GET frontend/action-api/list/<type:\d+>"    => "frontend/action-api/list",
                "GET frontend/action-api/data/<task_id:\d+>" => "frontend/action-api/data",
                "POST frontend/action-api/update/<id:\d+>"   => "frontend/action-api/update",
                "PUT frontend/action-api/<id:\d+>"           => "frontend/action-api/update",
                "DELETE frontend/action-api/<id:\d+>"        => "frontend/action-api/del",

                "POST frontend/plan-api/<id:\d+>"   => "frontend/plan-api/add",
                "PUT frontend/plan-api/<id:\d+>"    => "frontend/plan-api/update",
                "DELETE frontend/plan-api/<id:\d+>" => "frontend/plan-api/del",

                "GET frontend/task-api/plan/<year:\d+>/<week:\d+>"    => "frontend/task-api/plan",
                "PUT frontend/plan-api/plan/<task_id:\d+>"    => "frontend/plan-api/task-oper",
                "GET frontend/plan-api/chart/<year:\d+>/<week:\d+>"    => "frontend/plan-api/chart",

                "POST frontend/scheduler-api/<sid:\d+>"  => "frontend/scheduler-api/add",
                "PUT frontend/scheduler-api/<id:\d+>"    => "frontend/scheduler-api/update",
                "DELETE frontend/scheduler-api/<id:\d+>" => "frontend/scheduler-api/del",
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
