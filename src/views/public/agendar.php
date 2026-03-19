<?php
require_once __DIR__ . '/../../../config/database.php';

$message = null;
$error = null;

try {
    $db = (new DatabaseConnection())->conectar();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nomeCliente = trim((string) ($_POST['nome_cliente'] ?? ''));
        $contato = trim((string) ($_POST['contato'] ?? ''));
        $servico = trim((string) ($_POST['servico'] ?? ''));
        $dataAgendamento = (string) ($_POST['data_agendamento'] ?? '');
        $horaAgendamento = (string) ($_POST['hora_agendamento'] ?? '');
        $observacoes = trim((string) ($_POST['observacoes'] ?? ''));

        if ($nomeCliente === '' || $contato === '' || $servico === '' || $dataAgendamento === '' || $horaAgendamento === '') {
            $error = 'Preencha todos os campos obrigatórios.';
        } else {
            $status = 'pendente';
            $usuarioId = null;

            $stmt = $db->prepare(
                'INSERT INTO agendamentos (usuario_id, nome_cliente, contato, servico, data_agendamento, hora_agendamento, observacoes, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            if (!$stmt) {
                throw new RuntimeException('Falha ao preparar agendamento.');
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
            $message = 'Agendamento enviado com sucesso! Em breve entraremos em contacto.';
            $_POST = [];
        }
    }
} catch (Throwable $exception) {
    $error = 'Não foi possível concluir o agendamento: ' . $exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fazer Agendamento</title>
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/css/app.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<div class="container py-4 py-md-5">
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 p-md-5 d-flex flex-wrap justify-content-between align-items-start gap-3">
      <div>
        <span class="badge text-bg-primary mb-2">Agendamento online</span>
        <h1 class="h3 mb-2 text-primary">Fazer agendamento</h1>
        <p class="text-muted mb-0">Preencha o formulário e aguarde o retorno da administração.</p>
      </div>
      <a class="btn btn-outline-secondary btn-sm" href="/src/views/public/index.php">
        <i class="bi bi-arrow-left"></i> Ver comunicados
      </a>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
      <h2 class="h5 mb-2">Reserve sua vaga</h2>
      <p class="text-muted mb-4">Preencha os dados abaixo para concluir o agendamento.</p>

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

      <form method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome completo</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input class="form-control" name="nome_cliente" value="<?= htmlspecialchars((string) ($_POST['nome_cliente'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Contacto (telefone/WhatsApp)</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
            <input class="form-control" name="contato" value="<?= htmlspecialchars((string) ($_POST['contato'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Serviço/Curso</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-journal-text"></i></span>
            <input class="form-control" name="servico" value="<?= htmlspecialchars((string) ($_POST['servico'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
          </div>
        </div>

        <div class="col-md-3">
          <label class="form-label">Data</label>
          <input type="date" class="form-control" name="data_agendamento" value="<?= htmlspecialchars((string) ($_POST['data_agendamento'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Hora</label>
          <input type="time" class="form-control" name="hora_agendamento" value="<?= htmlspecialchars((string) ($_POST['hora_agendamento'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="col-12">
          <label class="form-label">Observações (opcional)</label>
          <textarea class="form-control" name="observacoes" rows="3" placeholder="Ex.: dúvida sobre documentação"><?= htmlspecialchars((string) ($_POST['observacoes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="col-12 d-grid d-md-flex justify-content-md-end">
          <button class="btn btn-primary px-4" type="submit">
            <i class="bi bi-calendar-check"></i> Confirmar agendamento
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
