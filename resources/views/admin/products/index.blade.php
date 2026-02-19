@extends('layouts.admin')

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Produtos</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Gerencie seu catálogo de produtos, tecidos e personalizações em um só lugar.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" 
                    onclick="document.getElementById('template-modal').classList.remove('hidden')"
                    class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm group">
                <i class="fa-solid fa-layer-group mr-2 text-indigo-500 group-hover:scale-110 transition-transform"></i>
                Modelos Sugeridos
            </button>
            <a href="{{ route('admin.products.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-0.5">
                <i class="fa-solid fa-plus mr-2"></i> Novo Produto
            </a>
        </div>
    </div>

    {{-- Management Hub / Quick Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Management Cards --}}
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('admin.tecidos.index') }}" class="group bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-blue-500/30 transition-all">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-scroll text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">Tecidos</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gerencie malhas e texturas</p>
            </a>
            
            <a href="{{ route('admin.personalizacoes.index') }}" class="group bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-emerald-500/30 transition-all">
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-palette text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">Personalizações</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Estampas e aplicações</p>
            </a>

            <a href="{{ route('admin.modelos.index') }}" class="group bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-purple-500/30 transition-all">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-purple-500/20">
                    <i class="fa-solid fa-shirt text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">Modelos</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cortes e variações</p>
            </a>
        </div>

        {{-- Info Alert --}}
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/50 rounded-2xl p-5 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <i class="fa-solid fa-circle-info text-8xl text-indigo-600"></i>
            </div>
            <h3 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 flex items-center mb-3">
                <i class="fa-solid fa-lightbulb mr-2"></i> Dica de Cadastro
            </h3>
            <ul class="space-y-2 text-xs text-indigo-800/80 dark:text-indigo-400">
                <li class="flex items-center gap-2">
                    <span class="w-1 h-1 bg-indigo-400 rounded-full"></span>
                    <strong>Unidade:</strong> Para acessórios e costura.
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-1 h-1 bg-indigo-400 rounded-full"></span>
                    <strong>Peso/Metro:</strong> Ideal para venda de tecidos.
                </li>
            </ul>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:max-w-xs">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" placeholder="Buscar produto..." 
                   class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none dark:text-white">
        </div>
        
        <div class="flex items-center gap-2">
            <button class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fa-solid fa-filter"></i>
            </button>
            <button class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-down-short-wide"></i>
            </button>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produto</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Preço / Tipo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Configuração</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                @if($product->images && $product->images->count() > 0)
                                    @php
                                        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                        $imageUrl = url('/storage/' . $primaryImage->image_path);
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $product->title }}" class="h-14 w-14 object-cover rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                @else
                                    <div class="h-14 w-14 bg-gray-100 dark:bg-gray-900 rounded-xl flex items-center justify-center border border-gray-100 dark:border-gray-700">
                                        <i class="fa-solid fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-[200px]">{{ $product->title }}</div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                                        <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-mono tracking-tight">{{ $product->sku ?? 'SEM SKU' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">R$ {{ number_format($product->price ?? 0, 2, ',', '.') }}</div>
                            <div class="mt-1">
                                @if($product->sale_type === 'unidade')
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                        <i class="fa-solid fa-box text-[8px]"></i> Unidade
                                    </span>
                                @elseif($product->sale_type === 'kg')
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                        <i class="fa-solid fa-weight-hanging text-[8px]"></i> Por Kg
                                    </span>
                                @elseif($product->sale_type === 'metro')
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 flex items-center gap-1">
                                        <i class="fa-solid fa-ruler-horizontal text-[8px]"></i> Por Metro
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[11px] space-y-1">
                                <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                    <i class="fa-solid fa-scroll w-3 text-center"></i>
                                    {{ $product->tecido->name ?? 'N/A' }}
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                    <i class="fa-solid fa-palette w-3 text-center"></i>
                                    {{ $product->personalizacao->name ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase tracking-widest">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 uppercase tracking-widest">
                                    Inativo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800/50 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 hover:shadow-sm transition-all"
                                   title="Editar Produto">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                    <span>Editar</span>
                                </a>
                                <form action="{{ route('admin.products.duplicate', $product) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800/50 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/50 hover:shadow-sm transition-all"
                                            title="Duplicar Produto">
                                        <i class="fa-solid fa-copy text-[10px]"></i>
                                        <span>Duplicar</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800/50 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 hover:shadow-sm transition-all"
                                            title="Excluir Produto">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        <span>Excluir</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-box-open text-4xl text-gray-300 dark:text-gray-700"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nenhum produto cadastrado</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-xs mx-auto">Comece a construir seu catálogo agora mesmo criando seu primeiro produto.</p>
                                <a href="{{ route('admin.products.create') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all shadow-md">
                                    <i class="fa-solid fa-plus mr-2"></i> Adicionar Primeiro Produto
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination fallback if needed --}}
        @if(method_exists($products, 'links'))
            <div class="px-6 py-4 bg-gray-50/30 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    // Fechar modal ao clicar fora
    window.onclick = function(event) {
        let modal = document.getElementById('template-modal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endpush

{{-- Template Selection Modal --}}
<div id="template-modal" 
     class="fixed inset-0 z-50 hidden overflow-y-auto" 
     x-data="{ 
        activeCutId: {{ $cutTypes->first()->id ?? 'null' }},
        activeCutSlug: '{{ Str::slug($cutTypes->first()->name ?? '') }}',
        templates: {{ json_encode($templates) }},
        init() {
            // Sincronizar visibilidade com o botão antigo
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        const isHidden = document.getElementById('template-modal').classList.contains('hidden');
                        this.$el.style.display = isHidden ? 'none' : 'block';
                    }
                });
            });
            observer.observe(document.getElementById('template-modal'), { attributes: true });
        }
     }"
     style="display: none;">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500/75 dark:bg-black/75 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-gray-100 dark:border-gray-700">
            <div class="bg-indigo-600 px-8 py-6 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-black text-white flex items-center tracking-tight">
                        <i class="fa-solid fa-layer-group mr-4 text-indigo-200"></i>
                        Modelos Sugeridos
                    </h3>
                    <p class="text-indigo-100 text-xs font-bold mt-1 uppercase tracking-widest opacity-80">Selecione um tipo de corte para ver os modelos</p>
                </div>
                <button type="button" @click="document.getElementById('template-modal').classList.add('hidden')" class="text-indigo-100 hover:text-white bg-indigo-500/30 hover:bg-indigo-500/50 w-10 h-10 rounded-xl flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <div class="p-0 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 flex items-center overflow-x-auto scrollbar-hide">
                @foreach($cutTypes as $cut)
                    <button type="button" 
                            @click="activeCutId = {{ $cut->id }}; activeCutSlug = '{{ Str::slug($cut->name) }}'"
                            class="px-8 py-4 text-sm font-black whitespace-nowrap transition-all border-b-2"
                            :class="activeCutId == {{ $cut->id }} ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400 bg-white dark:bg-gray-800' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/30'">
                        {{ $cut->name }}
                    </button>
                @endforeach
            </div>

            <div class="p-8 max-h-[60vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($templates as $id => $template)
                    <div class="group bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-indigo-500/50 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col"
                         x-show="{{ json_encode($template['compatible_cuts']) }}.includes(activeCutSlug)">
                        
                        <div class="p-6 flex-grow">
                            <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center mb-6 shadow-inner group-hover:scale-110 transition-transform duration-500">
                                <i class="fa-solid {{ $template['icon'] }} text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                            </div>
                            
                            <h4 class="text-lg font-black text-gray-900 dark:text-white mb-2 leading-tight tracking-tight">{{ $template['title'] }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2">
                                {{ $template['description'] }}
                            </p>
                            
                            <div class="mt-6 flex flex-wrap gap-2">
                                <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-indigo-100/50 dark:border-indigo-800/50">
                                    {{ $template['category'] }}
                                </span>
                                <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-emerald-100/50 dark:border-emerald-800/50">
                                    R$ {{ number_format($template['default_price'], 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="px-6 pb-6 mt-auto">
                            <form action="{{ route('admin.products.import-template') }}" method="POST">
                                @csrf
                                <input type="hidden" name="template_id" value="{{ $id }}">
                                <input type="hidden" name="cut_type_id" :value="activeCutId">
                                <button type="submit" class="w-full py-3.5 bg-gray-900 dark:bg-indigo-600 hover:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-xl text-sm font-black shadow-lg shadow-gray-200 dark:shadow-none transition-all flex items-center justify-center group/btn active:scale-95">
                                    <i class="fa-solid fa-download mr-2 group-hover/btn:translate-y-0.5 transition-transform"></i> 
                                    Importar p/ <span class="mx-1" x-text="' ' + activeCutSlug"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach

                    {{-- Empty State for Cut Type --}}
                    <div class="col-span-full py-16 flex flex-col items-center text-center" 
                         x-show="!Object.values(templates).some(t => t.compatible_cuts.includes(activeCutSlug))">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700/30 rounded-3xl flex items-center justify-center mb-4">
                            <i class="fa-solid fa-folder-open text-3xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Nenhum modelo compatível</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-xs">
                            Não encontramos modelos sugeridos específicos para o corte <span class="font-bold text-indigo-500" x-text="activeCutSlug"></span>.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-900/80 px-8 py-5 flex justify-between items-center border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-400"></i>
                    A importação criará um novo produto editável
                </span>
                <button type="button" 
                        @click="document.getElementById('template-modal').classList.add('hidden')"
                        class="px-6 py-2.5 text-sm font-black text-gray-600 dark:text-gray-400 hover:text-red-500 transition-colors">
                    Fechar Galeria
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

