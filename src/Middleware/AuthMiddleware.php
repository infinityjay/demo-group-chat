<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $token = $request->getHeaderLine('Authorization');
        if (empty($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Authentication required']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $stmt = $this->db->prepare("SELECT id, username FROM user WHERE token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Add user data to request attributes
        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }

    public function generateToken($username): string {
        return bin2hex(random_bytes(16));
    }
}