CREATE TABLE users
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    name       VARCHAR(255) NOT NULL,
    username   VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at DATETIME     NOT NULL,
    updated_at DATETIME DEFAULT NULL
);
