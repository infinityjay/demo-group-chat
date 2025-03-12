<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class UserService {
    private $db;
    private AuthMiddleware $authMiddleware;

    public function __construct($db, $authMiddleware) {
        $this->db = $db;
        $this->authMiddleware = $authMiddleware;
    }

    public function createUser(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        if (empty($username)) {
            return $this->jsonResponse($response, ['error' => 'Username cannot be empty'], 400);
        }

        try {
            $token = $this->authMiddleware->generateToken($username);

            $stmt = $this->db->prepare("INSERT INTO user (username, token) VALUES (?, ?)");
            $stmt->execute([$username, $token]);

            return $this->jsonResponse($response, [
                'id' => $this->db->lastInsertId(),
                'username' => $username,
                'token' => $token
            ], 201);
        } catch (PDOException $e) {
            // unique key error
            if ($e->getCode() == 23000) {
                return $this->jsonResponse($response, ['error' => 'Username already exists'], 409);
            }
            // server error
            return $this->jsonResponse($response, ['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function getUser(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $userId = $params['id'] ?? null;
        if (empty($userId)) {
            return $this->jsonResponse($response, ['error' => 'userId cannot be empty'], 400);
        }

        $stmt = $this->db->prepare("SELECT id, username FROM user WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->jsonResponse($response, ['error' => 'User not found'], 404);
        }

        return $this->jsonResponse($response, $user);

    }

    private function jsonResponse(Response $response, $data, $status = 200) {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
