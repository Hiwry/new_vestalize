{{-- Partial: Edit Request Card (Vendor View) --}}
@php
    $badgeClass = match($req->status) {
        'pending'   => 'mr-badge-pending',
        'approved'  => 'mr-badge-approved',
        'completed' => 'mr-badge-completed',
        'rejected'  => 'mr-badge-rejected',
        default     => 'mr-badge-pending',
    };
    $badgeLabel = match($req->status) {
        'pending'   => 'Aguardando',
        'approved'  => 'Aprovado',
        'completed' => 'Concluído',
        'rejected'  => 'Recusado',
        default     => $req->status,
    };
    $badgeIcon = match($req->status) {
        'pending'   => 'fa-clock',
        'approved'  => 'fa-circle-check',
        'completed' => 'fa-flag-checkered',
        'rejected'  => 'fa-ban',
        default     => 'fa-circle',
    };
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
            <span class="mr-metric-label">Solicitado em</span>
            <span class="mr-metric-value">{{ $req->created_at->format('d/m/Y H:i') }}</span>
        </div>
        @if($req->approved_at)
        <div class="mr-metric">
            <span class="mr-metric-label">{{ $req->status === 'rejected' ? 'Recusado em' : 'Aprovado em' }}</span>
            <span class="mr-metric-value">{{ $req->approved_at->format('d/m/Y H:i') }}</span>
        </div>
        @endif
        @if($req->approvedBy)
        <div class="mr-metric">
            <span class="mr-metric-label">Revisado por</span>
            <span class="mr-metric-value">{{ $req->approvedBy->name }}</span>
        </div>
        @endif
    </div>

    @if($req->reason)
    <div class="mr-note">
        <div class="mr-note-label">Motivo da solicitação</div>
        <div class="mr-note-text">{{ $req->reason }}</div>
    </div>
    @endif

    @if($req->admin_notes)
    <div class="mr-note mr-note-admin">
        <div class="mr-note-label">Observação da produção</div>
        <div class="mr-note-text">{{ $req->admin_notes }}</div>
    </div>
    @endif
</div>
