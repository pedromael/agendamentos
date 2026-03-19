<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        header('Location: /src/views/auth/login.php');
        exit;
    }
}

function currentUserName(): string
{
    return $_SESSION['user_name'] ?? 'Utilizador';
}
