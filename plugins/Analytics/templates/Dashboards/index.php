<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $surveys
 * @var int $surveyId
 * @var array|null $report
 */
$this->assign('title', 'Analytics');
$pp = $report['results']['percent_positive'] ?? [];
$enps = $report['results']['enps'] ?? [];
$gaps = $report['results']['demonstrated_importance'] ?? [];
$index = $report['results']['engagement_index'] ?? [];
$suppressed = !empty($pp['suppressed']) || !empty($enps['suppressed']) || !empty($index['suppressed']);
$titles = $report['questions'] ?? [];
?>
<div class="d-flex justify-content-between page-title">
    <div>
        <h1 class="h3 mb-0">Analytics</h1>
        <?php if ($report): ?>
            <div class="text-muted"><?= h($report['survey_title'] ?? '') ?></div>
        <?php endif; ?>
    </div>
    <form class="d-flex gap-2" method="get">
        <select name="survey_id" class="form-select" onchange="this.form.submit()">
            <?php foreach ($surveys as $id => $title): ?>
                <option value="<?= (int)$id ?>" <?= (int)$id === (int)$surveyId ? 'selected' : '' ?>><?= h($title) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<?php if (!$report): ?>
    <p class="text-muted">Create a survey and collect responses to see scores.</p>
<?php else: ?>
    <p class="text-muted mb-3">
        <?= (int)$report['response_count'] ?> responses · anonymity floor <?= (int)$report['min_responses'] ?>
        <?php if ($surveyId): ?>
            · <a href="/reports/add?survey_id=<?= (int)$surveyId ?>">Create report</a>
        <?php endif; ?>
    </p>
    <?php if ($suppressed): ?>
        <div class="alert alert-warning">
            <?= h($pp['message'] ?? 'Results are hidden until the anonymity floor is met.') ?>
            Collect <?= max(0, (int)$report['min_responses'] - (int)$report['response_count']) ?> more response(s) to unlock scores.
        </div>
    <?php else: ?>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card stat-card p-3 h-100">
                    <div class="text-muted text-uppercase small">Responses</div>
                    <div class="display-6"><?= (int)$report['response_count'] ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3 h-100">
                    <div class="text-muted text-uppercase small">Percent positive</div>
                    <div class="display-6"><?= h((string)($pp['percent_positive'] ?? '—')) ?><?= isset($pp['percent_positive']) ? '%' : '' ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3 h-100">
                    <div class="text-muted text-uppercase small">Engagement index</div>
                    <div class="display-6"><?= h((string)($index['engagement_index'] ?? '—')) ?><?= isset($index['engagement_index']) ? '%' : '' ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3 h-100">
                    <div class="text-muted text-uppercase small">eNPS</div>
                    <div class="display-6"><?= h((string)($enps['enps'] ?? '—')) ?></div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card stat-card p-3 chart-card h-100">
                    <h2 class="h6 text-uppercase text-muted">Percent positive by question</h2>
                    <canvas id="ppChart"></canvas>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card stat-card p-3 chart-card h-100">
                    <h2 class="h6 text-uppercase text-muted">eNPS split</h2>
                    <canvas id="enpsChart"></canvas>
                </div>
            </div>
            <div class="col-12">
                <div class="card stat-card p-3 chart-card">
                    <h2 class="h6 text-uppercase text-muted">Demonstrated vs importance</h2>
                    <canvas id="gapChart"></canvas>
                </div>
            </div>
        </div>
        <?php
        $ppLabels = [];
        $ppValues = [];
        foreach ($pp['by_question'] ?? [] as $qid => $stats) {
            $label = $titles[$qid] ?? ('Q' . $qid);
            $ppLabels[] = mb_strlen($label) > 42 ? mb_substr($label, 0, 41) . '…' : $label;
            $ppValues[] = $stats['percent_positive'] ?? 0;
        }
        $gapLabels = $gapDemo = $gapImp = [];
        foreach ($gaps['competencies'] ?? [] as $name => $row) {
            $gapLabels[] = $name;
            $gapDemo[] = $row['demonstrated'];
            $gapImp[] = $row['importance'];
        }
        $charts = [
            'ppLabels' => $ppLabels,
            'ppValues' => $ppValues,
            'enps' => [
                (int)($enps['promoters'] ?? 0),
                (int)($enps['passives'] ?? 0),
                (int)($enps['detractors'] ?? 0),
            ],
            'gapLabels' => $gapLabels,
            'gapDemo' => $gapDemo,
            'gapImp' => $gapImp,
        ];
        ?>
        <?php $this->append('scriptBottom'); ?>
        <script>
        const data = <?= json_encode($charts, JSON_UNESCAPED_UNICODE) ?>;
        const brand = getComputedStyle(document.documentElement).getPropertyValue('--brand').trim() || '#1f4e79';
        if (document.getElementById('ppChart')) {
            new Chart(document.getElementById('ppChart'), {
                type: 'bar',
                data: { labels: data.ppLabels, datasets: [{ label: '% positive', data: data.ppValues, backgroundColor: brand }] },
                options: { indexAxis: 'y', scales: { x: { min: 0, max: 100 } }, plugins: { legend: { display: false } } }
            });
        }
        if (document.getElementById('enpsChart')) {
            new Chart(document.getElementById('enpsChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Promoters', 'Passives', 'Detractors'],
                    datasets: [{ data: data.enps, backgroundColor: ['#2e7d32', '#f9a825', '#c62828'] }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });
        }
        if (document.getElementById('gapChart')) {
            new Chart(document.getElementById('gapChart'), {
                type: 'bar',
                data: {
                    labels: data.gapLabels,
                    datasets: [
                        { label: 'Demonstrated', data: data.gapDemo, backgroundColor: brand },
                        { label: 'Importance', data: data.gapImp, backgroundColor: '#8aa4b8' }
                    ]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });
        }
        </script>
        <?php $this->end(); ?>
    <?php endif; ?>
<?php endif; ?>
