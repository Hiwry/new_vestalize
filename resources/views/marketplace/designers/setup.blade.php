@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-3xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-primary/10 text-primary rounded-[2rem] mb-6 shadow-xl shadow-primary/5">
                <i class="fa-solid fa-wand-magic-sparkles text-3xl"></i>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-4">Torne-se um Designer</h1>
            <p class="text-lg text-gray-500 font-medium max-w-lg mx-auto">Compartilhe seu talento com milhares de usuários da Vestalize e comece a lucrar com seus designs.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700 relative overflow-hidden">
            <!-- Progress indicator (Step 1 of 1 for now) -->
            <div class="flex justify-center gap-2 mb-12">
                <div class="w-12 h-1.5 bg-primary rounded-full"></div>
            </div>

            <form action="{{ route('marketplace.designers.setup.store') }}" method="POST">
                @csrf
                
                <div class="space-y-8">
                    <!-- Display Name -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Como você quer ser chamado?</label>
                        <input type="text" name="display_name" value="{{ old('display_name', Auth::user()->name) }}" required 
                               class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner placeholder-gray-400"
                               placeholder="Ex: Studio Art Design">
                        @error('display_name') <p class="mt-2 text-xs text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Specialties -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Suas Especialidades</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($specialties as $value => $label)
                                <label class="relative flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl cursor-pointer border-2 border-transparent hover:border-primary/30 transition-all peer-checked:border-primary peer-checked:bg-primary/5 has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                    <input type="checkbox" name="specialties[]" value="{{ $value }}" class="sr-only peer">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500 peer-checked:text-primary">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('specialties') <p class="mt-2 text-xs text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Sua Bio / Experiência</label>
                        <textarea name="bio" rows="5" required 
                                  class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-[2rem] px-6 py-5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner placeholder-gray-400 leading-relaxed font-bold"
                                  placeholder="Conte um pouco sobre sua trajetória, seu estilo e o que você domina..."></textarea>
                        @error('bio') <p class="mt-2 text-xs text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Terms -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-gray-100 dark:border-gray-700">
                        <label class="flex items-start gap-4 cursor-pointer group">
                            <input type="checkbox" required class="mt-1 w-5 h-5 rounded-md border-gray-300 text-primary focus:ring-primary">
                            <span class="text-xs text-gray-500 font-medium leading-relaxed group-hover:text-gray-700 transition-colors">
                                Ao me cadastrar como designer, eu concordo com os <a href="#" class="text-primary font-black underline">Termos do Marketplace</a> e entendo que a Vestalize retém uma comissão de 15% sobre cada venda para manutenção da plataforma.
                            </span>
                        </label>
                    </div>

                    <div class="pt-8">
                        <button type="submit" class="w-full py-6 bg-primary hover:bg-primary-hover text-white rounded-[1.5rem] font-black text-sm uppercase tracking-widest shadow-2xl shadow-primary/20 transition-all active:scale-95">
                            Finalizar Cadastro <i class="fa-solid fa-arrow-right ml-3"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <p class="mt-12 text-center text-[10px] font-black uppercase tracking-widest text-gray-400">
            Precisa de ajuda? <a href="#" class="text-primary hover:underline">Fale com nosso suporte</a>
        </p>

    </div>
</div>
@endsection
