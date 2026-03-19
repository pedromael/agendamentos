<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /src/views/auth/register.php');
    exit;
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');
$confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

if ($nome === '' || $email === '' || $senha === '' || $confirmarSenha === '') {
    $_SESSION['register_error'] = 'Preencha todos os campos.';
    header('Location: /src/views/auth/register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = 'Informe um email válido.';
    header('Location: /src/views/auth/register.php');
    exit;
}

if ($senha !== $confirmarSenha) {
    $_SESSION['register_error'] = 'A confirmação da senha não confere.';
    header('Location: /src/views/auth/register.php');
    exit;
}

$passwordLength = function_exists('mb_strlen') ? mb_strlen($senha) : strlen($senha);

if ($passwordLength < 6) {
    $_SESSION['register_error'] = 'A senha deve ter pelo menos 6 caracteres.';
    header('Location: /src/views/auth/register.php');
    exit;
}

try {
    $db = (new DatabaseConnection())->conectar();

    $resCount = $db->query('SELECT COUNT(*) AS total FROM usuarios');
    $totalUsuarios = $resCount ? (int) ($resCount->fetch_assoc()['total'] ?? 0) : 0;

    if ($totalUsuarios > 0) {
        $_SESSION['login_error'] = 'A conta administrativa inicial já foi criada. Faça login para continuar.';
        header('Location: /src/views/auth/login.php');
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');

    if (!$stmt) {
        throw new RuntimeException('Falha ao preparar o registro administrativo.');
    }

    $stmt->bind_param('sss', $nome, $email, $senhaHash);
    $stmt->execute();

    $userId = (int) $stmt->insert_id;

    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $nome;
    $_SESSION['user_email'] = $email;

    header('Location: /src/views/dashboard/index.php');
    exit;
} catch (Throwable $exception) {
    $_SESSION['register_error'] = 'Erro ao criar conta administrativa: ' . $exception->getMessage();
    header('Location: /src/views/auth/register.php');
    exit;
}
