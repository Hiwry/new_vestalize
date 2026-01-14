<section class="space-y-6">
    <header>
        <h2 class="text-xl font-black text-red-600 dark:text-red-400 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> Deletar Conta
        </h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Uma vez deletada, todos os seus dados serão permanentemente removidos.
        </p>
    </header>

    <button x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-3 bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/30 rounded-xl font-bold uppercase tracking-wide hover:bg-red-100 dark:hover:bg-red-900/20 hover:shadow-lg hover:shadow-red-500/10 transition-all active:scale-95">
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
              class="relative bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl max-w-lg w-full p-6 sm:p-8 border border-gray-100 dark:border-slate-700 overflow-hidden">
            
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2">
                    Confirmar Exclusão
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Você tem certeza que deseja deletar sua conta? Por favor, digite sua senha para confirmar que deseja excluir permanentemente seus dados.
                </p>

                <div class="mb-6">
                    <label for="password" class="sr-only">Senha</label>
                    <input id="password" name="password" type="password" placeholder="Sua senha"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all outline-none font-medium text-lg placeholder:text-gray-400">
                    @error('password', 'userDeletion')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal')" class="px-5 py-2.5 rounded-xl font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold uppercase tracking-wide hover:bg-red-700 shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 transition-all active:scale-95">
                        Confirmar Exclusão
                    </button>
                </div>
            </form>
         </div>
    </div>
</section>
