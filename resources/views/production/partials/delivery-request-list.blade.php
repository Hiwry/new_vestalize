@php
    $statusMap = [
        'pendente' => [
            'badge_class' => 'pf-badge pf-badge-pending',
            'label' => 'Pendente',
        ],
        'aprovado' => [
            'badge_class' => 'pf-badge pf-badge-approved',
            'label' => 'Aprovada',
        ],
        'rejeitado' => [
            'badge_class' => 'pf-badge pf-badge-rejected',
            'label' => 'Rejeitada',
        ],
    ];

    $statusUi = $statusMap[$status] ?? $statusMap['pendente'];
@endphp

@if($items->isEmpty())
    <div class="pf-empty">
        <div class="pf-empty-icon"><i class="fa-solid fa-inbox"></i></div>
        <h3>Nenhum item nesta fila</h3>
        <p>Assim que novas solicitacoes entrarem neste status, elas aparecerao aqui com o mesmo contexto visual do dashboard.</p>
    </div>
@else
    <div class="pf-request-list">
        @foreach($items as $request)
            @php
                $daysDiff = \Carbon\Carbon::parse($request->requested_delivery_date)
                    ->diffInDays(\Carbon\Carbon::parse($request->current_delivery_date));
            @endphp

            <article class="pf-card pf-request-card">
                <div class="pf-request-head">
                    <div>
                        <div class="pf-request-title">
                            @if($request->order)
                                <a href="{{ route('orders.show', $request->order->id) }}" class="pf-request-link">
                                    Pedido #{{ str_pad($request->order->id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                            @else
                                Pedido removido
                            @endif
                        </div>
                        <div class="pf-panel-subtitle">
                            Cliente {{ $request->order?->client?->name ?? 'Nao identificado' }} | Solicitado por {{ $request->requested_by_name }}
                        </div>
                    </div>

                    <span class="{{ $statusUi['badge_class'] }}">{{ $statusUi['label'] }}</span>
                </div>

                <div class="pf-request-meta">
                    <div class="pf-metric">
                        <span class="pf-metric-label">Data atual</span>
                        <span class="pf-metric-value is-warning">{{ \Carbon\Carbon::parse($request->current_delivery_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="pf-metric">
                        <span class="pf-metric-label">
                            @if($status === 'aprovado')
                                Nova data
                            @else
                                Data solicitada
                            @endif
                        </span>
                        <span class="pf-metric-value is-success">{{ \Carbon\Carbon::parse($request->requested_delivery_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="pf-metric">
                        <span class="pf-metric-label">Antecipacao</span>
                        <span class="pf-metric-value">{{ $daysDiff }} {{ $daysDiff === 1 ? 'dia' : 'dias' }}</span>
                    </div>
                    <div class="pf-metric">
                        <span class="pf-metric-label">
                            @if($status === 'pendente')
                                Criado em
                            @elseif($status === 'aprovado')
                                Aprovado em
                            @else
                                Rejeitado em
                            @endif
                        </span>
                        <span class="pf-metric-value">
                            @if($status === 'pendente')
                                {{ $request->created_at?->format('d/m/Y H:i') }}
                            @else
                                {{ $request->reviewed_at?->format('d/m/Y H:i') ?? '-' }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="pf-note-box">
                    <div class="pf-note-label">Motivo da solicitacao</div>
                    <div class="pf-note-text">{{ $request->reason }}</div>
                </div>

                @if($status !== 'pendente')
                    <div class="pf-note-box">
                        <div class="pf-note-label">
                            @if($status === 'aprovado')
                                Observacoes da aprovacao
                            @else
                                Motivo da rejeicao
                            @endif
                        </div>
                        <div class="pf-note-text">{{ $request->review_notes ?: 'Sem observacoes registradas.' }}</div>
                    </div>
                @endif

                @if($status === 'pendente')
                    <div class="pf-request-actions">
                        <button type="button" onclick="openApproveModal({{ $request->id }})" class="pf-inline-button pf-inline-button-success">
                            <i class="fa-solid fa-check"></i>
                            <span>Aprovar</span>
                        </button>
                        <button type="button" onclick="openRejectModal({{ $request->id }})" class="pf-inline-button pf-inline-button-danger">
                            <i class="fa-solid fa-xmark"></i>
                            <span>Rejeitar</span>
                        </button>
                    </div>
                @endif
            </article>
        @endforeach
    </div>
@endif
