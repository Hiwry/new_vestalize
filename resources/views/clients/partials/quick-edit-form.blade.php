{{-- 
    Formulário de Edição Rápida de Cliente
    Usado no Side Panel para edições sem sair da página
--}}

<form action="{{ route('clients.quick-update', $client->id) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 space-y-4">
        <!-- Avatar e Info Principal -->
        <div class="flex items-center gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div class="h-16 w-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-indigo-600 dark:text-indigo-400 font-bold text-2xl">
                    {{ strtoupper(substr($client->name, 0, 1)) }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Cliente desde</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $client->created_at->format('d/m/Y') }}
                </p>
            </div>
        </div>

        <!-- Nome -->
        <div class="form-group">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nome Completo <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name', $client->name) }}"
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                required
            >
        </div>

        <!-- Telefones -->
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="phone_primary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Telefone Principal <span class="text-red-500">*</span>
                </label>
                <input 
                    type="tel" 
                    name="phone_primary" 
                    id="phone_primary" 
                    value="{{ old('phone_primary', $client->phone_primary) }}"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    required
                >
            </div>
            <div class="form-group">
                <label for="phone_secondary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Telefone Secundário
                </label>
                <input 
                    type="tel" 
                    name="phone_secondary" 
                    id="phone_secondary" 
                    value="{{ old('phone_secondary', $client->phone_secondary) }}"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                >
            </div>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                E-mail
            </label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="{{ old('email', $client->email) }}"
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            >
        </div>

        <!-- CPF/CNPJ e Categoria -->
        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    CPF/CNPJ
                </label>
                <input 
                    type="text" 
                    name="cpf_cnpj" 
                    id="cpf_cnpj" 
                    value="{{ old('cpf_cnpj', $client->cpf_cnpj) }}"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                >
            </div>
            <div class="form-group">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Categoria
                </label>
                <input 
                    type="text" 
                    name="category" 
                    id="category" 
                    value="{{ old('category', $client->category) }}"
                    placeholder="Ex: Cliente VIP, Atacado..."
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                >
            </div>
        </div>
    </div>

    <!-- Estatísticas (Somente Leitura) -->
    <div class="bg-gray-100 dark:bg-gray-800/50 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Estatísticas</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">Total de Pedidos</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $client->orders_count ?? $client->orders()->count() }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Total Gasto</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                    R$ {{ number_format($client->total_spent ?? $client->orders()->sum('total'), 2, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Botões -->
    <div class="flex gap-3 pt-4">
        <button 
            type="button" 
            onclick="window.sidePanel.close()"
            class="flex-1 px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition"
        >
            Cancelar
        </button>
        <button 
            type="submit"
            class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition"
        >
            Salvar Alterações
        </button>
    </div>

    <!-- Link para edição completa -->
    <div class="text-center pt-2">
        <a 
            href="{{ route('clients.edit', $client->id) }}" 
            class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline"
        >
            Abrir edição completa →
        </a>
    </div>
</form>
