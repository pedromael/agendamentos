<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../../config/database.php';

requireAuth();

$totalAgendamentos = 0;
$pendentes = 0;
$confirmados = 0;
$cancelados = 0;
$evolutionLabels30 = [];
$evolutionData30 = [];
$evolutionLabels7 = [];
$evolutionData7 = [];
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

        $evolutionMap = [];
        $resEvolution = $db->query(
          "SELECT DATE(data_agendamento) AS dia, COUNT(*) AS total
           FROM agendamentos
           WHERE data_agendamento >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
           GROUP BY DATE(data_agendamento)
           ORDER BY dia ASC"
        );

        if ($resEvolution) {
          while ($row = $resEvolution->fetch_assoc()) {
            $day = (string) ($row['dia'] ?? '');
            $evolutionMap[$day] = (int) ($row['total'] ?? 0);
          }
        }

        $startDate = new DateTimeImmutable('today -29 days');
        for ($i = 0; $i < 30; $i++) {
          $currentDate = $startDate->modify('+' . $i . ' days');
          $dateKey = $currentDate->format('Y-m-d');

          $evolutionLabels30[] = $currentDate->format('d/m');
          $evolutionData30[] = $evolutionMap[$dateKey] ?? 0;
        }

        $evolutionLabels7 = array_slice($evolutionLabels30, -7);
        $evolutionData7 = array_slice($evolutionData30, -7);
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
  <link href="../../../public/css/app.css" rel="stylesheet">
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

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap gap-2">
      <a class="btn btn-primary" href="/src/views/agendamentos/index.php">
        <i class="bi bi-calendar-check"></i> Gerir Agendamentos
      </a>
      <a class="btn btn-outline-primary" href="/src/views/publicacoes/index.php">
        <i class="bi bi-megaphone"></i> Gerir Publicações
      </a>
      <a class="btn btn-outline-primary" href="/src/views/usuarios/index.php">
        <i class="bi bi-people"></i> Gerir Administradores
      </a>
    </div>
  </div>

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

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h2 class="h6 mb-3">Distribuição de agendamentos por status</h2>
      <div style="height: 320px;">
        <canvas id="agendamentosStatusChart"></canvas>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 mb-0">Evolução de agendamentos por dia</h2>
        <div class="btn-group" role="group" aria-label="Intervalo do gráfico de evolução">
          <button type="button" class="btn btn-sm btn-outline-primary active" id="evolutionRange7">7 dias</button>
          <button type="button" class="btn btn-sm btn-outline-primary" id="evolutionRange30">30 dias</button>
        </div>
      </div>
      <div style="height: 320px;">
        <canvas id="agendamentosEvolucaoChart"></canvas>
      </div>
    </div>
  </div>

</div>
<script src="../../../public/js/bootstrap.bundle.min.js"></script>
<script src="../../../public/js/chart.js"></script>
<script>
  const statusChartElement = document.getElementById('agendamentosStatusChart');
  const evolutionChartElement = document.getElementById('agendamentosEvolucaoChart');

  if (statusChartElement) {
    const css = getComputedStyle(document.documentElement);
    const warningColor = css.getPropertyValue('--bs-warning').trim() || '#ffc107';
    const successColor = css.getPropertyValue('--bs-success').trim() || '#198754';
    const dangerColor = css.getPropertyValue('--bs-danger').trim() || '#dc3545';

    const statusChartData = {
      labels: ['Pendente', 'Confirmado', 'Cancelado'],
      datasets: [{
        label: 'Agendamentos',
        data: [
          <?= json_encode($pendentes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
          <?= json_encode($confirmados, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
          <?= json_encode($cancelados, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
        ],
        backgroundColor: [warningColor, successColor, dangerColor],
        borderWidth: 1
      }]
    };

    new Chart(statusChartElement, {
      type: 'bar',
      data: statusChartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  }

  if (evolutionChartElement) {
    const css = getComputedStyle(document.documentElement);
    const primaryColor = css.getPropertyValue('--bs-primary').trim() || '#0d6efd';
    const primarySubtleColor = css.getPropertyValue('--bs-primary-bg-subtle').trim() || 'rgba(13, 110, 253, 0.2)';

    const evolutionSeries = {
      '7': {
        labels: <?= json_encode($evolutionLabels7, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
        data: <?= json_encode($evolutionData7, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
      },
      '30': {
        labels: <?= json_encode($evolutionLabels30, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
        data: <?= json_encode($evolutionData30, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
      }
    };

    const evolutionChart = new Chart(evolutionChartElement, {
      type: 'line',
      data: {
        labels: evolutionSeries['7'].labels,
        datasets: [{
          label: 'Agendamentos por dia',
          data: evolutionSeries['7'].data,
          borderColor: primaryColor,
          backgroundColor: primarySubtleColor,
          fill: true,
          tension: 0.25,
          borderWidth: 2,
          pointRadius: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    const range7Button = document.getElementById('evolutionRange7');
    const range30Button = document.getElementById('evolutionRange30');

    const setActiveRange = (range) => {
      const isSevenDays = range === '7';

      evolutionChart.data.labels = evolutionSeries[range].labels;
      evolutionChart.data.datasets[0].data = evolutionSeries[range].data;
      evolutionChart.update();

      if (range7Button && range30Button) {
        range7Button.classList.toggle('active', isSevenDays);
        range30Button.classList.toggle('active', !isSevenDays);
      }
    };

    if (range7Button) {
      range7Button.addEventListener('click', () => setActiveRange('7'));
    }

    if (range30Button) {
      range30Button.addEventListener('click', () => setActiveRange('30'));
    }
  }
</script>
</body>
</html>
