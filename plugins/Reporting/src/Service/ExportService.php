<?php
declare(strict_types=1);

namespace Reporting\Service;

use Analytics\Service\ScoreService;
use Cake\ORM\Locator\LocatorAwareTrait;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpWord\PhpWord;

class ExportService
{
    use LocatorAwareTrait;

    public function __construct(private ?ScoreService $scores = null)
    {
        $this->scores ??= new ScoreService();
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(int $templateId): array
    {
        $template = $this->fetchTable('Reporting.ReportTemplates')->get($templateId, contain: ['ReportPages.ReportElements', 'Surveys']);
        $tenant = $this->fetchTable('AdminKit.Tenants')->get($template->tenant_id);
        $scores = $template->survey_id ? $this->scores->run((int)$template->survey_id) : [];

        return compact('template', 'tenant', 'scores');
    }

    public function html(array $payload): string
    {
        return $this->wrap($payload);
    }

    public function pdf(array $payload): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('dpi', 96);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($this->wrap($payload), 'UTF-8');
        $dompdf->render();

        return (string)$dompdf->output();
    }

    public function docx(array $payload): string
    {
        $phpWord = new PhpWord();
        $hex = $this->hex($payload['tenant']->primary_color);
        $phpWord->addTitleStyle(0, ['size' => 14, 'color' => $hex, 'bold' => true]);
        $phpWord->addTitleStyle(1, ['size' => 22, 'color' => $hex, 'bold' => true]);
        $phpWord->addTitleStyle(2, ['size' => 16, 'color' => '222222', 'bold' => true]);

        $section = $phpWord->addSection();
        $section->addTitle((string)$payload['tenant']->brand_name, 0);
        $section->addTitle((string)$payload['template']->name, 1);
        $section->addText($this->scoreHeadline($payload), ['italic' => true, 'color' => '555555']);
        $section->addTextBreak();

        foreach ($this->blocks($payload) as $block) {
            $section->addTitle($block['title'], 2);
            $section->addText($block['body']);
            if (!empty($block['rows'])) {
                $table = $section->addTable(['borderSize' => 4, 'borderColor' => 'CCCCCC', 'cellMargin' => 80]);
                $table->addRow();
                $table->addCell(4000)->addText('Item', ['bold' => true]);
                $table->addCell(2000)->addText('Score', ['bold' => true]);
                foreach ($block['rows'] as $row) {
                    $table->addRow();
                    $table->addCell(4000)->addText((string)$row[0]);
                    $table->addCell(2000)->addText((string)$row[1]);
                }
            }
            $section->addTextBreak();
        }

        return $this->writeTemp('docx', function (string $path) use ($phpWord): void {
            $phpWord->save($path, 'Word2007');
        });
    }

    public function pptx(array $payload): string
    {
        $ppt = new PhpPresentation();
        $hex = $this->hex($payload['tenant']->primary_color);
        $brand = (string)$payload['tenant']->brand_name;

        $titleSlide = $ppt->getActiveSlide();
        $this->fillSlide($titleSlide, $hex);
        $this->addSlideText($titleSlide, $brand, 40, 80, 22, true, 'FFFFFFFF');
        $this->addSlideText($titleSlide, (string)$payload['template']->name, 40, 160, 32, true, 'FFFFFFFF');
        $this->addSlideText($titleSlide, $this->scoreHeadline($payload), 40, 260, 16, false, 'FFE8EEF4');

        foreach ($this->blocks($payload) as $block) {
            $slide = $ppt->createSlide();
            $bar = $slide->createRichTextShape()->setHeight(70)->setWidth(960)->setOffsetX(0)->setOffsetY(0);
            $bar->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF' . $hex));
            $bar->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $run = $bar->getActiveParagraph()->createTextRun($block['title']);
            $run->getFont()->setBold(true)->setSize(20)->setColor(new Color('FFFFFFFF'));
            $this->addSlideText($slide, $block['body'], 40, 100, 16, false, 'FF222222');
            $offsetY = 200;
            foreach (array_slice($block['rows'] ?? [], 0, 8) as $row) {
                $this->addSlideText($slide, $row[0] . '  —  ' . $row[1], 40, $offsetY, 14, false, 'FF333333');
                $offsetY += 36;
            }
        }

        return $this->writeTemp('pptx', function (string $path) use ($ppt): void {
            $writer = \PhpOffice\PhpPresentation\IOFactory::createWriter($ppt, 'PowerPoint2007');
            $writer->save($path);
        });
    }

