<?php
require_once 'db.php';

$pdo = getConnection();
$input = $_POST;


function publicUser(array $row): array {
    unset($row['password']);
    return $row;
}

function required(array $input, array $fields): void {
    foreach ($fields as $field) {
        if (!isset($input[$field]) || trim((string)$input[$field]) === '') {
            respond(['success' => false, 'message' => "$field is required"], 422);
        }
    }
}

try {
    required($input, ['identifier', 'password']);

    $identifier = trim($input['identifier']);

    $stmt = $pdo->prepare(
        'SELECT * FROM tblusers WHERE username = ? OR email = ? LIMIT 1'
    );
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($input['password'], $user['password'])) {
        respond(['success' => false, 'message' => 'Invalid username/email or password'], 401);
    }

    respond([
        'success' => true,
        'message' => 'Login successful',
        'user' => publicUser($user)
    ]);
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Database error'], 500);
}