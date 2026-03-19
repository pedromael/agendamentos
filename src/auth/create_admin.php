<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/session.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /src/views/usuarios/index.php');
    exit;
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');
$confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

if ($nome === '' || $email === '' || $senha === '' || $confirmarSenha === '') {
    $_SESSION['admin_create_error'] = 'Preencha todos os campos do novo administrador.';
    header('Location: /src/views/usuarios/index.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['admin_create_error'] = 'Informe um email válido para o novo administrador.';
    header('Location: /src/views/usuarios/index.php');
    exit;
}

if ($senha !== $confirmarSenha) {
    $_SESSION['admin_create_error'] = 'A confirmação da senha não confere.';
    header('Location: /src/views/usuarios/index.php');
    exit;
}

$passwordLength = function_exists('mb_strlen') ? mb_strlen($senha) : strlen($senha);

if ($passwordLength < 6) {
    $_SESSION['admin_create_error'] = 'A senha deve ter pelo menos 6 caracteres.';
    header('Location: /src/views/usuarios/index.php');
    exit;
}

try {
    $db = (new DatabaseConnection())->conectar();

    $checkStmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    if (!$checkStmt) {
        throw new RuntimeException('Falha ao validar email do administrador.');
    }

    $checkStmt->bind_param('s', $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult && $checkResult->fetch_assoc()) {
        $_SESSION['admin_create_error'] = 'Já existe um administrador com esse email.';
        header('Location: /src/views/usuarios/index.php');
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');

    if (!$stmt) {
        throw new RuntimeException('Falha ao preparar cadastro de administrador.');
    }

    $stmt->bind_param('sss', $nome, $email, $senhaHash);
    $stmt->execute();

    $_SESSION['admin_create_success'] = 'Novo administrador cadastrado com sucesso.';
    header('Location: /src/views/usuarios/index.php');
    exit;
} catch (Throwable $exception) {
    $_SESSION['admin_create_error'] = 'Erro ao cadastrar administrador: ' . $exception->getMessage();
    header('Location: /src/views/usuarios/index.php');
    exit;
}
