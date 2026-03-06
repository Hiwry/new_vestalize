@php
    $statusMeta = match($editRequest->status) {
        'completed' => ['label' => 'Implementada', 'tone' => 'success'],
        'approved' => ['label' => 'Aprovada', 'tone' => 'success'],
        'rejected' => ['label' => 'Rejeitada', 'tone' => 'danger'],
        default => ['label' => 'Pendente', 'tone' => 'warning'],
    };

    $formatValue = function ($value, $key = null) {
        if (is_null($value) || $value === '') {
            return '<span class="pcm-empty">(vazio)</span>';
        }

        if (is_numeric($value) && in_array($key, ['subtotal', 'discount', 'delivery_fee', 'total', 'unit_price', 'total_price'], true)) {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        }

        if (in_array($key, ['order_date', 'delivery_date', 'entry_date'], true) && $value) {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        }

        return e((string) $value);
    };

    $renderValue = function ($value, $key, $tone = 'before') use ($formatValue) {
        if ($key === 'sizes' && is_array($value)) {
            $items = collect($value)
                ->filter(fn ($qty) => (int) $qty > 0)
                ->map(fn ($qty, $size) => '<div class="pcm-chip-row"><span>' . e($size) . '</span><strong>' . e($qty) . ' un</strong></div>')
                ->implode('');

            return $items !== '' ? '<div class="pcm-stack">' . $items . '</div>' : '<span class="pcm-empty">(vazio)</span>';
        }

        if ($key === 'sublimations' && is_array($value)) {
            $cards = collect($value)->map(function ($sub, $index) use ($tone) {
                $rows = [
                    'Tipo' => $sub['application_type'] ?? '-',
                    'Local' => $sub['location_name'] ?? '-',
                    'Tamanho' => $sub['size_name'] ?? '-',
                    'Quantidade' => $sub['quantity'] ?? '-',
                    'Cores' => $sub['color_details'] ?? (($sub['color_count'] ?? null) ? $sub['color_count'] . ' cores' : '-'),
                ];

                if (!empty($sub['seller_notes'])) {
                    $rows['Obs.'] = $sub['seller_notes'];
                }

                $body = collect($rows)->map(
                    fn ($lineValue, $label) => '<div class="pcm-kv"><span>' . e($label) . '</span><strong>' . e((string) $lineValue) . '</strong></div>'
                )->implode('');

                return '<div class="pcm-mini-card pcm-mini-card-' . e($tone) . '">'
                    . '<div class="pcm-mini-title">Aplicação ' . ($index + 1) . '</div>'
                    . $body
                    . '</div>';
            })->implode('');

            return $cards !== '' ? '<div class="pcm-stack">' . $cards . '</div>' : '<span class="pcm-empty">(vazio)</span>';
        }

        if ($key === 'files' && is_array($value)) {
            $files = collect($value)->map(
                fn ($file) => '<div class="pcm-file">' . e($file['file_name'] ?? 'Arquivo') . '</div>'
            )->implode('');

            return $files !== '' ? '<div class="pcm-stack">' . $files . '</div>' : '<span class="pcm-empty">(vazio)</span>';
        }

        if (is_array($value)) {
            return '<pre class="pcm-json">' . e(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
        }

        return $formatValue($value, $key);
    };

    $renderChange = function ($change, $key) use ($renderValue) {
        return '
            <div class="pcm-change">
                <div class="pcm-change-label">' . e($change['field']) . '</div>
                <div class="pcm-compare">
                    <div class="pcm-side pcm-side-before">
                        <div class="pcm-side-head">Antes</div>
                        <div class="pcm-side-body">' . $renderValue($change['old'], $key, 'before') . '</div>
                    </div>
                    <div class="pcm-side pcm-side-after">
                        <div class="pcm-side-head">Depois</div>
                        <div class="pcm-side-body">' . $renderValue($change['new'], $key, 'after') . '</div>
                    </div>
                </div>
            </div>
        ';
    };
@endphp

<style>
    .pcm {
        color: #0f172a;
    }
    .dark .pcm {
        color: #e2e8f0;
    }
    .pcm-empty {
        font-style: italic;
        opacity: .7;
    }
    .pcm-top {
        display: grid;
        gap: 16px;
        margin-bottom: 18px;
    }
    .pcm-summary {
        background: linear-gradient(135deg, #f8fafc, #eef2ff);
        border: 1px solid #dbe4ff;
        border-radius: 26px;
        padding: 20px;
    }
    .dark .pcm-summary {
        background: linear-gradient(135deg, rgba(15, 23, 42, .95), rgba(30, 41, 59, .92));
        border-color: rgba(148, 163, 184, .28);
    }
    .pcm-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }
    .pcm-status-success { background: rgba(16, 185, 129, .14); color: #047857; }
    .pcm-status-danger { background: rgba(239, 68, 68, .14); color: #b91c1c; }
    .pcm-status-warning { background: rgba(245, 158, 11, .16); color: #b45309; }
    .dark .pcm-status-success { color: #6ee7b7; }
    .dark .pcm-status-danger { color: #fca5a5; }
    .dark .pcm-status-warning { color: #fcd34d; }
    .pcm-title {
        font-size: 24px;
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -.03em;
        margin: 0 0 6px;
    }
    .pcm-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        max-width: 760px;
    }
    .dark .pcm-subtitle {
        color: #94a3b8;
    }
    .pcm-meta {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        margin-top: 16px;
    }
    .pcm-meta-card {
        background: rgba(255, 255, 255, .8);
        border: 1px solid rgba(148, 163, 184, .18);
        border-radius: 18px;
        padding: 14px 16px;
    }
    .dark .pcm-meta-card {
        background: rgba(15, 23, 42, .45);
        border-color: rgba(148, 163, 184, .2);
    }
    .pcm-meta-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 6px;
    }
    .pcm-meta-value {
        font-size: 14px;
        font-weight: 700;
    }
    .pcm-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: 1.2fr .8fr;
        margin-bottom: 18px;
        align-items: start;
    }
    .pcm-grid > :only-child {
        grid-column: 1 / -1;
    }
    .pcm-note {
        border-radius: 24px;
        padding: 20px;
        border: 1px solid #dbe4ff;
        background: linear-gradient(135deg, #eef2ff, #f8fafc);
    }
    .pcm-note-muted {
        border-color: #e2e8f0;
        background: #f8fafc;
    }
    .dark .pcm-note {
        border-color: rgba(99, 102, 241, .28);
        background: linear-gradient(135deg, rgba(49, 46, 129, .28), rgba(15, 23, 42, .9));
    }
    .dark .pcm-note-muted {
        border-color: rgba(148, 163, 184, .2);
        background: rgba(15, 23, 42, .85);
    }
    .pcm-note-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #6366f1;
        margin-bottom: 8px;
    }
    .pcm-note-muted .pcm-note-label {
        color: #94a3b8;
    }
    .pcm-note-text {
        font-size: 15px;
        line-height: 1.75;
        font-weight: 600;
    }
    .pcm-section {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 30px;
        padding: 18px;
        margin-bottom: 18px;
    }
    .dark .pcm-section {
        border-color: rgba(148, 163, 184, .2);
        background: rgba(15, 23, 42, .72);
    }
    .pcm-section-head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }
    .pcm-section-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
        font-weight: 900;
        flex-shrink: 0;
    }
    .pcm-section-icon-order { background: linear-gradient(135deg, #2563eb, #60a5fa); }
    .pcm-section-icon-client { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
    .pcm-section-icon-items { background: linear-gradient(135deg, #db2777, #f472b6); }
    .pcm-section-title {
        font-size: 17px;
        font-weight: 800;
        margin: 0;
    }
    .pcm-section-subtitle {
        margin: 2px 0 0;
        font-size: 13px;
        color: #94a3b8;
        font-weight: 600;
    }
    .pcm-change-list {
        display: grid;
        gap: 14px;
    }
    .pcm-change {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 16px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
    }
    .dark .pcm-change {
        background: rgba(15, 23, 42, .82);
        border-color: rgba(148, 163, 184, .18);
        box-shadow: none;
    }
    .pcm-change-label {
        margin-bottom: 12px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .pcm-compare {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .pcm-side {
        border-radius: 20px;
        padding: 14px;
        border: 1px solid;
        min-height: 96px;
    }
    .pcm-side-before {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #9f1239;
    }
    .pcm-side-after {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }
    .dark .pcm-side-before {
        background: rgba(127, 29, 29, .22);
        border-color: rgba(252, 165, 165, .28);
        color: #fecaca;
    }
    .dark .pcm-side-after {
        background: rgba(6, 95, 70, .22);
        border-color: rgba(110, 231, 183, .28);
        color: #a7f3d0;
    }
    .pcm-side-head {
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 10px;
        opacity: .72;
    }
    .pcm-side-body {
        font-size: 15px;
        line-height: 1.7;
        font-weight: 700;
        word-break: break-word;
    }
    .pcm-stack {
        display: grid;
        gap: 8px;
    }
    .pcm-chip-row,
    .pcm-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 14px;
        background: rgba(255, 255, 255, .55);
        border: 1px solid rgba(148, 163, 184, .16);
    }
    .dark .pcm-chip-row,
    .dark .pcm-file {
        background: rgba(15, 23, 42, .3);
        border-color: rgba(148, 163, 184, .16);
    }
    .pcm-mini-card {
        border-radius: 16px;
        padding: 12px;
        border: 1px solid;
        background: rgba(255, 255, 255, .55);
    }
    .pcm-mini-card-before {
        border-color: #fda4af;
    }
    .pcm-mini-card-after {
        border-color: #6ee7b7;
    }
    .dark .pcm-mini-card {
        background: rgba(15, 23, 42, .35);
    }
    .pcm-mini-title {
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 10px;
    }
    .pcm-kv {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        font-size: 12px;
        line-height: 1.5;
        margin-bottom: 4px;
    }
    .pcm-kv span {
        opacity: .72;
    }
    .pcm-json {
        margin: 0;
        overflow: auto;
        max-height: 360px;
        border-radius: 18px;
        background: #020617;
        color: #e2e8f0;
        padding: 14px;
        font-size: 12px;
        line-height: 1.65;
    }
    .pcm-item-card {
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        background: #fff;
        padding: 16px;
        margin-top: 14px;
    }
    .dark .pcm-item-card {
        border-color: rgba(148, 163, 184, .18);
        background: rgba(15, 23, 42, .82);
    }
    .pcm-item-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .pcm-item-title {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 12px;
    }
    .pcm-alert {
        border-radius: 26px;
        padding: 18px 20px;
        border: 1px solid;
        margin-bottom: 18px;
    }
    .pcm-alert-success {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }
    .pcm-alert-warning {
        background: #fffbeb;
        border-color: #fcd34d;
        color: #92400e;
    }
    .dark .pcm-alert-success {
        background: rgba(6, 95, 70, .22);
        border-color: rgba(110, 231, 183, .28);
        color: #a7f3d0;
    }
    .dark .pcm-alert-warning {
        background: rgba(120, 53, 15, .22);
        border-color: rgba(252, 211, 77, .28);
        color: #fde68a;
    }
    .pcm-alert-title {
        font-size: 17px;
        font-weight: 800;
        margin-bottom: 6px;
    }
    .pcm-alert-text {
        font-size: 14px;
        line-height: 1.7;
        font-weight: 600;
    }
    @media (max-width: 980px) {
        .pcm-grid,
        .pcm-compare {
            grid-template-columns: 1fr;
        }
        .pcm-title {
            font-size: 21px;
        }
    }
</style>

<div class="pcm">
    <div class="pcm-top">
        <section class="pcm-summary">
            <div class="pcm-status pcm-status-{{ $statusMeta['tone'] }}">{{ $statusMeta['label'] }}</div>
            <h3 class="pcm-title">Resumo da solicitação</h3>
            <p class="pcm-subtitle">Pedido #{{ str_pad($editRequest->order_id, 6, '0', STR_PAD_LEFT) }} com contexto da solicitação e comparativo visual do que foi alterado.</p>

            <div class="pcm-meta">
                <div class="pcm-meta-card">
                    <div class="pcm-meta-label">Solicitado por</div>
                    <div class="pcm-meta-value">{{ $editRequest->user->name }}</div>
                </div>
                <div class="pcm-meta-card">
                    <div class="pcm-meta-label">Criado em</div>
                    <div class="pcm-meta-value">{{ $editRequest->created_at->format('d/m/Y H:i') }}</div>
                </div>
                @if($editRequest->approvedBy)
                    <div class="pcm-meta-card">
                        <div class="pcm-meta-label">{{ $editRequest->status === 'rejected' ? 'Rejeitado por' : 'Revisado por' }}</div>
                        <div class="pcm-meta-value">{{ $editRequest->approvedBy->name }}</div>
                    </div>
                @endif
                @if($editRequest->approved_at)
                    <div class="pcm-meta-card">
                        <div class="pcm-meta-label">Última ação</div>
                        <div class="pcm-meta-value">{{ $editRequest->approved_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <div class="pcm-grid">
        <section class="pcm-note">
            <div class="pcm-note-label">Motivo da solicitação</div>
            <div class="pcm-note-text">{{ $editRequest->reason }}</div>
        </section>

        @if($editRequest->admin_notes)
            <section class="pcm-note pcm-note-muted">
                <div class="pcm-note-label">Observações da análise</div>
                <div class="pcm-note-text">{{ $editRequest->admin_notes }}</div>
            </section>
        @endif
    </div>

    @if($editRequest->status === 'completed' && isset($differences) && !empty($differences))
        @if(isset($differences['order']))
            <section class="pcm-section">
                <div class="pcm-section-head">
                    <div class="pcm-section-icon pcm-section-icon-order">R</div>
                    <div>
                        <h4 class="pcm-section-title">Dados do pedido</h4>
                        <p class="pcm-section-subtitle">Campos financeiros e datas impactados pela alteração.</p>
                    </div>
                </div>
                <div class="pcm-change-list">
                    @foreach($differences['order'] as $key => $change)
                        {!! $renderChange($change, $key) !!}
                    @endforeach
                </div>
            </section>
        @endif

        @if(isset($differences['client']))
            <section class="pcm-section">
                <div class="pcm-section-head">
                    <div class="pcm-section-icon pcm-section-icon-client">C</div>
                    <div>
                        <h4 class="pcm-section-title">Dados do cliente</h4>
                        <p class="pcm-section-subtitle">Informações cadastrais alteradas durante a edição.</p>
                    </div>
                </div>
                <div class="pcm-change-list">
                    @foreach($differences['client'] as $key => $change)
                        {!! $renderChange($change, $key) !!}
                    @endforeach
                </div>
            </section>
        @endif

        @if(isset($differences['items']))
            <section class="pcm-section">
                <div class="pcm-section-head">
                    <div class="pcm-section-icon pcm-section-icon-items">I</div>
                    <div>
                        <h4 class="pcm-section-title">Itens do pedido</h4>
                        <p class="pcm-section-subtitle">Comparativo detalhado por item, tamanhos, aplicações e arquivos.</p>
                    </div>
                </div>

                @foreach($differences['items'] as $itemId => $itemChanges)
                    <div class="pcm-item-card">
                        <div class="pcm-item-label">Item do pedido</div>
                        <div class="pcm-item-title">Item #{{ $itemId }}</div>
                        <div class="pcm-change-list">
                            @foreach($itemChanges as $key => $change)
                                {!! $renderChange($change, $key) !!}
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>
        @endif
    @elseif($editRequest->status === 'approved' && $editRequest->order_snapshot_before)
        <section class="pcm-alert pcm-alert-success">
            <div class="pcm-alert-title">Edição aprovada</div>
            <div class="pcm-alert-text">A solicitação foi aprovada e agora aguarda implementação pelo usuário. O comparativo completo aparecerá após essa etapa.</div>
        </section>

        @if($editRequest->changes && count($editRequest->changes) > 0)
            <section class="pcm-section">
                <div class="pcm-section-head">
                    <div class="pcm-section-icon pcm-section-icon-order">J</div>
                    <div>
                        <h4 class="pcm-section-title">Mudanças solicitadas</h4>
                        <p class="pcm-section-subtitle">Estrutura recebida antes da implementação.</p>
                    </div>
                </div>
                <pre class="pcm-json">{{ json_encode($editRequest->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </section>
        @endif
    @else
        <section class="pcm-alert pcm-alert-warning">
            <div class="pcm-alert-title">Aguardando análise</div>
            <div class="pcm-alert-text">A solicitação ainda está pendente. O comparativo visual completo aparece quando a edição é aprovada e implementada.</div>
        </section>

        @if($editRequest->changes && count($editRequest->changes) > 0)
            <section class="pcm-section">
                <div class="pcm-section-head">
                    <div class="pcm-section-icon pcm-section-icon-order">J</div>
                    <div>
                        <h4 class="pcm-section-title">Mudanças solicitadas</h4>
                        <p class="pcm-section-subtitle">Prévia bruta enviada na requisição.</p>
                    </div>
                </div>
                <pre class="pcm-json">{{ json_encode($editRequest->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </section>
        @endif
    @endif
</div>
