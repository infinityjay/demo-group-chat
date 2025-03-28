<?php

use Slim\App;

require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';
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

    // create user, don't need auth verification
    $app->post('/user', [$userService, 'createUser']);
    // get user info
    $app->get('/user', [$userService, 'getUser'])->add($authMiddleware);
    // user join group
    $app->get('/user/group/{id}', [$userService, 'joinGroup'])->add($authMiddleware);

    // create group
    $app->post('/group', [$groupService, 'createGroup'])->add($authMiddleware);
    // get group info
    $app->get('/group', [$groupService, 'getGroup'])->add($authMiddleware);

    // create message
    $app->post('/message', [$messageService, 'createMessage'])->add($authMiddleware);
    // get all message of a group
    $app->get('/message/group/{id}', [$messageService, 'getGroupMessages'])->add($authMiddleware);

};
