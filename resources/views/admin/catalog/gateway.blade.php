@extends('layouts.admin')

@push('styles')
<style>
    .catalog-gateway-page {
        color: #0f172a;
    }

    .gateway-card {
        background: #ffffff;
        border: 1px solid #dbe4f0;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .gateway-subcard {
        background: #f8fafc;
        border: 1px solid #dbe4f0;
        border-radius: 14px;
    }

    .gateway-label {
        color: #475569;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .gateway-muted {
        color: #64748b;
    }

    .gateway-input {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
        border-radius: 12px;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .gateway-input::placeholder {
        color: #94a3b8;
    }

    .gateway-input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        outline: none;
    }

    .gateway-toggle {
        background: #ffffff;
        border: 1px solid #dbe4f0;
        border-radius: 14px;
    }

    .gateway-primary-btn {
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 18px rgba(91, 33, 182, 0.25);
    }

    .gateway-primary-btn:hover {
        filter: brightness(1.05);
    }

    .gateway-secondary-btn {
        border-radius: 11px;
    }

    .dark .gateway-card {
        background: #111827;
        border-color: #334155;
        box-shadow: none;
    }

    .dark .gateway-subcard {
        background: #0f172a;
        border-color: #334155;
    }

    .dark .gateway-label {
        color: #cbd5e1;
    }

    .dark .gateway-muted {
        color: #94a3b8;
    }

    .dark .gateway-input {
        border-color: #475569;
        background: #1f2937;
        color: #f8fafc;
    }

    .dark .gateway-input::placeholder {
        color: #64748b;
    }

    .dark .gateway-toggle {
        background: #111827;
        border-color: #334155;
    }
</style>
@endpush

