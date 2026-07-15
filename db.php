<?php
function getConnection(): PDO {
    $host = 'kotlin-mysql-db-factgroupkotlin.d.aivencloud.com';
    $port = 24980; 
    $db   = 'defaultdb'; 
    $user = 'avnadmin';
    $pass = 'AVNS_wWn3H-UARohoi503bCB'; // Double check this matches your Aiven dashboard!

    // 1. Create the base PDO instance without driver array options
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    // 2. Set standard attributes safely
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    return $pdo;
}

function respond(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
