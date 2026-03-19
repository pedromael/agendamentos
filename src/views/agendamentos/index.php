<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../../config/database.php';

requireAuth();

$allowedStatuses = ['pendente', 'confirmado', 'cancelado'];
$message = null;
$error = null;

try {
    $db = (new DatabaseConnection())->conectar();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'create') {
            $nomeCliente = trim((string) ($_POST['nome_cliente'] ?? ''));
            $contato = trim((string) ($_POST['contato'] ?? ''));
            $servico = trim((string) ($_POST['servico'] ?? ''));
            $dataAgendamento = (string) ($_POST['data_agendamento'] ?? '');
            $horaAgendamento = (string) ($_POST['hora_agendamento'] ?? '');
            $observacoes = trim((string) ($_POST['observacoes'] ?? ''));

            if ($nomeCliente === '' || $contato === '' || $servico === '' || $dataAgendamento === '' || $horaAgendamento === '') {
                throw new RuntimeException('Preencha os campos obrigatórios.');
            }

            $status = 'pendente';
            $usuarioId = (int) $_SESSION['user_id'];

            $stmt = $db->prepare(
                'INSERT INTO agendamentos (usuario_id, nome_cliente, contato, servico, data_agendamento, hora_agendamento, observacoes, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            if (!$stmt) {
                throw new RuntimeException('Falha ao preparar cadastro.');
            }

            $stmt->bind_param(
                'isssssss',
                $usuarioId,
                $nomeCliente,
                $contato,
                $servico,
                $dataAgendamento,
                $horaAgendamento,
                $observacoes,
                $status
            );

            $stmt->execute();
            $message = 'Agendamento criado com sucesso.';
        }

        if ($action === 'update_status') {
            $id = (int) ($_POST['id'] ?? 0);
            $status = (string) ($_POST['status'] ?? '');

            if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
                throw new RuntimeException('Dados de atualização inválidos.');
            }

            $stmt = $db->prepare('UPDATE agendamentos SET status = ? WHERE id = ?');
            if (!$stmt) {
                throw new RuntimeException('Falha ao preparar atualização.');
            }

            $stmt->bind_param('si', $status, $id);
            $stmt->execute();

            $message = 'Status atualizado.';
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new RuntimeException('ID inválido para remoção.');
            }

            $stmt = $db->prepare('DELETE FROM agendamentos WHERE id = ?');
            if (!$stmt) {
                throw new RuntimeException('Falha ao preparar remoção.');
            }

            $stmt->bind_param('i', $id);
            $stmt->execute();

            $message = 'Agendamento removido.';
        }
    }

    $rows = [];
    $result = $db->query('SELECT id, nome_cliente, contato, servico, data_agendamento, hora_agendamento, status, created_at FROM agendamentos ORDER BY created_at DESC');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
    $rows = $rows ?? [];
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Agendamentos - Agendamento</title>
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/css/app.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Agendamentos</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-sm" href="/src/views/dashboard/index.php">Dashboard</a>
      <a class="btn btn-outline-danger btn-sm" href="/src/auth/logout.php">Sair</a>
    </div>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h2 class="h6 mb-3">Novo agendamento</h2>
      <form method="post" class="row g-2">
        <input type="hidden" name="action" value="create">
        <div class="col-md-4">
          <input class="form-control" name="nome_cliente" placeholder="Nome do cliente" required>
        </div>
        <div class="col-md-3">
          <input class="form-control" name="contato" placeholder="Contacto" required>
        </div>
        <div class="col-md-3">
          <input class="form-control" name="servico" placeholder="Serviço" required>
        </div>
        <div class="col-md-2">
          <input type="date" class="form-control" name="data_agendamento" required>
        </div>
        <div class="col-md-2">
          <input type="time" class="form-control" name="hora_agendamento" required>
        </div>
        <div class="col-md-8">
          <input class="form-control" name="observacoes" placeholder="Observações (opcional)">
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-primary" type="submit">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Contacto</th>
              <th>Serviço</th>
              <th>Data</th>
              <th>Hora</th>
              <th>Status</th>
              <th class="text-end">Ações</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Nenhum agendamento cadastrado.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $row): ?>
              <tr>
                <td><?= htmlspecialchars((string) $row['nome_cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['contato'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['servico'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['data_agendamento'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['hora_agendamento'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                      <?php foreach ($allowedStatuses as $statusOption): ?>
                        <option value="<?= $statusOption ?>" <?= $row['status'] === $statusOption ? 'selected' : '' ?>>
                          <?= ucfirst($statusOption) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </td>
                <td class="text-end">
                  <form method="post" class="d-inline" onsubmit="return confirm('Remover agendamento?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
