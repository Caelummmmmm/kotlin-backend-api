<?php
require_once 'db.php';
 
$pdo = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$input = json_decode(file_get_contents('php://input'), true) ?? [];
 
function publicUser(array $row): array {
    unset($row['password']);
    return $row;
}
 
function required(array $input, array $fields): void {
    foreach ($fields as $field) {
        if (!isset($input[$field]) || trim((string) $input[$field]) === '') {
            respond(['success' => false, 'message' => "$field is required"], 422);
        }
    }
}
 
try {
    if ($method === 'GET' && $id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM tblusers WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) respond(['success' => false, 'message' => 'User not found'], 404);
        respond(['success' => true, 'user' => publicUser($user)]);
    }
 
    if ($method === 'GET') {
        $stmt = $pdo->query('SELECT * FROM tblusers ORDER BY id DESC');
        $users = array_map('publicUser', $stmt->fetchAll());
        respond(['success' => true, 'users' => $users]);
    }
 
    if ($method === 'POST') {
        required($input, ['username', 'last_name', 'first_name', 'email', 'password']);
        $stmt = $pdo->prepare(
            'INSERT INTO tblusers (username, last_name, first_name, middle_name, email, password, photo)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            trim($input['username']),
            trim($input['last_name']),
            trim($input['first_name']),
            trim($input['middle_name'] ?? ''),
            trim($input['email']),
            password_hash($input['password'], PASSWORD_DEFAULT),
            trim($input['photo'] ?? '')
        ]);
        respond(['success' => true, 'message' => 'User created', 'id' => (int) $pdo->lastInsertId()], 201);
    }
 
    if ($method === 'PUT' && $id > 0) {
        required($input, ['username', 'last_name', 'first_name', 'email']);
 
        if (!empty($input['password'])) {
            $stmt = $pdo->prepare(
                'UPDATE tblusers
                 SET username=?, last_name=?, first_name=?, middle_name=?, email=?, password=?, photo=?
                 WHERE id=?'
            );
            $stmt->execute([
                trim($input['username']),
                trim($input['last_name']),
                trim($input['first_name']),
                trim($input['middle_name'] ?? ''),
                trim($input['email']),
                password_hash($input['password'], PASSWORD_DEFAULT),
                trim($input['photo'] ?? ''),
                $id
            ]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE tblusers
                 SET username=?, last_name=?, first_name=?, middle_name=?, email=?, photo=?
                 WHERE id=?'
            );
            $stmt->execute([
                trim($input['username']),
                trim($input['last_name']),
                trim($input['first_name']),
                trim($input['middle_name'] ?? ''),
                trim($input['email']),
                trim($input['photo'] ?? ''),
                $id
            ]);
        }
 
        respond(['success' => true, 'message' => 'User updated']);
    }
 
    if ($method === 'DELETE' && $id > 0) {
        $stmt = $pdo->prepare('DELETE FROM tblusers WHERE id = ?');
        $stmt->execute([$id]);
        respond(['success' => true, 'message' => 'User deleted']);
    }
 
    respond(['success' => false, 'message' => 'Unsupported request'], 405);
} catch (PDOException $e) {
    respond(['success' => false, 'message' => 'Database error'], 500);
}
