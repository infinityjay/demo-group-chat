<?php

use Psr\Http\Message\ResponseInterface as Response;

class MessageService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createMessage(Response $response, $userId, $groupId, $data) {
        $content = $data['content'] ?? '';

        if (empty($content)) {
            return $this->jsonResponse($response, ['error' => 'Message content cannot be empty'], 400);
        }

        // Check if group exists
        $stmt = $this->db->prepare("SELECT id FROM `group` WHERE id = ?");
        $stmt->execute([$groupId]);
        if (!$stmt->fetch()) {
            return $this->jsonResponse($response, ['error' => 'Group does not exist'], 404);
        }

        try {
            $timestamp = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare("INSERT INTO message (user_id, group_id, content, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $groupId, $content, $timestamp]);

            return $this->jsonResponse($response, [
                'id' => $this->db->lastInsertId(),
                'user_id' => $userId,
                'group_id' => $groupId,
                'content' => $content,
                'created_at' => $timestamp
            ], 201);
        } catch (PDOException $e) {
            return $this->jsonResponse($response, ['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function getGroupMessages(Response $response, $groupId) {
        // Check if group exists
        $stmt = $this->db->prepare("SELECT id FROM `group` WHERE id = ?");
        $stmt->execute([$groupId]);
        if (!$stmt->fetch()) {
            return $this->jsonResponse($response, ['error' => 'Group does not exist'], 404);
        }

        $stmt = $this->db->prepare("
            SELECT m.id, m.content, m.created_at, m.user_id, u.username
            FROM message m
            JOIN user u ON m.user_id = u.id
            WHERE m.group_id = ?
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$groupId]);
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