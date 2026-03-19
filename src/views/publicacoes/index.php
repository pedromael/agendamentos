<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../../config/database.php';

requireAuth();

$message = null;
$error = null;

try {
    $db = (new DatabaseConnection())->conectar();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'create') {
            $titulo = trim((string) ($_POST['titulo'] ?? ''));
            $conteudo = trim((string) ($_POST['conteudo'] ?? ''));
            $imagemUrl = trim((string) ($_POST['imagem_url'] ?? ''));

            if ($titulo === '' || $conteudo === '') {
                throw new RuntimeException('Preencha título e conteúdo da publicação.');
            }

            $usuarioId = (int) ($_SESSION['user_id'] ?? 0);
            $autorNome = (string) ($_SESSION['user_name'] ?? 'Administrador');

            $stmt = $db->prepare(
                'INSERT INTO publicacoes (usuario_id, autor_nome, titulo, conteudo, imagem_url)
                 VALUES (?, ?, ?, ?, ?)'
            );

            if (!$stmt) {
                throw new RuntimeException('Falha ao preparar criação de publicação.');
            }

            $stmt->bind_param('issss', $usuarioId, $autorNome, $titulo, $conteudo, $imagemUrl);
            $stmt->execute();

            $message = 'Publicação criada com sucesso.';
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new RuntimeException('ID inválido para remoção.');
            }

            $stmt = $db->prepare('DELETE FROM publicacoes WHERE id = ?');
            if (!$stmt) {
                throw new RuntimeException('Falha ao preparar remoção de publicação.');
            }

            $stmt->bind_param('i', $id);
            $stmt->execute();

            $message = 'Publicação removida.';
        }
    }

    $publicacoes = [];
    $result = $db->query('SELECT id, autor_nome, titulo, conteudo, imagem_url, created_at FROM publicacoes ORDER BY created_at DESC');

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $publicacoes[] = $row;
        }
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
    $publicacoes = $publicacoes ?? [];
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Publicações - Agendamento</title>
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/css/app.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Publicações</h1>
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
      <h2 class="h6 mb-3">Nova publicação</h2>
      <form method="post" class="row g-2">
        <input type="hidden" name="action" value="create">
        <div class="col-md-6">
          <input class="form-control" name="titulo" placeholder="Título" required>
        </div>
        <div class="col-md-6">
          <input class="form-control" name="imagem_url" placeholder="URL da imagem (opcional)">
        </div>
        <div class="col-12">
          <textarea class="form-control" name="conteudo" rows="4" placeholder="Conteúdo da publicação" required></textarea>
        </div>
        <div class="col-12 d-grid d-md-flex justify-content-md-end">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-send"></i> Publicar
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <h2 class="h6 mb-3">Publicações recentes</h2>
      <?php if (!$publicacoes): ?>
        <p class="text-muted mb-0">Nenhuma publicação cadastrada.</p>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($publicacoes as $post): ?>
            <div class="col-12">
              <div class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                  <div>
                    <h3 class="h6 mb-1"><?= htmlspecialchars((string) $post['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <small class="text-muted">
                      Por <?= htmlspecialchars((string) $post['autor_nome'], ENT_QUOTES, 'UTF-8') ?>
                      em <?= htmlspecialchars((string) $post['created_at'], ENT_QUOTES, 'UTF-8') ?>
                    </small>
                  </div>
                  <form method="post" onsubmit="return confirm('Remover publicação?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                  </form>
                </div>

                <?php if (!empty($post['imagem_url'])): ?>
                  <img
                    src="<?= htmlspecialchars((string) $post['imagem_url'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="Imagem da publicação"
                    class="img-fluid rounded mt-3"
                    style="max-height: 240px; object-fit: cover; width: 100%;"
                  >
                <?php endif; ?>

                <p class="mb-0 mt-3"><?= nl2br(htmlspecialchars((string) $post['conteudo'], ENT_QUOTES, 'UTF-8')) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
