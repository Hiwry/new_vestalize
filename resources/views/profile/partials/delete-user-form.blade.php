<section class="space-y-6">
    <header>
        <h2 class="text-xl font-bold text-red-600 dark:text-red-400 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> Deletar Conta
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Uma vez deletada, todos os seus dados serão permanentemente removidos.
        </p>
    </header>

    <button x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="text-red-600 dark:text-red-400 font-medium hover:text-red-800 dark:hover:text-red-300 hover:underline transition-colors focus:outline-none">
        Deletar Conta
    </button>

    <div x-data="{ show: false }" 
         x-on:open-modal.window="if ($event.detail === 'confirm-user-deletion') show = true"
         x-on:close-modal.window="show = false"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

         <!-- Backdrop -->
         <div x-show="show" x-transition.opacity class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="show = false"></div>

         <!-- Modal Content -->
         <div x-show="show" 
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
              class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-lg w-full p-6 sm:p-8 border border-gray-100 dark:border-slate-700 overflow-hidden">
            
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    Confirmar Exclusão
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Você tem certeza que deseja deletar sua conta? Por favor, digite sua senha para confirmar que deseja excluir permanentemente seus dados.
                </p>

                <div class="mb-6">
                    <label for="password" class="sr-only">Senha</label>
                    <input id="password" name="password" type="password" placeholder="Sua senha"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors placeholder:text-gray-400">
                    @error('password', 'userDeletion')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal')" class="px-4 py-2 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors shadow-sm">
                        Confirmar Exclusão
                    </button>
                </div>
            </form>
         </div>
    </div>
</section>
