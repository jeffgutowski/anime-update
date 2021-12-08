<?php

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

$aws_url = "http://".env("AWS_BUCKET").".s3.".env("AWS_REGION").".amazonaws.com";
$client = new S3Client(
    ["driver" => "s3",
        "key"     => env("AWS_KEY"),
        "secret"  => env("AWS_SECRET"),
        "region"  => env("AWS_REGION"),
        "bucket"  => env("AWS_BUCKET"),
        "url"     => $aws_url,
        "version" => "latest",
        "credentials" => [
            "key"    => env("AWS_KEY"),
            "secret" => env("AWS_SECRET"),
        ],
    ]
);

$adapter    = new AwsS3Adapter($client, env("AWS_BUCKET"));
$filesystem = new Filesystem($adapter, ["url" => $aws_url]);

return [
    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */

    'route' => [
        'prefix'     => config('backpack.base.route_prefix').'/elfinder',
        'middleware' => ['web', 'auth', 'role:admin,access_backend'], //Set to null to disable middleware filter
    ],

    /*
    |--------------------------------------------------------------------------
    | Access filter
    |--------------------------------------------------------------------------
    |
    | Filter callback to check the files
    |
    */

    'access' => 'Barryvdh\Elfinder\Elfinder::checkAccess',

    /*
    |--------------------------------------------------------------------------
    | Roots
    |--------------------------------------------------------------------------
    |
    | By default, the roots file is LocalFileSystem, with the above public dir.
    | If you want custom options, you can set your own roots below.
    |
    */

    'roots' => [
        [
            'driver'     => 'Flysystem',
            'alias'      => 'S3BUCKET',
            'filesystem' => $filesystem,
            'URL'        => $aws_url,
            'tmbURL'     => 'self',
        ]
    ],
];
