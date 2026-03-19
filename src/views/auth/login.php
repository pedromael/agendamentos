<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../../config/database.php';

if (isAuthenticated()) {
    header('Location: /src/views/dashboard/index.php');
    exit;
}

$setupError = null;

try {
  $db = (new DatabaseConnection())->conectar();
  $resCount = $db->query('SELECT COUNT(*) AS total FROM usuarios');
  $totalUsuarios = $resCount ? (int) ($resCount->fetch_assoc()['total'] ?? 0) : 0;

  if ($totalUsuarios === 0) {
    header('Location: /src/views/auth/register.php');
    exit;
  }
} catch (Throwable $exception) {
  $setupError = 'Não foi possível validar a conta administrativa: ' . $exception->getMessage();
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
  <link href="../../../public/css/app.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<div class="container py-4 py-md-5 d-flex align-items-center" style="min-height: 100vh;">
  <div class="card border-0 shadow-lg overflow-hidden w-100 mx-auto" style="max-width: 900px;">
    <div class="row g-0">
      <div class="col-md-5 d-none d-md-flex bg-primary-subtle">
        <div class="p-4 p-lg-5 d-flex flex-column justify-content-between w-100">
          <div>
            <span class="badge text-bg-primary mb-3">Painel administrativo</span>
            <h2 class="h4 mb-2">Agendar.com</h2>
            <p class="text-secondary mb-0">Acesse sua conta para gerir agendamentos, publicações e contas administrativas.</p>
          </div>
          <div class="text-secondary small d-flex align-items-center gap-2">
            <i class="bi bi-shield-check"></i>
            Sessão segura
          </div>
        </div>
      </div>

      <div class="col-12 col-md-7">
        <div class="p-4 p-lg-5">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <h1 class="h4 mb-1">Entrar</h1>
              <p class="text-muted mb-0">Use suas credenciais para continuar.</p>
            </div>
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:46px; height:46px;">
              <i class="bi bi-box-arrow-in-right text-white"></i>
            </div>
          </div>

          <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
              <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
          <?php endif; ?>

          <?php if ($setupError): ?>
            <div class="alert alert-warning" role="alert">
              <?= htmlspecialchars($setupError, ENT_QUOTES, 'UTF-8') ?>
            </div>
          <?php endif; ?>

          <form action="/src/auth/login.php" method="post" class="mt-3">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email" placeholder="Digite seu email" autocomplete="email" required>
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label">Senha</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" name="senha" placeholder="Digite sua senha" autocomplete="current-password" required>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">
              <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>
          </form>

          <div class="mt-3 text-center">
            <a href="/src/views/public/index.php" class="text-decoration-none small">Voltar para comunicados públicos</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
