<?php

return [
    'esrb' => [
        'EC'    => env("S3_BUCKET_URL").'ratings/EC.png',
        'E'     => env("S3_BUCKET_URL").'ratings/E.png',
        'E10'   => env("S3_BUCKET_URL").'ratings/E10.png',
        'T'     => env("S3_BUCKET_URL").'ratings/T.png',
        'M'     => env("S3_BUCKET_URL").'ratings/M.png',
        'AO'    => env("S3_BUCKET_URL").'ratings/AO.png',
        'RP'    => env("S3_BUCKET_URL").'ratings/RP.png',
    ],
    'pegi' => [
        '3'     => env("S3_BUCKET_URL").'ratings/3.png',
        '7'     => env("S3_BUCKET_URL").'ratings/7.png',
        '12'    => env("S3_BUCKET_URL").'ratings/12.png',
        '16'    => env("S3_BUCKET_URL").'ratings/16.png',
        '18'    => env("S3_BUCKET_URL").'ratings/18.png',
    ]
];
