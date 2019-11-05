<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

(Dotenv\Dotenv::create(__DIR__.'\..'))->load();

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
            'smtp_host' =>  getenv('smtp_host'),
            'smtp_username' =>  getenv('smtp_username'),
            'smtp_password' => getenv('smtp_password'),
            'smtp_from' =>  getenv('smtp_from'),
            'smtp_to' =>  getenv('smtp_to'), 
			'smtp_port' => getenv('smtp_port'),
        ],
    ]);
};
