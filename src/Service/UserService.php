<?php

use Psr\Http\Message\ResponseInterface as Response;
require __DIR__ . '/../Middleware/AuthMiddleware.php';

class UserService {
    private $db;
    private AuthMiddleware $authMiddleware;

    public function __construct($db, $authMiddleware) {
        $this->db = $db;
        $this->authMiddleware = $authMiddleware;
    }

    public function createUser(Response $response, $data) {
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
            if ($e->getCode() == 23000) { // SQLite UNIQUE constraint violation
                return $this->jsonResponse($response, ['error' => 'Username already exists'], 409);
            }
            return $this->jsonResponse($response, ['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function getUsers(Response $response) {
        $stmt = $this->db->query("SELECT id, username FROM user");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->jsonResponse($response, $users);
    }

    private function jsonResponse(Response $response, $data, $status = 200) {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
