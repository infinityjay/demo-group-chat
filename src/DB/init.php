<?php

/**
 * Init the sqlite database the db file can be found under root directory.
 *
 * @return PDO
 */
function initializeDatabase(): PDO {
    static $dbInstance = null;

    if ($dbInstance !== null) {
        return $dbInstance;
    }

    $dbPath = __DIR__ . '/../../chat.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = [
        "CREATE TABLE IF NOT EXISTS user (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            username   TEXT UNIQUE NOT NULL,
            token      TEXT UNIQUE NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS `group` (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            groupname       TEXT UNIQUE NOT NULL,
            create_user_id  INTEGER NOT NULL,
            created_at      TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS message (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id     INTEGER NOT NULL,
            group_id    INTEGER NOT NULL,
            content     TEXT NOT NULL,
            created_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS user_group (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id     INTEGER NOT NULL,
            group_id    INTEGER NOT NULL,
            created_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT unq UNIQUE (user_id, group_id)
        )"
    ];

    foreach ($tables as $sql) {
        $db->exec($sql);
    }

    $dbInstance = $db;
    return $db;
}