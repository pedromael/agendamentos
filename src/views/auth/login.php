<?php
require_once __DIR__ . '/../../auth/session.php';

if (isAuthenticated()) {
    header('Location: /src/views/dashboard/index.php');
    exit;
}

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Login - Agendamento</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-grid align-items-center justify-content-center w-100 p-4" style="min-height: 100vh;">
  <div class="card shadow p-4" style="width:100%; max-width:400px;">
    <div class="text-center mb-4">
      <div class="mx-auto rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
        <i class="bi bi-calendar-check text-white" style="font-size:34px;"></i>
      </div>
      <h3 class="mt-3 mb-1 fw-semibold text-secondary">Agendar.com</h3>
      <p class="text-muted mb-0">Faça login para continuar</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form action="/src/auth/login.php" method="post">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" placeholder="Digite seu email" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha</label>
        <input type="password" class="form-control" name="senha" placeholder="Digite sua senha" required>
      </div>
      <button type="submit" class="btn btn-primary w-100 text-white">
        <i class="bi bi-box-arrow-in-right"></i> Entrar
      </button>
    </form>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
