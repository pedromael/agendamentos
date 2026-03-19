<?php
require_once __DIR__ . '/../../../config/database.php';

$postsError = null;
$publicacoes = [];

try {
    $db = (new DatabaseConnection())->conectar();
    $resultPosts = $db->query(
        'SELECT titulo, conteudo, imagem_url, autor_nome, created_at
         FROM publicacoes
         ORDER BY created_at DESC
         LIMIT 10'
    );

    if ($resultPosts) {
        while ($row = $resultPosts->fetch_assoc()) {
            $publicacoes[] = $row;
        }
    }
} catch (Throwable $exception) {
    $postsError = 'Publicações indisponíveis no momento.';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Comunicados - Agendamento</title>
  <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/icon/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4 py-md-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0 text-primary">Comunicados da administração</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-primary btn-sm" href="/src/views/public/agendar.php">
        <i class="bi bi-calendar-check"></i> Fazer agendamento
      </a>
      <a class="btn btn-outline-secondary btn-sm" href="/src/views/auth/login.php">
        <i class="bi bi-person-lock"></i> Área administrativa
      </a>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <?php if ($postsError): ?>
        <div class="alert alert-warning mb-0" role="alert">
          <?= htmlspecialchars($postsError, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php elseif (!$publicacoes): ?>
        <p class="text-muted mb-0">Ainda não há publicações.</p>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($publicacoes as $post): ?>
            <div class="col-12">
              <div class="border rounded p-3">
                <h2 class="h6 mb-1"><?= htmlspecialchars((string) $post['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
                <small class="text-muted d-block mb-2">
                  Por <?= htmlspecialchars((string) $post['autor_nome'], ENT_QUOTES, 'UTF-8') ?> em
                  <?= htmlspecialchars((string) $post['created_at'], ENT_QUOTES, 'UTF-8') ?>
                </small>

                <?php if (!empty($post['imagem_url'])): ?>
                  <img
                    src="<?= htmlspecialchars((string) $post['imagem_url'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="Imagem da publicação"
                    class="img-fluid rounded mb-3"
                    style="max-height: 260px; object-fit: cover; width: 100%;"
                  >
                <?php endif; ?>

                <p class="mb-0"><?= nl2br(htmlspecialchars((string) $post['conteudo'], ENT_QUOTES, 'UTF-8')) ?></p>
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
