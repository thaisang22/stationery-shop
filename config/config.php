<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Stationery Store',

        // PROD is /
        'base_url' => '/stationery',
    ],

    'database' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'stationery_shop',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],

    'mail' => [
        'driver' => 'smtp', // 'smtp' hoặc 'mail'
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls', // 'tls', 'ssl', hoặc 'none'
        'username' => 'thaisang06072004@gmail.com',
        'password' => 'clpt insg erqs fwqq', // Mật khẩu ứng dụng Gmail 
        'from_address' => 'thaisang06072004@gmail.com',
        'from_name' => 'Mộc Nhiên Stationery',
        'smtp_auth' => true,
    ],
    'sepay' => [
        'secret' => 'zxcasdqwe1',
        'api_url' => 'https://userapi-sandbox.sepay.vn/v2',
        'api_token' => '92ISLG0CEZQLZVYSXGAFEKLJVAU7ONY4ES87N0WLRVKPODMAH1CCYTIHJJ5BJPSR',
        'bank_account' => 'SBSEPAYNLVJQGCXLXEN',
        'bank_name' => 'BIDV',
        'account_holder' => 'VO THAI SANG',
        'template' => 'compact',
    ],
    'Ghn' => [
        'token' => 'fd115825-bcfc-11f1-b944-42faa0331861',
        'api_url' => 'https://dev-online-gateway.ghn.vn',
        'shop_id' => '2536158'
    ]
];

