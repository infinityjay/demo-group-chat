<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MessageService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createMessage(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $content = $data['content'] ?? '';
        $userId = $data['userId'] ?? null;
        $groupId = $data['groupId'] ?? null;
        // parameter check
        if (empty($content)) {
            return $this->jsonResponse($response, ['error' => 'Message content cannot be empty'], 400);
        }
        if (empty($userId)) {
            return $this->jsonResponse($response, ['error' => 'user Id cannot be empty'], 400);
        }
        if (empty($groupId)) {
            return $this->jsonResponse($response, ['error' => 'group Id cannot be empty'], 400);
        }

        // Check if group exists
        $stmt = $this->db->prepare("SELECT id FROM `group` WHERE id = ?");
        $stmt->execute([$groupId]);
        if (!$stmt->fetch()) {
            return $this->jsonResponse($response, ['error' => 'Group does not exist'], 404);
        }
        // Check if user exists
        $stmt = $this->db->prepare("SELECT id FROM `user` WHERE id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            return $this->jsonResponse($response, ['error' => 'User does not exist'], 404);
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO message (user_id, group_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $groupId, $content]);

            return $this->jsonResponse($response, [
                'id' => $this->db->lastInsertId(),
                'user_id' => $userId,
                'group_id' => $groupId,
                'content' => $content
            ], 201);
        } catch (PDOException $e) {
            return $this->jsonResponse($response, ['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function getGroupMessages(Request $request, Response $response, array $args) {
        // Get the path parameter from $args
        $groupId = $args['id'] ?? null;
        // Get query parameters from the request
        $queryParams = $request->getQueryParams();
        $limit = $queryParams['limit'] ?? 10;
        $offset = $queryParams['offset'] ?? 0;
        $user = $request->getAttribute('user');
        $userId = $user['id'] ?? null;

        // parameter check
        if (!$groupId) {
            return $this->jsonResponse($response, ['error' => 'Group ID is required'], 400);
        }
        if (!$userId) {
            return $this->jsonResponse($response, ['error' => 'User ID is required'], 400);
        }
        // Check if group exists
        $stmt = $this->db->prepare("SELECT id FROM `group` WHERE id = ?");
        $stmt->execute([$groupId]);
        if (!$stmt->fetch()) {
            return $this->jsonResponse($response, ['error' => 'Group does not exist'], 404);
        }
        // Check if user in the group
        $stmt = $this->db->prepare("SELECT id FROM `user_group` WHERE user_id = ? AND group_id = ?");
        $stmt->execute([$userId, $groupId]);
        if (!$stmt->fetch()) {
            return $this->jsonResponse($response, ['error' => 'User is not in the group'], 404);
        }

        $stmt = $this->db->prepare("
            SELECT m.id, m.content, m.created_at, m.user_id, u.username
            FROM message m
            JOIN user u ON m.user_id = u.id
            WHERE m.group_id = ?
            ORDER BY m.created_at ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$groupId, $limit, $offset]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->jsonResponse($response, $messages);
    }

    private function jsonResponse(Response $response, $data, $status = 200) {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}