<?php

function initializeDatabase(): PDO {
    $dbPath = __DIR__ . '/chat.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = [
        "CREATE TABLE IF NOT EXISTS user (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            username   TEXT UNIQUE NOT NULL,
            token      TEXT UNIQUE NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS `group` (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            groupname   TEXT UNIQUE NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS message (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id     INTEGER NOT NULL,
            group_id    INTEGER NOT NULL,
            content     TEXT NOT NULL,
            created_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )"
    ];

    foreach ($tables as $sql) {
        $db->exec($sql);
    }
    echo 'The database is initiated.';

    return $db;
}