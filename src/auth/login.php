<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /src/views/auth/login.php');
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');

if ($email === '' || $senha === '') {
    $_SESSION['login_error'] = 'Preencha email e senha.';
    header('Location: /src/views/auth/login.php');
    exit;
}

try {
    $db = (new DatabaseConnection())->conectar();
    $stmt = $db->prepare('SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1');

    if (!$stmt) {
        throw new RuntimeException('Falha ao preparar autenticação.');
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;

    if (!$user) {
        $_SESSION['login_error'] = 'Credenciais inválidas.';
        header('Location: /src/views/auth/login.php');
        exit;
    }

    $storedPassword = (string) $user['senha'];
    $hashedMatch = password_verify($senha, $storedPassword);
    $plaintextMatch = hash_equals($storedPassword, $senha);
    $passwordMatches = $hashedMatch || $plaintextMatch;

    if (!$passwordMatches) {
        $_SESSION['login_error'] = 'Credenciais inválidas.';
        header('Location: /src/views/auth/login.php');
        exit;
    }

    if ($plaintextMatch) {
        $newHash = password_hash($senha, PASSWORD_DEFAULT);
        $updateStmt = $db->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');

        if ($updateStmt) {
            $userId = (int) $user['id'];
            $updateStmt->bind_param('si', $newHash, $userId);
            $updateStmt->execute();
        }
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = (string) $user['nome'];
    $_SESSION['user_email'] = (string) $user['email'];

    header('Location: /src/views/dashboard/index.php');
    exit;
} catch (Throwable $exception) {
    $_SESSION['login_error'] = 'Erro ao autenticar: ' . $exception->getMessage();
    header('Location: /src/views/auth/login.php');
    exit;
}
