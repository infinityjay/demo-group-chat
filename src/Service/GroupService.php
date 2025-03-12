<?php

use Psr\Http\Message\ResponseInterface as Response;

class GroupService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createGroup(Response $response, $data) {
        $groupname = $data['groupname'] ?? '';

        if (empty($groupname)) {
            return $this->jsonResponse($response, ['error' => 'Group name cannot be empty'], 400);
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO `group` (groupname) VALUES (?)");
            $stmt->execute([$groupname]);

            return $this->jsonResponse($response, [
                'id' => $this->db->lastInsertId(),
                'groupname' => $groupname
            ], 201);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // SQLite UNIQUE constraint violation
                return $this->jsonResponse($response, ['error' => 'Group already exists'], 409);
            }
            return $this->jsonResponse($response, ['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function getGroups(Response $response) {
        $stmt = $this->db->query("SELECT id, groupname FROM `group`");
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->jsonResponse($response, $groups);
    }

    private function jsonResponse(Response $response, $data, $status = 200) {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}