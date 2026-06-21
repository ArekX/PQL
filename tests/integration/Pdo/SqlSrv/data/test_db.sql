IF OBJECT_ID('users', 'U') IS NOT NULL
    DROP TABLE users;

CREATE TABLE users
(
    id         INT IDENTITY (1,1) PRIMARY KEY,
    name       VARCHAR(255) NOT NULL,
    username   VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    -- Stored as VARCHAR so date values round-trip identically across PDO
    -- transports (the sqlsrv driver reformats DATETIME values, e.g. adding
    -- milliseconds, while FreeTDS returns them verbatim).
    created_at VARCHAR(255) NOT NULL,
    updated_at VARCHAR(255) NULL
);
