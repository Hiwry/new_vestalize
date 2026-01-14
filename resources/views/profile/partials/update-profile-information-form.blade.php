<section>
    <header class="mb-6">
        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-1">
            Informações do Perfil
        </h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Atualize as informações do seu perfil e endereço de e-mail.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                Nome
            </label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none font-medium">
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-bold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                Email
            </label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none font-medium">
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-bold">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-900/50">
                    <p class="text-sm font-bold text-orange-800 dark:text-orange-300">
                        {{ __('Seu endereço de e-mail não foi verificado.') }}
                    </p>
                    <button form="send-verification" class="mt-2 text-sm font-black text-orange-600 dark:text-orange-400 hover:text-orange-800 underline decoration-2 decoration-orange-300 underline-offset-2">
                        {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-3 text-sm font-bold text-green-600 dark:text-green-400">
                            {{ __('Um novo link de verificação foi enviado para o seu endereço de e-mail.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold uppercase tracking-wide shadow-lg shadow-indigo-600/30 hover:shadow-xl hover:shadow-indigo-600/40 hover:-translate-y-1 transition-all active:scale-95">
                Salvar Alterações
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="flex items-center gap-2 text-sm font-bold text-green-600 dark:text-green-400">
                   <i class="fa-solid fa-check-circle"></i> Salvo com sucesso.
                </p>
            @endif
        </div>
    </form>
</section>
