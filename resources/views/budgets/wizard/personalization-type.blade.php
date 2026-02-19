@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto text-gray-100">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-3">
                <div class="w-7 h-7 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</div>
                <div>
                    <span class="text-base font-semibold text-purple-400">Escolha o Tipo de Personalização</span>
                    <p class="text-xs text-gray-400">Etapa 2 de 4</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400">Progresso</div>
                <div class="text-sm font-bold text-purple-400">50%</div>
            </div>
        </div>
        <div class="w-full bg-gray-800 rounded-full h-1.5">
            <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 50%"></div>
        </div>
    </div>

    <div class="bg-[#0b0f1a] border border-[#1f2937] rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-[#1f2937]">
            <h1 class="text-xl font-bold text-white">Tipos de Personalização</h1>
            <p class="text-sm text-gray-400">Selecione uma ou mais opções para este orçamento.</p>
        </div>

        <form method="POST" action="{{ route('budget.personalization-type') }}">
            @csrf
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $options = [
                        'sub_local' => 'Sublimação Local',
                        'sub_total' => 'Sublimação Total',
                        'serigrafia' => 'Serigrafia',
                        'dtf' => 'DTF',
                        'bordado' => 'Bordado',
                        'emborrachado' => 'Emborrachado',
                        'lisas' => 'Lisas',
                    ];
                @endphp
                @foreach($options as $key => $label)
                    <label class="relative flex items-start gap-3 p-5 rounded-2xl bg-purple-600/80 hover:bg-purple-600 transition border border-purple-400/40 cursor-pointer">
                        <input type="checkbox" name="types[]" value="{{ $key }}"
                               class="mt-1 h-4 w-4 text-purple-900 bg-white border-gray-300 rounded focus:ring-purple-200"
                               {{ in_array($key, $selectedTypes ?? []) ? 'checked' : '' }}>
                        <div>
                            <div class="font-bold text-white text-lg">{{ $label }}</div>
                            <div class="text-xs text-purple-100/80">Aplicável para itens deste orçamento.</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="flex justify-between items-center px-6 py-5 border-t border-[#1f2937] bg-[#0b0f1a]">
                <a href="{{ route('budget.start') }}" class="text-gray-300 hover:text-white text-sm">← Voltar</a>
                <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-sm font-semibold transition">
                    Continuar →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
