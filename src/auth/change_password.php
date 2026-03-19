<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/session.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /src/views/auth/change_password.php');
    exit;
}

$senhaAtual = (string) ($_POST['senha_atual'] ?? '');
$novaSenha = (string) ($_POST['nova_senha'] ?? '');
$confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');
$userId = (int) ($_SESSION['user_id'] ?? 0);

if (isset($_POST['user_id'])) {
    unset($_POST['user_id']);
}

if ($userId <= 0) {
    $_SESSION['password_change_error'] = 'Sessão inválida para trocar senha.';
    header('Location: /src/views/auth/change_password.php');
    exit;
}

if ($senhaAtual === '' || $novaSenha === '' || $confirmarSenha === '') {
    $_SESSION['password_change_error'] = 'Preencha todos os campos para trocar a senha.';
    header('Location: /src/views/auth/change_password.php');
    exit;
}

if ($novaSenha !== $confirmarSenha) {
    $_SESSION['password_change_error'] = 'A confirmação da nova senha não confere.';
    header('Location: /src/views/auth/change_password.php');
    exit;
}

$passwordLength = function_exists('mb_strlen') ? mb_strlen($novaSenha) : strlen($novaSenha);

if ($passwordLength < 6) {
    $_SESSION['password_change_error'] = 'A nova senha deve ter pelo menos 6 caracteres.';
    header('Location: /src/views/auth/change_password.php');
    exit;
}

try {
    $db = (new DatabaseConnection())->conectar();

    $selectStmt = $db->prepare('SELECT senha FROM usuarios WHERE id = ? LIMIT 1');
    if (!$selectStmt) {
        throw new RuntimeException('Falha ao validar senha atual.');
    }

    $selectStmt->bind_param('i', $userId);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;

    if (!$user) {
        $_SESSION['password_change_error'] = 'Utilizador não encontrado.';
        header('Location: /src/views/auth/change_password.php');
        exit;
    }

    $storedPassword = (string) ($user['senha'] ?? '');
    $hashedMatch = password_verify($senhaAtual, $storedPassword);
    $plaintextMatch = hash_equals($storedPassword, $senhaAtual);

    if (!$hashedMatch && !$plaintextMatch) {
        $_SESSION['password_change_error'] = 'A senha atual está incorreta.';
        header('Location: /src/views/auth/change_password.php');
        exit;
    }

    $newHash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $updateStmt = $db->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');

    if (!$updateStmt) {
        throw new RuntimeException('Falha ao preparar atualização de senha.');
    }

    $updateStmt->bind_param('si', $newHash, $userId);
    $updateStmt->execute();

    $_SESSION['password_change_success'] = 'Senha alterada com sucesso.';
    header('Location: /src/views/auth/change_password.php');
    exit;
} catch (Throwable $exception) {
    $_SESSION['password_change_error'] = 'Erro ao trocar senha: ' . $exception->getMessage();
    header('Location: /src/views/auth/change_password.php');
    exit;
}
