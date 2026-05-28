<?php

return [
    'db_dsn'      => getenv('DB_DSN') ?: 'pgsql:host=localhost;port=5432;dbname=url_shortener',
    'db_user'     => getenv('DB_USER') ?: 'postgres',
    'db_password' => getenv('DB_PASSWORD') ?: '2783641',

    'code_length'     => 6,
    'max_code_length' => 12,
];