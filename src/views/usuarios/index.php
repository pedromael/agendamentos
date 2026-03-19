<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../../config/database.php';

requireAuth();

$message = $_SESSION['admin_create_success'] ?? null;
unset($_SESSION['admin_create_success']);

$error = $_SESSION['admin_create_error'] ?? null;
unset($_SESSION['admin_create_error']);

$admins = [];
$loadError = null;

try {
    $db = (new DatabaseConnection())->conectar();
    $result = $db->query('SELECT id, nome, email, created_at FROM usuarios ORDER BY created_at DESC');

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
    }
} catch (Throwable $exception) {
    $loadError = $exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administradores - Agendamento</title>
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Administradores</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-primary btn-sm" href="/src/views/auth/change_password.php">Trocar senha</a>
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

  <?php if ($loadError): ?>
    <div class="alert alert-warning" role="alert">
      Não foi possível carregar os administradores: <?= htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h2 class="h6 mb-3">Cadastrar novo administrador</h2>
      <form method="post" action="/src/auth/create_admin.php" class="row g-2">
        <div class="col-md-4">
          <input type="text" class="form-control" name="nome" placeholder="Nome" required>
        </div>
        <div class="col-md-4">
          <input type="email" class="form-control" name="email" placeholder="Email" required>
        </div>
        <div class="col-md-2">
          <input type="password" class="form-control" name="senha" placeholder="Senha" required>
        </div>
        <div class="col-md-2">
          <input type="password" class="form-control" name="confirmar_senha" placeholder="Confirmar senha" required>
        </div>
        <div class="col-12 d-grid d-md-flex justify-content-md-end">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-person-plus"></i> Cadastrar administrador
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <h2 class="h6 mb-3">Contas administrativas</h2>

      <?php if (!$admins): ?>
        <p class="text-muted mb-0">Nenhum administrador cadastrado.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Criado em</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($admins as $admin): ?>
                <tr>
                  <td><?= htmlspecialchars((string) $admin['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string) $admin['email'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string) $admin['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
