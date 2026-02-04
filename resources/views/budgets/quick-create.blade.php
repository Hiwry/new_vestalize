@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Orçamento Rápido</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Crie um orçamento em segundos</p>
                </div>
            </div>
            <a href="{{ route('budget.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                ← Voltar
            </a>
        </div>
    </div>

    <form id="quickBudgetForm" method="POST" action="{{ route('budget.quick-store') }}" 
          x-data="quickBudgetForm()" @submit.prevent="submitForm">
        @csrf

        {{-- BLOCO 1 - Contato --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Contato</h2>
                    <span class="text-xs text-red-500 font-medium ml-2">obrigatório</span>
                </div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do contato *</label>
                    <input type="text" name="contact_name" x-model="form.contact_name" required
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                           placeholder="Ex: Maria Oliveira">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp *</label>
                    <input type="text" name="contact_phone" x-model="form.contact_phone" required
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                           placeholder="(00) 00000-0000"
                           x-mask="(99) 99999-9999">
                </div>
            </div>
        </div>

        {{-- BLOCO 2 - Item Resumido --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Item</h2>
                    <span class="text-xs text-gray-400 ml-2">(uso interno, cliente não vê o produto)</span>
                </div>
            </div>
            <div class="p-5 space-y-4">
                {{-- Produto interno --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produto (interno)</label>
                    <input type="text" name="product_internal" x-model="form.product_internal"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                           placeholder="Ex: Camiseta Básica, Camisa Polo, etc">
                </div>

                {{-- Personalização --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Personalização *</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <template x-for="tech in techniques" :key="tech">
                            <button type="button" 
                                    @click="form.technique = tech"
                                    :class="form.technique === tech 
                                        ? 'bg-indigo-600 text-white border-indigo-600' 
                                        : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:border-indigo-400'"
                                    class="px-4 py-2 rounded-lg border text-sm font-medium transition-all">
                                <span x-text="tech"></span>
                            </button>
                        </template>
                    </div>
                    <input type="text" name="technique" x-model="form.technique" required
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                           placeholder="Ou digite a personalização...">
                </div>

                {{-- Quantidade e Valor --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade *</label>
                        <input type="number" name="quantity" x-model.number="form.quantity" required min="1"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                               placeholder="100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor unitário (R$) *</label>
                        <input type="number" name="unit_price" x-model.number="form.unit_price" required min="0.01" step="0.01"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                               placeholder="26.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prazo (dias)</label>
                        <input type="number" name="deadline_days" x-model.number="form.deadline_days" min="1" max="365"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                               placeholder="15">
                    </div>
                </div>
            </div>
        </div>

        {{-- BLOCO 3 - Observação --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Observação</h2>
                    <span class="text-xs text-gray-400 ml-2">(opcional)</span>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex flex-wrap gap-2">
                    <template x-for="opt in observationOptions" :key="opt">
                        <button type="button" 
                                @click="toggleObservation(opt)"
                                :class="form.observations.includes(opt) 
                                    ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border-indigo-300 dark:border-indigo-600' 
                                    : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-600 hover:border-indigo-300'"
                                class="px-3 py-1.5 rounded-full border text-xs font-medium transition-all">
                            <span x-text="opt"></span>
                        </button>
                    </template>
                </div>
                {{-- Campo de texto --}}
                <textarea name="observations" x-model="form.observations" rows="2"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm resize-none"
                          placeholder="Ou digite uma observação personalizada..."></textarea>
            </div>
        </div>

        {{-- BLOCO 4 - Detalhes Avançados (Colapsado) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4 overflow-hidden" x-data="{ open: false }">
            <button type="button" @click="open = !open" 
                    class="w-full px-5 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Detalhes técnicos</h2>
                    <span class="text-xs text-gray-400 ml-2">(opcional)</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                <div class="p-5 text-sm text-gray-500 dark:text-gray-400">
                    <p class="italic">Para detalhes como cores, gola, tamanhos e acréscimos, use o orçamento completo ou converta este orçamento em pedido após aprovação.</p>
                </div>
            </div>
        </div>

        {{-- BLOCO 5 - Resumo --}}
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl shadow-lg mb-6 overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h2 class="font-semibold text-white stay-white" style="color: white !important;">Resumo do Orçamento</h2>
                </div>
            </div>
            <div class="p-5 text-white" style="color: white !important;">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="text-xs text-white/60 mb-1" style="color: rgba(255,255,255,0.6) !important;">Quantidade</p>
                        <p class="text-lg font-bold" style="color: white !important;" x-text="(form.quantity || 0) + ' peças'"></p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="text-xs text-white/60 mb-1" style="color: rgba(255,255,255,0.6) !important;">Personalização</p>
                        <p class="text-lg font-bold" style="color: white !important;" x-text="form.technique || '-'"></p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="text-xs text-white/60 mb-1" style="color: rgba(255,255,255,0.6) !important;">Valor unitário</p>
                        <p class="text-lg font-bold" style="color: white !important;" x-text="formatCurrency(form.unit_price || 0)"></p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="text-xs text-white/60 mb-1" style="color: rgba(255,255,255,0.6) !important;">Prazo</p>
                        <p class="text-lg font-bold" style="color: white !important;" x-text="(form.deadline_days || 15) + ' dias'"></p>
                    </div>
                </div>
                <div class="bg-white/20 rounded-lg p-4 text-center">
                    <p class="text-sm text-white/80 mb-1" style="color: rgba(255,255,255,0.8) !important;">Total do Orçamento</p>
                    <p class="text-3xl font-bold" style="color: white !important;" x-text="formatCurrency(total)"></p>
                </div>
            </div>
        </div>

        {{-- CTA Buttons --}}
        <div class="flex flex-wrap gap-3 justify-center">
            <button type="submit" name="action" value="save" :disabled="!isValid || loading"
                    style="color: white !important;"
                    class="flex items-center space-x-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-400 text-white rounded-lg font-medium shadow-lg transition-all disabled:cursor-not-allowed">
                <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white !important;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" style="color: white !important;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span style="color: white !important;">Salvar Orçamento</span>
            </button>
            <button type="button" @click="saveAndPdf()" :disabled="!isValid || loading"
                    style="color: white !important;"
                    class="flex items-center space-x-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white rounded-lg font-medium shadow-lg transition-all disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white !important;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span style="color: white !important;">Gerar PDF</span>
            </button>
            <button type="button" @click="saveAndCopy()" :disabled="!isValid || loading"
                    style="color: white !important;"
                    class="flex items-center space-x-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-400 text-white rounded-lg font-medium shadow-lg transition-all disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white !important;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span style="color: white !important;">Copiar</span>
            </button>
            <button type="button" @click="saveAndWhatsApp()" :disabled="!isValid || loading"
                    style="color: white !important;"
                    class="flex items-center space-x-2 px-6 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg font-medium shadow-lg transition-all disabled:cursor-not-allowed">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" style="color: white !important;">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                <span style="color: white !important;">Enviar WhatsApp</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function quickBudgetForm() {
    return {
        form: {
            contact_name: '',
            contact_phone: '',
            product_internal: '',
            technique: '',
            quantity: null,
            unit_price: null,
            deadline_days: 15,
            observations: ''
        },
        techniques: ['Silk 1 cor', 'Silk 2 cores', 'Bordado', 'Sublimação', 'Sublimação Local', 'DTF'],
        loading: false,
        savedBudget: null,
        observationOptions: @json($observationOptions),

        get total() {
            return (this.form.quantity || 0) * (this.form.unit_price || 0);
        },

        get isValid() {
            return this.form.contact_name.trim() !== '' &&
                   this.form.contact_phone.trim() !== '' &&
                   this.form.technique !== '' &&
                   this.form.quantity > 0 &&
                   this.form.unit_price > 0;
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
        },

        toggleObservation(opt) {
            const current = this.form.observations;
            if (current.includes(opt)) {
                // Remove the option
                this.form.observations = current.replace(opt, '').replace(/\n+/g, '\n').replace(/^\n|\n$/g, '').trim();
            } else {
                // Add the option
                this.form.observations = current ? current + '\n' + opt : opt;
            }
        },

        async submitForm() {
            if (!this.isValid) return;
            
            this.loading = true;
            try {
                const response = await fetch('{{ route("budget.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.savedBudget = data;
                    alert('Orçamento salvo com sucesso!');
                    window.location.href = data.redirect_url;
                } else {
                    alert('Erro: ' + (data.message || 'Erro ao salvar orçamento'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erro ao salvar orçamento. Tente novamente.');
            } finally {
                this.loading = false;
            }
        },

        async saveAndPdf() {
            if (!this.isValid) return;
            
            this.loading = true;
            try {
                const response = await fetch('{{ route("budget.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.savedBudget = data;
                    // Open PDF in new tab
                    window.open(data.pdf_url, '_blank');
                    // Redirect to detail page
                    window.location.href = data.redirect_url;
                } else {
                    alert('Erro: ' + (data.message || 'Erro ao salvar orçamento'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erro ao salvar orçamento. Tente novamente.');
            } finally {
                this.loading = false;
            }
        },
        
        async saveAndCopy() {
            if (!this.isValid) return;
            
            this.loading = true;
            try {
                const response = await fetch('{{ route("budget.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.savedBudget = data;
                    
                    // Fetch message text
                    const msgResponse = await fetch(data.whatsapp_url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const msgData = await msgResponse.json();

                    if (msgData.message) {
                        await navigator.clipboard.writeText(msgData.message);
                        alert('Informações copiadas! Redirecionando...');
                    }
                    
                    window.location.href = data.redirect_url;
                } else {
                    alert('Erro: ' + (data.message || 'Erro ao salvar orçamento'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erro ao salvar orçamento. Tente novamente.');
            } finally {
                this.loading = false;
            }
        },

        async saveAndWhatsApp() {
            if (!this.isValid) return;
            
            this.loading = true;
            try {
                const response = await fetch('{{ route("budget.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.savedBudget = data;
                    // Open WhatsApp in new tab
                    window.open(data.whatsapp_url, '_blank');
                    // Redirect to detail page
                    window.location.href = data.redirect_url;
                } else {
                    alert('Erro: ' + (data.message || 'Erro ao salvar orçamento'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erro ao salvar orçamento. Tente novamente.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
