<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

require __DIR__ . '/../src/Middleware/AuthMiddleware.php';
require __DIR__ . '/../src/Action/HomeAction.php';
require_once __DIR__ . '/../src/Service/UserService.php';
require_once __DIR__ . '/../src/Service/GroupService.php';
require_once __DIR__ . '/../src/Service/MessageService.php';

return function (App $app) {
    // get db from container
    $container = $app->getContainer();
    $db = $container->get('db');
    // instance AuthMiddleware
    $authMiddleware = new AuthMiddleware($db);
    // instance service
    $userService = new UserService($db, $authMiddleware);
    $groupService = new GroupService($db);
    $messageService = new MessageService($db);

    // register routes, add AuthMiddleware
    $app->get('/', HomeAction::class);
    $app->get('/hello', [HomeAction::class, 'SendHello'])->add($authMiddleware);

    // create user, don't need auth verification
    $app->post('/user', [$userService, 'createUser']);
    // get user info
    $app->get('/users', [$userService, 'getUser'])->add($authMiddleware);

    // create group
    $app->post('/group', [$groupService, 'createGroup'])->add($authMiddleware);
    // get group info
    $app->get('/groups', [$groupService, 'getGroup'])->add($authMiddleware);

    // create message
    $app->post('/groups/{id}/messages', [$messageService, 'createMessage'])->add($authMiddleware);
    // get all message of group
    $app->get('/groups/{id}/messages', [$messageService, 'getGroupMessages'])->add($authMiddleware);

};
