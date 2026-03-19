<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../../config/database.php';

if (isAuthenticated()) {
    header('Location: /src/views/dashboard/index.php');
    exit;
}

$error = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);

$setupError = null;

try {
    $db = (new DatabaseConnection())->conectar();
    $resCount = $db->query('SELECT COUNT(*) AS total FROM usuarios');
    $totalUsuarios = $resCount ? (int) ($resCount->fetch_assoc()['total'] ?? 0) : 0;

    if ($totalUsuarios > 0) {
        $_SESSION['login_error'] = 'A conta administrativa já está configurada. Faça login para continuar.';
        header('Location: /src/views/auth/login.php');
        exit;
    }
} catch (Throwable $exception) {
    $setupError = 'Não foi possível validar o estado inicial do sistema: ' . $exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Registro Inicial - Agendamento</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/css/app.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-grid align-items-center justify-content-center w-100 p-4" style="min-height: 100vh;">
  <div class="card shadow p-4" style="width:100%; max-width:460px;">
    <div class="text-center mb-4">
      <div class="mx-auto rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
        <i class="bi bi-person-plus text-white" style="font-size:34px;"></i>
      </div>
      <h3 class="mt-3 mb-1 fw-semibold text-secondary">Registro administrativo inicial</h3>
      <p class="text-muted mb-0">Nenhuma conta administrativa foi encontrada. Crie a primeira conta para começar.</p>
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

    <form action="/src/auth/register.php" method="post">
      <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars((string) ($_POST['nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha</label>
        <input type="password" class="form-control" name="senha" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmar senha</label>
        <input type="password" class="form-control" name="confirmar_senha" required>
      </div>
      <button type="submit" class="btn btn-primary w-100 text-white">
        <i class="bi bi-check2-circle"></i> Criar conta administrativa
      </button>
    </form>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
