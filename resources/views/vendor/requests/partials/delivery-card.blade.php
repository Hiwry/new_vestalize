{{-- Partial: Delivery Request Card (Vendor View) --}}
@php
    $badgeClass = match($req->status) {
        'pendente'  => 'mr-badge-pending',
        'aprovado'  => 'mr-badge-approved',
        'rejeitado' => 'mr-badge-rejected',
        default     => 'mr-badge-pending',
    };
    $badgeLabel = match($req->status) {
        'pendente'  => 'Aguardando',
        'aprovado'  => 'Aprovado',
        'rejeitado' => 'Recusado',
        default     => $req->status,
    };
    $badgeIcon = match($req->status) {
        'pendente'  => 'fa-clock',
        'aprovado'  => 'fa-circle-check',
        'rejeitado' => 'fa-ban',
        default     => 'fa-circle',
    };

    $daysDiff = \Carbon\Carbon::parse($req->requested_delivery_date)
        ->diffInDays(\Carbon\Carbon::parse($req->current_delivery_date), false);
@endphp

<div class="mr-card">
    <div class="mr-card-head">
        <div>
            <div class="mr-order-id">
                @if($req->order)
                    <a href="{{ route('orders.show', $req->order->id) }}" class="mr-order-link">
                        #{{ str_pad($req->order->id, 6, '0', STR_PAD_LEFT) }}
                    </a>
                @else
                    <span style="color:var(--mr-sub);">Pedido removido</span>
                @endif
            </div>
            @if($req->order && $req->order->client)
                <div class="mr-client-name">{{ $req->order->client->name }}</div>
            @endif
        </div>
        <span class="mr-badge {{ $badgeClass }}">
            <i class="fa-solid {{ $badgeIcon }}"></i> {{ $badgeLabel }}
        </span>
    </div>

    <div class="mr-meta">
        <div class="mr-metric">
            <span class="mr-metric-label">Data atual</span>
            <span class="mr-metric-value">{{ $req->current_delivery_date->format('d/m/Y') }}</span>
        </div>
        <div class="mr-metric">
            <span class="mr-metric-label">Data solicitada</span>
            <span class="mr-metric-value {{ $daysDiff < 0 ? 'mr-metric-warn' : '' }}">
                {{ $req->requested_delivery_date->format('d/m/Y') }}
            </span>
        </div>
        @if($daysDiff < 0)
        <div class="mr-metric">
            <span class="mr-metric-label">Antecipação</span>
            <span class="mr-metric-value mr-metric-warn">{{ abs($daysDiff) }} dia(s)</span>
        </div>
        @endif
        <div class="mr-metric">
            <span class="mr-metric-label">Solicitado em</span>
            <span class="mr-metric-value">{{ $req->created_at->format('d/m/Y H:i') }}</span>
        </div>
        @if($req->reviewed_at)
        <div class="mr-metric">
            <span class="mr-metric-label">Revisado em</span>
            <span class="mr-metric-value">{{ $req->reviewed_at->format('d/m/Y H:i') }}</span>
        </div>
        @endif
        @if($req->reviewed_by_name)
        <div class="mr-metric">
            <span class="mr-metric-label">Revisado por</span>
            <span class="mr-metric-value">{{ $req->reviewed_by_name }}</span>
        </div>
        @endif
    </div>

    @if($req->reason)
    <div class="mr-note">
        <div class="mr-note-label">Motivo da solicitação</div>
        <div class="mr-note-text">{{ $req->reason }}</div>
    </div>
    @endif

    @if($req->review_notes)
    <div class="mr-note mr-note-admin">
        <div class="mr-note-label">Observação da produção</div>
        <div class="mr-note-text">{{ $req->review_notes }}</div>
    </div>
    @endif
</div>
