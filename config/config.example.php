<?php

define('BASE_URL', '/restaurant-management-system/public/');
define('ASSET_URL', '/restaurant-management-system/assets/');

define('DB_HOST', 'localhost');
define('DB_USER', 'your_database_username');
define('DB_PASSWORD', 'your_database_password');
define('DB_NAME', 'your_database_name');

function conn()
{
    $link = mysqli_connect(
        DB_HOST,
        DB_USER,
        DB_PASSWORD,
        DB_NAME
    );

    if (!$link) {
        die('Database connection failed: ' . mysqli_connect_error());
    }

    mysqli_set_charset($link, 'utf8mb4');

    return $link;
}