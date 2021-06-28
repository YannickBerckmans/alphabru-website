<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

//(Dotenv\Dotenv::create(__DIR__.'\..'))->load();

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => false, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'smtp_host' =>  "ssl0.ovh.net",
            'smtp_username' =>  "info@alphabru.be",
            'smtp_password' => "So4v1784v1",
            'smtp_from' =>  "info@alphabru.be",
            'smtp_to' =>	"info@alphabru.be", 
			'smtp_port' => 587,
        ],
    ]);
};
