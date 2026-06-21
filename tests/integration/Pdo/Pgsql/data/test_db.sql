DROP TABLE IF EXISTS users;

-- ----------------------------
-- Table structure for users
-- ----------------------------
CREATE TABLE users
(
    id         serial       NOT NULL,
    name       varchar(255) NOT NULL,
    username   varchar(255) NOT NULL,
    password   varchar(255) NOT NULL,
    created_at timestamp    NOT NULL,
    updated_at timestamp    NULL DEFAULT NULL,
    PRIMARY KEY (id)
);
