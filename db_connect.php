<?php
function getPDO() {
    $host = 'sql108.infinityfree.com';
    $db = 'if0_42305549_ticket';
    $user = 'if0_42305549';
    $pass = '8BuT9qQViCFB4T2';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    return new PDO($dsn, $user, $pass, $options);
}