@section('content')
<div class="catalog-gateway-page max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.catalog.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                ← Voltar para Gestão do Catálogo
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">Gateway de Pagamento do Catálogo</h1>
            <p class="text-sm gateway-muted mt-1">
                Configure a integração de pagamento e as regras para geração dos pedidos internos.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-300">
            <ul class="list-disc ml-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 gateway-card p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Configurações</h2>

            <form action="{{ route('admin.catalog-gateway.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="provider" class="gateway-label block text-xs uppercase mb-2">
                        Provedor de pagamento
                    </label>
                    <select id="provider" name="provider" class="gateway-input w-full px-3 py-2 text-sm">
                        @foreach($providers as $value => $label)
                            <option value="{{ $value }}" {{ old('provider', $settings['provider']) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs gateway-muted">
                        Em modo manual, o pagamento é confirmado manualmente pela equipe.
                    </p>
                </div>

                <div class="gateway-subcard p-4 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Credenciais do Mercado Pago (por loja)</h3>
                    <p class="text-xs gateway-muted">
                        Essas chaves são usadas somente para este tenant receber os pagamentos do catálogo.
                    </p>

                    <div>
                        <label for="mercadopago_access_token" class="gateway-label block text-xs uppercase mb-1">
                            Access Token (API privada)
                        </label>
                        <input id="mercadopago_access_token" name="mercadopago_access_token" type="password"
                               class="gateway-input w-full px-3 py-2 text-sm"
                               placeholder="APP_USR-xxxxxxxxxxxxxxxxxxxxxxxx">
                        <p class="mt-1 text-xs gateway-muted">
                            @if($hasTenantAccessToken)
                                Chave da loja configurada.
                            @elseif(($credentialsSource ?? 'global') === 'global' && $mercadoPagoConfigured)
                                Usando chave global do sistema (.env).
                            @else
                                Chave ainda não configurada.
                            @endif
                        </p>
                        @if($hasTenantAccessToken)
                            <label class="mt-2 inline-flex items-center gap-2 text-xs gateway-muted">
                                <input type="checkbox" name="clear_mercadopago_access_token" value="1"
                                       {{ old('clear_mercadopago_access_token') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                Remover chave desta loja
                            </label>
                        @endif
                    </div>

                    <div>
                        <label for="mercadopago_public_key" class="gateway-label block text-xs uppercase mb-1">
                            Public Key (opcional)
                        </label>
                        <input id="mercadopago_public_key" name="mercadopago_public_key" type="password"
                               class="gateway-input w-full px-3 py-2 text-sm"
                               placeholder="APP_USR-xxxxxxxxxxxxxxxxxxxxxxxx">
                        <p class="mt-1 text-xs gateway-muted">
                            @if($hasTenantPublicKey)
                                Public Key da loja configurada.
                            @else
                                Opcional para o fluxo atual de PIX.
                            @endif
                        </p>
                        @if($hasTenantPublicKey)
                            <label class="mt-2 inline-flex items-center gap-2 text-xs gateway-muted">
                                <input type="checkbox" name="clear_mercadopago_public_key" value="1"
                                       {{ old('clear_mercadopago_public_key') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                Remover public key desta loja
                            </label>
                        @endif
                    </div>
                </div>

                <label class="gateway-toggle flex items-start gap-3 p-3">
                    <input type="hidden" name="require_paid_before_order" value="0">
                    <input type="checkbox" name="require_paid_before_order" value="1"
                           {{ old('require_paid_before_order', $settings['require_paid_before_order']) ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span>
                        <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Só gerar pedido interno quando o pagamento estiver pago
                        </span>
                        <span class="block text-xs gateway-muted mt-1">
                            Se ativado, não será possível aprovar/converter pedido com pagamento pendente.
                        </span>
                    </span>
                </label>

                <label class="gateway-toggle flex items-start gap-3 p-3">
                    <input type="hidden" name="mark_failed_payments" value="0">
                    <input type="checkbox" name="mark_failed_payments" value="1"
                           {{ old('mark_failed_payments', $settings['mark_failed_payments']) ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span>
                        <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Marcar pagamentos reprovados como falhou automaticamente
                        </span>
                        <span class="block text-xs gateway-muted mt-1">
                            Isso ajuda a equipe a acompanhar tentativas sem sucesso e falar com o cliente.
                        </span>
                    </span>
                </label>

                <button type="submit" class="gateway-primary-btn inline-flex items-center justify-center px-4 py-2 text-sm font-semibold">
                    Salvar configuração
                </button>
            </form>
        </div>

        <div class="space-y-6">
            <div class="gateway-card p-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Status da integração</h3>

                <div class="flex items-center justify-between text-sm">
                    <span class="gateway-muted">Mercado Pago</span>
                    @if($mercadoPagoConfigured)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                            Configurado
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                            Não configurado
                        </span>
                    @endif
                </div>
                <div class="mt-2 text-xs gateway-muted">
                    Fonte da credencial ativa:
                    <span class="font-semibold text-gray-700 dark:text-gray-200">
                        {{ ($credentialsSource ?? 'global') === 'tenant' ? 'Chave desta loja' : 'Chave global do sistema' }}
                    </span>
                </div>
            </div>

            <div class="gateway-card p-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Webhook Mercado Pago</h3>
                <p class="text-xs gateway-muted mb-3">
                    Cadastre esta URL no painel do Mercado Pago para atualizar o status dos pedidos do catálogo.
                </p>
                <div class="flex items-center gap-2">
                    <input id="catalog-webhook-url" type="text" readonly value="{{ $webhookUrl }}"
                           class="gateway-input w-full px-3 py-2 text-xs">
                    <button type="button" onclick="copyCatalogWebhook()"
                            class="gateway-secondary-btn rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800 dark:bg-slate-600 dark:hover:bg-slate-500">
                        Copiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyCatalogWebhook() {
        const input = document.getElementById('catalog-webhook-url');
        const value = input?.value || '';

        if (!value) {
            return;
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(value).catch(() => {});
            return;
        }

        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
    }
</script>
@endpush
