<?php
require_once __DIR__ . '/../../auth/session.php';

requireAuth();

$message = $_SESSION['password_change_success'] ?? null;
unset($_SESSION['password_change_success']);

$error = $_SESSION['password_change_error'] ?? null;
unset($_SESSION['password_change_error']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trocar Senha - Agendamento</title>
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/css/app.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Trocar Senha</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-sm" href="/src/views/dashboard/index.php">Dashboard</a>
      <a class="btn btn-outline-danger btn-sm" href="/src/auth/logout.php">Sair</a>
    </div>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-success" role="alert">
      <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert alert-danger" role="alert">
      <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form method="post" action="/src/auth/change_password.php" class="row g-2" autocomplete="off">
        <div class="col-md-4">
          <label class="form-label">Senha atual</label>
          <input type="password" class="form-control" name="senha_atual" autocomplete="current-password" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Nova senha</label>
          <input type="password" class="form-control" name="nova_senha" autocomplete="new-password" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Confirmar nova senha</label>
          <input type="password" class="form-control" name="confirmar_senha" autocomplete="new-password" required>
        </div>
        <div class="col-12 d-grid d-md-flex justify-content-md-end mt-2">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-key"></i> Atualizar senha
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
