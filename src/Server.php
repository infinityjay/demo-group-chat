<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/Middleware/AuthMiddleware.php';
require __DIR__ . '/routers.php';
require __DIR__ . '/DB/init.php';


$app = AppFactory::create();

// init a sqlite DB connection
$db = initializeDatabase();

// add auth check Middleware
$authMiddleware = new AuthMiddleware($db);
$app->add($authMiddleware);

// add routes
registerRoutes($app, $db, $authMiddleware);
$app->addErrorMiddleware(true, true, true);

// run the app
$app->run();
