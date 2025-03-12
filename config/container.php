<?php

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    // Database connection
    'db' => function (ContainerInterface $container) {
        require_once __DIR__ . '/../src/DB/init.php';
        return initializeDatabase();
    },

    App::class => function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);

        // Load routes
        $registerRoutes = require __DIR__ . '/routes.php';
        $registerRoutes($app);

        // Load middleware
        $registerMiddleware = require __DIR__ . '/middleware.php';
        $registerMiddleware($app);

        return $app;
    },
];