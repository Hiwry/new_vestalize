@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-graduation-cap text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Gerenciar Tutoriais</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Categorias, temas e vídeos do YouTube</p>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        {{-- Error Messages --}}
        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-sm font-bold">
            @foreach($errors->all() as $error)
                <p><i class="fa-solid fa-exclamation-circle mr-1"></i> {{ $error }}</p>
            @endforeach
        </div>
        @endif

        {{-- Add New Category --}}
        <div class="bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700/50 rounded-2xl p-5" x-data="{ showForm: false }">
            <button @click="showForm = !showForm" class="flex items-center gap-2 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                <i class="fa-solid fa-plus"></i> Nova Categoria
            </button>
            <form x-show="showForm" x-cloak action="{{ route('admin.tutorials.categories.store') }}" method="POST" class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nome</label>
                    <input type="text" name="nome" required placeholder="Ex: Vendas" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Perfil</label>
                    <select name="perfil" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                        <option value="admin">Administrador</option>
                        <option value="admin_loja">Admin de Loja</option>
                        <option value="vendedor">Vendedor</option>
                        <option value="producao">Produção</option>
                        <option value="caixa">Caixa</option>
                        <option value="estoque">Estoque</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Ícone (Font Awesome)</label>
                    <input type="text" name="icone" placeholder="fa-folder" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all">
                    <i class="fa-solid fa-check mr-1"></i> Criar
                </button>
            </form>
        </div>

        {{-- Categories by Profile --}}
        @foreach(['admin' => 'Administrador', 'admin_loja' => 'Admin de Loja', 'vendedor' => 'Vendedor', 'producao' => 'Produção', 'caixa' => 'Caixa', 'estoque' => 'Estoque'] as $perfil => $perfilLabel)
            @php $perfilCats = $categories->get($perfil, collect()); @endphp
            
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    @php
                        $perfilColors = [
                            'admin' => 'from-purple-500 to-indigo-600',
                            'admin_loja' => 'from-blue-500 to-cyan-600',
                            'vendedor' => 'from-amber-500 to-orange-600',
                            'producao' => 'from-emerald-500 to-teal-600',
                            'caixa' => 'from-pink-500 to-rose-600',
                            'estoque' => 'from-sky-500 to-blue-600',
                        ];
                    @endphp
                    <div class="w-1.5 h-7 rounded-full bg-gradient-to-b {{ $perfilColors[$perfil] }}"></div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white">{{ $perfilLabel }}</h2>
                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest bg-gray-100 dark:bg-gray-800 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700">
                        {{ $perfilCats->count() }} {{ $perfilCats->count() === 1 ? 'categoria' : 'categorias' }}
                    </span>
                </div>

                @forelse($perfilCats as $cat)
                <div class="bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700/50 rounded-2xl overflow-hidden" x-data="{ editCat: false, addVideo: false }">
                    {{-- Category Header --}}
                    <div class="flex flex-wrap items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                                <i class="fa-solid {{ $cat->icone }} text-sm text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $cat->nome }}</span>
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 ml-2">({{ $cat->tutorials->count() }} vídeos)</span>
                            </div>
                            @if(!$cat->ativo)
                                <span class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-400/10 px-2 py-0.5 rounded-lg">INATIVA</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <button @click="addVideo = !addVideo" class="px-3 py-1.5 bg-green-50 dark:bg-green-500/10 hover:bg-green-100 dark:hover:bg-green-500/20 text-green-700 dark:text-green-400 text-xs font-bold rounded-lg transition-all" title="Adicionar vídeo">
                                <i class="fa-solid fa-plus mr-1"></i> Vídeo
                            </button>
                            <button @click="editCat = !editCat" class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-lg transition-all" title="Editar">
                                <i class="fa-solid fa-pen text-[10px]"></i>
                            </button>
                            <form action="{{ route('admin.tutorials.categories.toggle', $cat) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold rounded-lg transition-all" title="{{ $cat->ativo ? 'Desativar' : 'Ativar' }}">
                                    <i class="fa-solid {{ $cat->ativo ? 'fa-eye-slash' : 'fa-eye' }} text-[10px]"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.tutorials.categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Remover categoria e todos os vídeos?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg transition-all" title="Excluir">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit Category Form --}}
                    <div x-show="editCat" x-cloak class="p-4 bg-indigo-50/50 dark:bg-indigo-500/5 border-b border-gray-100 dark:border-gray-700/50">
                        <form action="{{ route('admin.tutorials.categories.update', $cat) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nome</label>
                                <input type="text" name="nome" value="{{ $cat->nome }}" required class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Perfil</label>
                                <select name="perfil" required class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                                    <option value="admin" {{ $cat->perfil === 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="admin_loja" {{ $cat->perfil === 'admin_loja' ? 'selected' : '' }}>Admin de Loja</option>
                                    <option value="vendedor" {{ $cat->perfil === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                                    <option value="producao" {{ $cat->perfil === 'producao' ? 'selected' : '' }}>Produção</option>
                                    <option value="caixa" {{ $cat->perfil === 'caixa' ? 'selected' : '' }}>Caixa</option>
                                    <option value="estoque" {{ $cat->perfil === 'estoque' ? 'selected' : '' }}>Estoque</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Ícone</label>
                                <input type="text" name="icone" value="{{ $cat->icone }}" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all">
                                <i class="fa-solid fa-check mr-1"></i> Salvar
                            </button>
                        </form>
                    </div>

                    {{-- Add Video Form --}}
                    <div x-show="addVideo" x-cloak class="p-4 bg-green-50/50 dark:bg-green-500/5 border-b border-gray-100 dark:border-gray-700/50">
                        <form action="{{ route('admin.tutorials.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="tutorial_category_id" value="{{ $cat->id }}">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Título *</label>
                                    <input type="text" name="titulo" required placeholder="Nome do vídeo" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">URL do YouTube *</label>
                                    <input type="text" name="youtube_url" required placeholder="https://youtube.com/watch?v=..." class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Duração</label>
                                    <input type="text" name="duracao" placeholder="5:30" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Capa (URL img)</label>
                                    <input type="url" name="capa_url" placeholder="Opcional, usa thumbnail do YouTube" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Descrição</label>
                                <input type="text" name="descricao" placeholder="Breve descrição do que o vídeo ensina" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition-all">
                                <i class="fa-solid fa-plus mr-1"></i> Adicionar Vídeo
                            </button>
                        </form>
                    </div>

                    {{-- Tutorial Videos List --}}
                    @if($cat->tutorials->count() > 0)
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach($cat->tutorials as $tutorial)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" x-data="{ editVideo: false }">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <img src="{{ $tutorial->thumbnail }}" alt="" class="w-16 h-10 rounded-lg object-cover flex-shrink-0 border border-gray-200 dark:border-gray-700">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $tutorial->titulo }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $tutorial->descricao }}</p>
                                </div>
                                @if($tutorial->duracao)
                                <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md flex-shrink-0">{{ $tutorial->duracao }}</span>
                                @endif
                                @if(!$tutorial->ativo)
                                    <span class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-400/10 px-2 py-0.5 rounded-lg flex-shrink-0">OFF</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0 ml-3">
                                <a href="https://www.youtube.com/watch?v={{ $tutorial->youtube_id }}" target="_blank" class="px-2 py-1 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg transition-all" title="Ver no YouTube">
                                    <i class="fa-brands fa-youtube"></i>
                                </a>
                                <button @click="editVideo = !editVideo" class="px-2 py-1 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-lg transition-all" title="Editar">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                </button>
                                <form action="{{ route('admin.tutorials.toggle', $tutorial) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold rounded-lg transition-all">
                                        <i class="fa-solid {{ $tutorial->ativo ? 'fa-eye-slash' : 'fa-eye' }} text-[10px]"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.tutorials.destroy', $tutorial) }}" method="POST" class="inline" onsubmit="return confirm('Remover este vídeo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg transition-all">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Edit Video Form (below the row) --}}
                            <div x-show="editVideo" x-cloak class="w-full mt-3 p-4 bg-indigo-50/50 dark:bg-indigo-500/5 rounded-xl border border-gray-200 dark:border-gray-700/50" style="flex-basis: 100%;">
                                <form action="{{ route('admin.tutorials.update', $tutorial) }}" method="POST" class="space-y-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Categoria</label>
                                            <select name="tutorial_category_id" required class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 outline-none">
                                                @foreach($categories->flatten() as $allCat)
                                                    <option value="{{ $allCat->id }}" {{ $tutorial->tutorial_category_id == $allCat->id ? 'selected' : '' }}>{{ $allCat->nome }} ({{ $allCat->perfil }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Título</label>
                                            <input type="text" name="titulo" value="{{ $tutorial->titulo }}" required class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">URL YouTube</label>
                                            <input type="text" name="youtube_url" value="https://www.youtube.com/watch?v={{ $tutorial->youtube_id }}" required class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Duração</label>
                                            <input type="text" name="duracao" value="{{ $tutorial->duracao }}" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Capa URL</label>
                                            <input type="url" name="capa_url" value="{{ $tutorial->capa_url }}" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Descrição</label>
                                        <input type="text" name="descricao" value="{{ $tutorial->descricao }}" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:border-indigo-500 outline-none">
                                    </div>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all">
                                        <i class="fa-solid fa-check mr-1"></i> Salvar
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum vídeo nesta categoria.</p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700/50 rounded-2xl p-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma categoria para {{ $perfilLabel }}.</p>
                </div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
@endsection
