<?php
function getConnection(): PDO {
    // 1. Updated cloud credentials from your Aiven dashboard
    $host = 'kotlin-mysql-db-factgroupkotlin.d.aivencloud.com';
    $port = 24980; // Explicit cloud port
    $db   = 'defaultdb'; // Aiven default database layout
    $user = 'avnadmin';
    
    // 2. Click the eye icon next to Password on your Aiven panel to copy your real string
    $pass = 'AVNS_wWn3H-UARohoi503bCB'; 

    return new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            
            // 3. Mandatory Aiven security properties (Fixes Public Key Retrieval and SSL errors)
            PDO::MYSQL_ATTR_SSL_CA => true,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ]
    );
}

function respond(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
