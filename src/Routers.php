<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

function registerRoutes(App $app, $db, $authMiddleware): void {
    // Services
    $userService = new UserService($db, $authMiddleware);
    $groupService = new GroupService($db);
    $messageService = new MessageService($db);

    // create user
    $app->post('/users', function (Request $request, Response $response) use ($userService) {
        $data = $request->getParsedBody();
        return $userService->createUser($response, $data);
    });

    // Auth required group
    $app->group('', function (RouteCollectorProxy $group) use ($userService, $groupService, $messageService) {
        // get user info
        $group->get('/users', function (Request $request, Response $response) use ($userService) {
            return $userService->getUsers($response);
        });

        // create group
        $group->post('/groups', function (Request $request, Response $response) use ($groupService) {
            $data = $request->getParsedBody();
            return $groupService->createGroup($response, $data);
        });
        // get group info
        $group->get('/groups', function (Request $request, Response $response) use ($groupService) {
            return $groupService->getGroups($response);
        });

        // Message routes
        $group->post('/groups/{id}/messages', function (Request $request, Response $response, $args) use ($messageService) {
            $user = $request->getAttribute('user');
            $data = $request->getParsedBody();
            return $messageService->createMessage($response, $user['id'], $args['id'], $data);
        });

        $group->get('/groups/{id}/messages', function (Request $request, Response $response, $args) use ($messageService) {
            return $messageService->getGroupMessages($response, $args['id']);
        });
    })->add($authMiddleware);
}