    private function wrap(array $payload): string
    {
        $color = '#' . $this->hex($payload['tenant']->primary_color);
        $parts = [];
        $parts[] = '<div class="banner">' . h($payload['tenant']->brand_name) . '</div>';
        $parts[] = '<h1>' . h($payload['template']->name) . '</h1>';
        $parts[] = '<p class="muted">' . h($this->scoreHeadline($payload)) . '</p>';
        foreach ($this->blocks($payload) as $block) {
            $parts[] = '<h2>' . h($block['title']) . '</h2>';
            $parts[] = '<p>' . nl2br(h($block['body'])) . '</p>';
            if (!empty($block['rows'])) {
                $parts[] = '<table><thead><tr><th>Item</th><th>Score</th></tr></thead><tbody>';
                foreach ($block['rows'] as $row) {
                    $parts[] = '<tr><td>' . h((string)$row[0]) . '</td><td>' . h((string)$row[1]) . '</td></tr>';
                }
                $parts[] = '</tbody></table>';
            }
        }

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            @page { margin: 22mm 18mm; }
            body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 12pt; }
            .banner { background: ' . $color . '; color: #fff; padding: 14px 18px; margin: -10px -10px 18px; }
            h1 { font-size: 22pt; margin: 0 0 8px; color: ' . $color . '; }
            h2 { font-size: 14pt; margin: 22px 0 8px; color: ' . $color . '; }
            .muted { color: #555; }
            table { width: 100%; border-collapse: collapse; margin-top: 8px; }
            th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
            th { background: #f3f5f8; }
        </style></head><body>' . implode('', $parts) . '</body></html>';
    }

    /**
     * @return list<array{title:string,body:string,rows?:list<array{0:string,1:string}>}>
     */
    private function blocks(array $payload): array
    {
        $blocks = [];
        foreach ($payload['template']->report_pages as $page) {
            $body = [];
            $rows = [];
            foreach ($page->report_elements as $element) {
                $rendered = $this->elementContent($element, $payload);
                if ($rendered['body'] !== '') {
                    $body[] = $rendered['body'];
                }
                foreach ($rendered['rows'] as $row) {
                    $rows[] = $row;
                }
            }
            $blocks[] = [
                'title' => (string)$page->title,
                'body' => implode("\n\n", $body) ?: 'No commentary on this page.',
                'rows' => $rows,
            ];
        }
        if ($blocks === []) {
            $blocks[] = [
                'title' => 'Results',
                'body' => $this->scoreHeadline($payload),
                'rows' => $this->scoreRows($payload['scores'] ?? []),
            ];
        }

        return $blocks;
    }

    /**
     * @return array{body:string,rows:list<array{0:string,1:string}>}
     */
    private function elementContent(object $element, array $payload): array
    {
        $config = json_decode((string)$element->config_json, true) ?: [];
        if ($element->type === 'static') {
            return ['body' => (string)($config['text'] ?? ''), 'rows' => []];
        }
        if ($element->type === 'score') {
            $calc = (string)($config['calculator'] ?? 'percent_positive');
            $result = $payload['scores']['results'][$calc] ?? [];

            return [
                'body' => $this->describeScore($calc, $result),
                'rows' => $this->rowsFor($calc, $result, $payload['scores']['questions'] ?? []),
            ];
        }

        return ['body' => (string)$element->type, 'rows' => []];
    }

    /**
     * @param array<string, mixed> $result
     */
    private function describeScore(string $calc, array $result): string
    {
        if (!empty($result['suppressed'])) {
            return (string)($result['message'] ?? 'Results are hidden until the anonymity floor is met.');
        }
        return match ($calc) {
            'percent_positive' => sprintf('Overall percent positive is %s%% across %s scored answers.', $result['percent_positive'] ?? '—', $result['n'] ?? 0),
            'enps' => sprintf('eNPS is %s (promoters %s, passives %s, detractors %s).', $result['enps'] ?? '—', $result['promoters'] ?? 0, $result['passives'] ?? 0, $result['detractors'] ?? 0),
            'engagement_index' => sprintf('Engagement index is %s%%.', $result['engagement_index'] ?? '—'),
            'demonstrated_importance' => 'Demonstrated versus importance scores by competency.',
            default => ucfirst(str_replace('_', ' ', $calc)),
        };
    }

    /**
     * @param array<string, mixed> $result
     * @param array<int, string> $titles
     * @return list<array{0:string,1:string}>
     */
    private function rowsFor(string $calc, array $result, array $titles): array
    {
        if (!empty($result['suppressed'])) {
            return [];
        }
        $rows = [];
        if ($calc === 'percent_positive') {
            foreach ($result['by_question'] ?? [] as $qid => $stats) {
                $rows[] = [(string)($titles[$qid] ?? ('Question ' . $qid)), ($stats['percent_positive'] ?? '—') . '%'];
            }
        }
        if ($calc === 'demonstrated_importance') {
            foreach ($result['competencies'] ?? [] as $name => $row) {
                $rows[] = [$name, sprintf('D %s / I %s (gap %s)', $row['demonstrated'] ?? '—', $row['importance'] ?? '—', $row['gap'] ?? '—')];
            }
        }
        if ($calc === 'enps') {
            $rows[] = ['Promoters', (string)($result['promoters'] ?? 0)];
            $rows[] = ['Passives', (string)($result['passives'] ?? 0)];
            $rows[] = ['Detractors', (string)($result['detractors'] ?? 0)];
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $scores
     * @return list<array{0:string,1:string}>
     */
    private function scoreRows(array $scores): array
    {
        $rows = [];
        foreach ($scores['results'] ?? [] as $name => $result) {
            $rows = array_merge($rows, $this->rowsFor($name, $result, $scores['questions'] ?? []));
        }

        return $rows;
    }

    private function scoreHeadline(array $payload): string
    {
        $scores = $payload['scores'] ?? [];
        if ($scores === []) {
            return 'No survey is linked to this template.';
        }
        $count = (int)($scores['response_count'] ?? 0);
        $min = (int)($scores['min_responses'] ?? 0);
        $pp = $scores['results']['percent_positive']['percent_positive'] ?? null;
        if (!empty($scores['results']['percent_positive']['suppressed'])) {
            return sprintf('%d responses (floor %d). Scores are hidden until the anonymity floor is met.', $count, $min);
        }

        return sprintf('%d responses. Percent positive: %s%%.', $count, $pp ?? '—');
    }

    private function hex(?string $color): string
    {
        $hex = ltrim((string)$color, '#');

        return preg_match('/^[0-9a-fA-F]{6}$/', $hex) ? strtoupper($hex) : '1F4E79';
    }

    private function fillSlide(object $slide, string $hex): void
    {
        $shape = $slide->createRichTextShape()->setHeight(540)->setWidth(960)->setOffsetX(0)->setOffsetY(0);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF' . $hex));
    }

    private function addSlideText(object $slide, string $text, int $x, int $y, int $size, bool $bold, string $color): void
    {
        $shape = $slide->createRichTextShape()->setHeight(80)->setWidth(860)->setOffsetX($x)->setOffsetY($y);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $run = $shape->getActiveParagraph()->createTextRun($text);
        $run->getFont()->setBold($bold)->setSize($size)->setColor(new Color($color));
    }

    private function writeTemp(string $ext, callable $save): string
    {
        $path = TMP . 'report_' . uniqid('', true) . '.' . $ext;
        $save($path);
        $bytes = (string)file_get_contents($path);
        @unlink($path);

        return $bytes;
    }
}
