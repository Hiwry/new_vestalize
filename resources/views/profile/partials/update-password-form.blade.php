<section>
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
            Atualizar Senha
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Garanta que sua conta está usando uma senha longa e segura.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Senha Atual
            </label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            @error('current_password', 'updatePassword')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Nova Senha
            </label>
            <input id="password" name="password" type="password" autocomplete="new-password"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            @error('password', 'updatePassword')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Confirmar Senha
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            @error('password_confirmation', 'updatePassword')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2.5 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 rounded-lg font-medium hover:bg-gray-800 dark:hover:bg-white/90 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900" style="color: white !important;">
                Salvar Senha
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="flex items-center gap-2 text-sm font-medium text-green-600 dark:text-green-400">
                   <i class="fa-solid fa-check-circle"></i> Senha atualizada.
                </p>
            @endif
        </div>
    </form>
</section>
