<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GroupService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createGroup(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $groupname = $data['groupname'] ?? '';
        $userId = $data['userId'] ?? null;

        if (empty($groupname)) {
            return $this->jsonResponse($response, ['error' => 'Group name cannot be empty'], 400);
        }
        if (empty($userId)) {
            return $this->jsonResponse($response, ['error' => 'Create user id cannot be empty'], 400);
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO `group` (groupname, create_user_id) VALUES (?, ?)");
            $stmt->execute([$groupname, $userId]);

            return $this->jsonResponse($response, [
                'id' => $this->db->lastInsertId(),
                'groupname' => $groupname
            ], 201);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return $this->jsonResponse($response, ['error' => 'Group already exists'], 409);
            }
            return $this->jsonResponse($response, ['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function getGroup(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $groupId = $params['id'] ?? null;
        if (empty($groupId)) {
            return $this->jsonResponse($response, ['error' => 'groupId cannot be empty'], 400);
        }

        $stmt = $this->db->prepare("SELECT id, groupname, create_user_id FROM `group` WHERE id = ?");
        $stmt->execute([$groupId]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$group) {
            return $this->jsonResponse($response, ['error' => 'group not found'], 404);
        }

        return $this->jsonResponse($response, $group);
    }

    private function jsonResponse(Response $response, $data, $status = 200)
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}