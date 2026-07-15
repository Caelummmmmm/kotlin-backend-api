<?php
require_once 'db.php';

$pdo = getConnection();

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?? []; 

function required(array $input, array $fields): void {
    foreach ($fields as $field) {
        if (!isset($input[$field]) || trim((string)$input[$field]) === '') {
            respond(['success' => false, 'message' => "$field is required"], 422);
        }
    }
}

try {
    required($input, ['username', 'last_name', 'first_name', 'email', 'password']);

    $username = trim($input['username']);
    $email = trim($input['email']);

    $stmt = $pdo->prepare('SELECT id FROM tblusers WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$username, $email]);

    if ($stmt->fetch()) {
        respond(['success' => false, 'message' => 'Username or email already exists'], 409);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO tblusers (username, last_name, first_name, middle_name, email, password, photo) 
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );

    $stmt->execute([
        $username,
        trim($input['last_name']),
        trim($input['first_name']),
        trim($input['middle_name'] ?? ''),
        $email,
        password_hash($input['password'], PASSWORD_DEFAULT),
        trim($input['photo'] ?? '')
    ]);

    respond(['success' => true, 'message' => 'Registered successfully'], 201);
} catch (Throwable $e) {
    respond(['success' => false, 'message' => $e->getMessage()], 500);
}
