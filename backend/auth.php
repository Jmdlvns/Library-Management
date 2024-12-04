<?php
require_once 'config.php';

function register($pdo, $username, $password, $role) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
    $stmt->execute([$username, $hashed_password, $role]);
    return $pdo->lastInsertId();
}

function login($pdo, $username, $password) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

// Usage example:
// $user_id = register($pdo, 'admin', 'admin123', 'admin');
// $user = login($pdo, 'admin', 'admin123');