@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6" x-data="quickBudgetBuilder()">
    <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(249,115,22,0.16),_transparent_26%),linear-gradient(135deg,_#fffaf3,_#ffffff_48%,_#f3fbff)] p-6 shadow-[0_24px_80px_-32px_rgba(15,23,42,0.35)] dark:border-slate-700 dark:bg-[linear-gradient(135deg,_#0f172a,_#111827_48%,_#172554)] sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <span class="inline-flex rounded-full border border-sky-200 bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.28em] text-sky-700 dark:border-sky-800 dark:bg-slate-900/70 dark:text-sky-300">Orçamento rápido</span>
                <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Fluxo rápido, mas com vários itens no mesmo orçamento.</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300 sm:text-base">Preencha contato, adicione peças em sequência e acompanhe o total sem sair da tela.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Itens</p>
                    <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white" x-text="items.length"></p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Peças</p>
                    <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white" x-text="totalQuantity"></p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Prazo</p>
                    <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white"><span x-text="form.deadline_days"></span>d</p>
                </div>
                <div class="rounded-2xl border border-cyan-200 bg-[linear-gradient(135deg,_#ecfeff,_#f0fdfa_52%,_#f8fafc)] p-4 text-slate-900 dark:border-slate-600 dark:bg-slate-950 dark:text-white">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-cyan-700 dark:text-slate-400">Total</p>
                    <p class="mt-2 text-lg font-black text-slate-900 dark:text-white sm:text-xl" x-text="formatCurrency(grandTotal)"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <template x-if="serverError">
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200" x-text="serverError"></div>
            </template>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">1. Contato</p>
                            <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">Dados essenciais</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Só o que precisa para sair rápido.</p>
                        </div>
                        <a href="{{ route('budget.index') }}" class="inline-flex items-center rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300 dark:hover:text-white">Voltar</a>
                    </div>

                    <div class="mt-6 grid gap-4">
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Nome do contato</span>
                            <input type="text" x-model="form.contact_name" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-sky-500 dark:focus:bg-slate-900 dark:focus:ring-sky-900/50" placeholder="Ex: Maria Oliveira">
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">WhatsApp</span>
                            <input type="text" x-model="form.contact_phone" x-mask="(99) 99999-9999" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-sky-500 dark:focus:bg-slate-900 dark:focus:ring-sky-900/50" placeholder="(00) 00000-0000">
                        </label>
                        <div>
                            <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Prazo estimado</span>
                            <div class="grid grid-cols-4 gap-2">
                                <template x-for="day in quickDeadlines" :key="day">
                                    <button type="button" @click="form.deadline_days = day" :class="form.deadline_days === day ? 'border-cyan-300 bg-cyan-50 text-cyan-900 ring-2 ring-cyan-100 dark:border-sky-400 dark:bg-sky-500 dark:text-slate-950 dark:ring-0' : 'border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200'" class="rounded-2xl border px-3 py-2 text-sm font-semibold transition hover:border-slate-400">
                                        <span x-text="day + ' dias'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">2. Observações</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">Recados rápidos</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Use sugestões prontas e complemente se quiser.</p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <template x-for="opt in observationOptions" :key="opt">
                            <button type="button" @click="toggleObservation(opt)" :class="form.observations.includes(opt) ? 'border-amber-300 bg-amber-100 text-amber-800 dark:border-amber-700 dark:bg-amber-900/40 dark:text-amber-200' : 'border-slate-200 bg-slate-50 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300'" class="rounded-full border px-3 py-1.5 text-xs font-semibold transition hover:border-amber-400">
                                <span x-text="opt"></span>
                            </button>
                        </template>
                    </div>

                    <label class="mt-5 block">
                        <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Observações gerais</span>
                        <textarea x-model="form.observations" rows="8" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-amber-400 focus:bg-white focus:ring-4 focus:ring-amber-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-amber-500 dark:focus:bg-slate-900 dark:focus:ring-amber-900/50" placeholder="Ex: valores válidos para tamanhos padrão, prazo sujeito à aprovação..."></textarea>
                    </label>
                </section>
            </div>

            <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">3. Itens</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">Mais de um item no mesmo orçamento</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Adicione um item, confirme e siga para o próximo.</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200">
                        <span class="font-semibold">Resumo em tempo real:</span> quantidade, prazo e total já consolidados.
                    </div>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)]">
                    <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50/80 p-5 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="editingIndex === null ? 'Novo item rápido' : 'Editar item'"></h3>
                            <template x-if="editingIndex !== null">
                                <button type="button" @click="cancelEdit()" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 transition hover:text-slate-900 dark:hover:text-white">Cancelar</button>
                            </template>
                        </div>

                        <div class="mt-5 space-y-4">
                            <label class="block">
                                <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Produto interno</span>
                                <input type="text" x-model="draft.product_internal" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-900/50" placeholder="Ex: Camiseta básica">
                            </label>

                            <div>
                                <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Técnica</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="tech in techniques" :key="tech">
                                        <button type="button" @click="setTechnique(tech)" :class="draft.technique_type === tech ? 'border-cyan-300 bg-cyan-50 text-cyan-900 ring-2 ring-cyan-100 dark:border-sky-400 dark:bg-sky-500 dark:text-slate-950 dark:ring-0' : 'border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200'" class="rounded-full border px-3 py-2 text-xs font-semibold transition hover:border-slate-400">
                                            <span x-text="tech"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Tamanho da aplicação</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="size in applicationSizes" :key="size">
                                        <button type="button" @click="setSize(size)" :class="draft.application_size === size ? 'border-amber-300 bg-amber-50 text-amber-900 ring-2 ring-amber-100 dark:border-amber-500 dark:bg-amber-400 dark:text-slate-950 dark:ring-0' : 'border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200'" class="rounded-full border px-3 py-2 text-xs font-semibold transition hover:border-amber-300">
                                            <span x-text="size"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <label class="block">
                                <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Descrição da personalização</span>
                                <input type="text" x-model="draft.technique" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-900/50" placeholder="Ex: Serigrafia - A4">
                            </label>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="block">
                                    <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Quantidade</span>
                                    <input type="number" min="1" x-model.number="draft.quantity" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-900/50">
                                </label>
                                <label class="block">
                                    <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Valor unitário</span>
                                    <input type="number" min="0.01" step="0.01" x-model.number="draft.unit_price" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-900/50">
                                </label>
                            </div>

                            <label class="block">
                                <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Notas do item</span>
                                <textarea x-model="draft.notes" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-900/50" placeholder="Ex: frente e costas, ajuste de arte..."></textarea>
                            </label>
                        </div>

                        <template x-if="draftError">
                            <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200" x-text="draftError"></div>
                        </template>

                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="rounded-2xl border border-cyan-200 bg-[linear-gradient(135deg,_#ecfeff,_#f0fdfa)] px-4 py-3 text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <p class="text-[11px] uppercase tracking-[0.24em] text-cyan-700 dark:text-slate-400">Total do item</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white" x-text="formatCurrency(draftTotal)"></p>
                            </div>
                            <button type="button" @click="saveDraft()" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/20 transition hover:translate-y-[-1px] hover:shadow-sky-500/30">
                                <span x-text="editingIndex === null ? 'Adicionar item' : 'Atualizar item'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <template x-if="items.length === 0">
                            <div class="flex min-h-[280px] flex-col items-center justify-center rounded-[24px] border border-slate-200 bg-[linear-gradient(180deg,_#fff,_#f8fafc)] p-6 text-center dark:border-slate-700 dark:bg-[linear-gradient(180deg,_#0f172a,_#111827)]">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-900/40 dark:text-sky-300">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" /></svg>
                                </div>
                                <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">Nenhum item adicionado</h3>
                                <p class="mt-2 max-w-sm text-sm text-slate-500 dark:text-slate-400">Comece por uma peça. Depois você segue empilhando os demais itens aqui mesmo.</p>
                            </div>
                        </template>

                        <template x-for="(item, index) in items" :key="item.uid">
                            <article class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 text-xs font-bold text-cyan-900 dark:bg-sky-500 dark:text-slate-950" x-text="index + 1"></span>
                                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="item.product_internal || 'Item rápido'"></h3>
                                        </div>
                                        <p class="mt-2 text-sm font-medium text-sky-700 dark:text-sky-300" x-text="item.technique"></p>
                                        <template x-if="item.notes">
                                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400" x-text="item.notes"></p>
                                        </template>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" @click="editItem(index)" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-slate-400 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300 dark:hover:text-white">Editar</button>
                                        <button type="button" @click="removeItem(index)" class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:border-rose-300 dark:border-rose-900/60 dark:text-rose-300">Remover</button>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
                                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Qtd</p>
                                        <p class="mt-1 text-lg font-black text-slate-900 dark:text-white" x-text="item.quantity"></p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
                                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Unitário</p>
                                        <p class="mt-1 text-lg font-black text-slate-900 dark:text-white" x-text="formatCurrency(item.unit_price)"></p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
                                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Aplicação</p>
                                        <p class="mt-1 text-lg font-black text-slate-900 dark:text-white" x-text="item.application_size || '-'"></p>
                                    </div>
                                    <div class="rounded-2xl border border-cyan-200 bg-[linear-gradient(135deg,_#ecfeff,_#f0fdfa)] px-4 py-3 text-slate-900 dark:border-sky-400 dark:bg-sky-500 dark:text-slate-950">
                                        <p class="text-[11px] uppercase tracking-[0.2em] text-cyan-700 dark:text-slate-900/70">Total</p>
                                        <p class="mt-1 text-lg font-black" x-text="formatCurrency(itemTotal(item))"></p>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>
            </section>
        </div>

        <aside class="xl:sticky xl:top-6 xl:self-start">
            <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="border-b border-cyan-100 bg-[linear-gradient(135deg,_#ecfeff,_#ccfbf1_55%,_#f0fdfa)] px-6 py-5 text-slate-900 dark:border-slate-700 dark:bg-[linear-gradient(135deg,_#082f49,_#0f766e)] dark:text-white">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-700 dark:text-cyan-200">Resumo</p>
                    <h2 class="mt-2 text-2xl font-black">Fechamento rápido</h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-cyan-100/80">Tudo pronto para salvar, enviar ou gerar PDF.</p>
                </div>

                <div class="space-y-5 p-6">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Itens</p>
                            <p class="mt-1 text-xl font-black text-slate-900 dark:text-white" x-text="items.length"></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Peças</p>
                            <p class="mt-1 text-xl font-black text-slate-900 dark:text-white" x-text="totalQuantity"></p>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-cyan-200 bg-[linear-gradient(145deg,_#ecfeff,_#f0fdfa_52%,_#ffffff)] px-5 py-5 text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <p class="text-xs uppercase tracking-[0.24em] text-cyan-700 dark:text-slate-400">Total do orçamento</p>
                        <p class="mt-2 text-4xl font-black text-slate-900 dark:text-white" x-text="formatCurrency(grandTotal)"></p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Prazo estimado: <span class="font-semibold text-slate-900 dark:text-white" x-text="form.deadline_days + ' dias'"></span></p>
                    </div>

                    <div class="space-y-3">
                        <button type="button" @click="submitForm('save')" :disabled="!canSubmit || loading" class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300 dark:disabled:bg-slate-700">
                            <span x-text="loadingAction === 'save' ? 'Salvando...' : 'Salvar orçamento'"></span>
                        </button>
                        <button type="button" @click="submitForm('pdf')" :disabled="!canSubmit || loading" class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:bg-slate-300 dark:disabled:bg-slate-700">
                            <span x-text="loadingAction === 'pdf' ? 'Gerando...' : 'Salvar e gerar PDF'"></span>
                        </button>
                        <button type="button" @click="submitForm('copy')" :disabled="!canSubmit || loading" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-transparent dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white">
                            <span x-text="loadingAction === 'copy' ? 'Copiando...' : 'Salvar e copiar texto'"></span>
                        </button>
                        <button type="button" @click="submitForm('whatsapp')" :disabled="!canSubmit || loading" class="inline-flex w-full items-center justify-center rounded-2xl bg-[#25D366] px-5 py-3 text-sm font-bold text-slate-950 transition hover:brightness-95 disabled:cursor-not-allowed disabled:bg-slate-300 dark:disabled:bg-slate-700">
                            <span x-text="loadingAction === 'whatsapp' ? 'Abrindo...' : 'Salvar e abrir WhatsApp'"></span>
                        </button>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <p class="font-semibold text-slate-900 dark:text-white">Checklist</p>
                        <ul class="mt-2 space-y-1.5">
                            <li :class="form.contact_name.trim() ? 'text-emerald-600 dark:text-emerald-300' : ''">Contato preenchido</li>
                            <li :class="form.contact_phone.trim() ? 'text-emerald-600 dark:text-emerald-300' : ''">WhatsApp informado</li>
                            <li :class="items.length ? 'text-emerald-600 dark:text-emerald-300' : ''">Ao menos um item adicionado</li>
                        </ul>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>

@push('scripts')
<script>
function quickBudgetBuilder() {
    return {
        form: { contact_name: '', contact_phone: '', deadline_days: 15, observations: '' },
        draft: { product_internal: '', technique: '', technique_type: '', application_size: '', quantity: 1, unit_price: null, notes: '' },
        items: [],
        editingIndex: null,
        loading: false,
        loadingAction: '',
        draftError: '',
        serverError: '',
        techniques: ['Serigrafia', 'Bordado', 'Sublimação', 'Sublimação Local', 'Sublimação Total', 'DTF'],
        applicationSizes: ['ESCUDO', 'A5', 'A4', 'A3', 'A2'],
        quickDeadlines: [7, 10, 15, 20],
        observationOptions: @json($observationOptions),

        get draftTotal() { return (this.draft.quantity || 0) * (this.draft.unit_price || 0); },
        get totalQuantity() { return this.items.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0); },
        get grandTotal() { return this.items.reduce((sum, item) => sum + this.itemTotal(item), 0); },
        get canSubmit() { return this.form.contact_name.trim() !== '' && this.form.contact_phone.trim() !== '' && this.items.length > 0; },

        formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(value || 0));
        },

        itemTotal(item) {
            return (Number(item.quantity) || 0) * (Number(item.unit_price) || 0);
        },

        toggleObservation(opt) {
            const lines = this.form.observations ? this.form.observations.split('\n') : [];
            if (lines.includes(opt)) this.form.observations = lines.filter(line => line !== opt).join('\n');
            else this.form.observations = [...lines.filter(Boolean), opt].join('\n');
        },

        setTechnique(tech) {
            this.draft.technique_type = tech;
            this.syncDraftTechnique();
        },

        setSize(size) {
            this.draft.application_size = size;
            this.syncDraftTechnique();
        },

        syncDraftTechnique() {
            const parts = [];
            if (this.draft.technique_type) parts.push(this.draft.technique_type);
            if (this.draft.application_size) parts.push(this.draft.application_size);
            if (parts.length) this.draft.technique = parts.join(' - ');
        },

        validateDraft() {
            if (!this.draft.technique.trim()) return 'Informe a personalização do item.';
            if (!this.draft.quantity || Number(this.draft.quantity) < 1) return 'Defina uma quantidade válida.';
            if (!this.draft.unit_price || Number(this.draft.unit_price) <= 0) return 'Defina um valor unitário válido.';
            return '';
        },

        saveDraft() {
            this.draftError = this.validateDraft();
            if (this.draftError) return;

            const item = {
                uid: this.editingIndex === null ? `${Date.now()}-${Math.random()}` : this.items[this.editingIndex].uid,
                product_internal: this.draft.product_internal.trim(),
                technique: this.draft.technique.trim(),
                technique_type: this.draft.technique_type.trim(),
                application_size: this.draft.application_size.trim(),
                quantity: Number(this.draft.quantity),
                unit_price: Number(this.draft.unit_price),
                notes: this.draft.notes.trim()
            };

            if (this.editingIndex === null) this.items.unshift(item);
            else this.items.splice(this.editingIndex, 1, item);

            this.cancelEdit();
        },

        editItem(index) {
            const item = this.items[index];
            this.editingIndex = index;
            this.draft = {
                product_internal: item.product_internal || '',
                technique: item.technique || '',
                technique_type: item.technique_type || '',
                application_size: item.application_size || '',
                quantity: item.quantity || 1,
                unit_price: item.unit_price || null,
                notes: item.notes || ''
            };
            this.draftError = '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        cancelEdit() {
            this.editingIndex = null;
            this.draftError = '';
            this.draft = { product_internal: '', technique: '', technique_type: '', application_size: '', quantity: 1, unit_price: null, notes: '' };
        },

        removeItem(index) {
            this.items.splice(index, 1);
            if (this.editingIndex === index) this.cancelEdit();
            else if (this.editingIndex !== null && this.editingIndex > index) this.editingIndex -= 1;
        },

        buildPayload() {
            return {
                contact_name: this.form.contact_name.trim(),
                contact_phone: this.form.contact_phone.trim(),
                deadline_days: Number(this.form.deadline_days) || 15,
                observations: this.form.observations.trim(),
                items: this.items.map(item => ({
                    product_internal: item.product_internal,
                    technique: item.technique,
                    technique_type: item.technique_type,
                    application_size: item.application_size,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    notes: item.notes
                }))
            };
        },

        async submitForm(action) {
            this.serverError = '';
            if (!this.canSubmit) {
                this.serverError = 'Preencha contato, WhatsApp e adicione pelo menos um item.';
                return;
            }

            this.loading = true;
            this.loadingAction = action;

            try {
                const response = await fetch('{{ route("budget.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.buildPayload())
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    if (data.errors) {
                        const firstField = Object.keys(data.errors)[0];
                        this.serverError = data.errors[firstField][0];
                    } else {
                        this.serverError = data.message || 'Erro ao salvar orçamento rápido.';
                    }
                    return;
                }

                if (action === 'pdf') {
                    window.open(data.pdf_url, '_blank');
                    window.location.href = data.redirect_url;
                    return;
                }

                if (action === 'copy') {
                    const msgResponse = await fetch(data.whatsapp_url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const msgData = await msgResponse.json();
                    if (msgData.message) await navigator.clipboard.writeText(msgData.message);
                    window.location.href = data.redirect_url;
                    return;
                }

                if (action === 'whatsapp') {
                    window.open(data.whatsapp_url, '_blank');
                    window.location.href = data.redirect_url;
                    return;
                }

                window.location.href = data.redirect_url;
            } catch (error) {
                console.error(error);
                this.serverError = 'Erro ao salvar orçamento rápido. Tente novamente.';
            } finally {
                this.loading = false;
                this.loadingAction = '';
            }
        }
    };
}
</script>
@endpush
@endsection
