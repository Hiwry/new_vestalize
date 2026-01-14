<section>
    <header class="mb-6">
        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-1">
            Atualizar Senha
        </h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Garanta que sua conta está usando uma senha longa e segura.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                Senha Atual
            </label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none font-medium">
            @error('current_password', 'updatePassword')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                Nova Senha
            </label>
            <input id="password" name="password" type="password" autocomplete="new-password"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none font-medium">
            @error('password', 'updatePassword')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                Confirmar Senha
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none font-medium">
            @error('password_confirmation', 'updatePassword')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-3 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 rounded-xl font-bold uppercase tracking-wide hover:bg-gray-800 dark:hover:bg-white/90 hover:-translate-y-1 transition-all active:scale-95 shadow-lg">
                Salvar Senha
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="flex items-center gap-2 text-sm font-bold text-green-600 dark:text-green-400">
                   <i class="fa-solid fa-check-circle"></i> Senha atualizada.
                </p>
            @endif
        </div>
    </form>
</section>
