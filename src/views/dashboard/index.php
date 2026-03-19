<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../../config/database.php';

requireAuth();

$totalAgendamentos = 0;
$pendentes = 0;
$confirmados = 0;
$cancelados = 0;
$loadError = null;

try {
    $db = (new DatabaseConnection())->conectar();

    $resTotal = $db->query('SELECT COUNT(*) AS total FROM agendamentos');
    $totalAgendamentos = $resTotal ? (int) ($resTotal->fetch_assoc()['total'] ?? 0) : 0;

    $resStatus = $db->query("SELECT status, COUNT(*) AS total FROM agendamentos GROUP BY status");
    if ($resStatus) {
        while ($row = $resStatus->fetch_assoc()) {
            $status = (string) ($row['status'] ?? '');
            $count = (int) ($row['total'] ?? 0);

            if ($status === 'pendente') {
                $pendentes = $count;
            } elseif ($status === 'confirmado') {
                $confirmados = $count;
            } elseif ($status === 'cancelado') {
                $cancelados = $count;
            }
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
  <title>Dashboard - Agendamento</title>
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Dashboard</h1>
    <div class="d-flex align-items-center gap-3">
      <span class="text-secondary">Olá, <?= htmlspecialchars(currentUserName(), ENT_QUOTES, 'UTF-8') ?></span>
      <a class="btn btn-outline-danger btn-sm" href="/src/auth/logout.php">
        <i class="bi bi-box-arrow-right"></i> Sair
      </a>
    </div>
  </div>

  <?php if ($loadError): ?>
    <div class="alert alert-warning" role="alert">
      Não foi possível carregar os indicadores: <?= htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-secondary small">Total</div>
          <div class="h3 mb-0"><?= $totalAgendamentos ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-secondary small">Pendentes</div>
          <div class="h3 mb-0 text-warning"><?= $pendentes ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-secondary small">Confirmados</div>
          <div class="h3 mb-0 text-success"><?= $confirmados ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-secondary small">Cancelados</div>
          <div class="h3 mb-0 text-danger"><?= $cancelados ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body d-flex flex-wrap gap-2">
      <a class="btn btn-primary" href="/src/views/agendamentos/index.php">
        <i class="bi bi-calendar-check"></i> Gerir Agendamentos
      </a>
      <a class="btn btn-outline-primary" href="/src/views/publicacoes/index.php">
        <i class="bi bi-megaphone"></i> Gerir Publicações
      </a>
    </div>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
