<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vestalize | Sistema de Gestão para Confecções</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-header {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
        }
        .text-gradient {
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-glow {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(15, 23, 42, 0) 70%);
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body class="antialiased bg-[#0f172a] text-slate-300 selection:bg-sky-500/30 selection:text-sky-200">
    
    <!-- Background Glow -->
    <div class="hero-glow"></div>

    <!-- Header -->
    <header class="fixed top-0 w-full z-50 glass-header border-b border-slate-800/60">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-sky-500/20">V</div>
                <span class="font-bold text-xl text-white tracking-tight">Vestalize</span>
            </div>
            
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#hero" class="text-slate-400 hover:text-white transition-colors">Início</a>
                <a href="#features" class="text-slate-400 hover:text-white transition-colors">Recursos</a>
                <a href="#solutions" class="text-slate-400 hover:text-white transition-colors">Soluções</a>
                <a href="#launch" class="text-slate-400 hover:text-white transition-colors">Lançamento</a>
            </nav>

            <div class="flex items-center gap-4">
                <a href="<?php echo e(route('login')); ?>" class="hidden md:block text-sm font-medium text-slate-300 hover:text-white transition-colors">Entrar</a>
                <a href="#vip-form" class="bg-white text-slate-900 hover:bg-sky-50 px-5 py-2.5 rounded-full text-sm font-bold transition-all transform hover:scale-105 shadow-[0_0_20px_rgba(255,255,255,0.15)]">
                    Entrar na Lista VIP
                </a>
            </div>
        </div>
    </header>

    <main class="pt-24">
        <!-- Hero Section -->
        <section id="hero" class="py-20 lg:py-32 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8 relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/50 border border-slate-700/50 text-xs font-medium text-sky-400">
                        <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
                        Sistema Especializado para Confecções
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-bold text-white tracking-tight leading-[1.1]">
                        Pare de se perder em <span class="text-gradient">pedidos</span> e atrasar a <span class="text-gradient">produção</span>.
                    </h1>
                    
                    <p class="text-lg text-slate-400 leading-relaxed max-w-xl">
                        O Vestalize é o sistema feito exclusivamente para confecções que querem organizar pedidos, produção e financeiro em um só lugar. Chega de planilhas e papel.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#vip-form" class="bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg shadow-sky-500/25 transition-all transform hover:-translate-y-1 text-center">
                            Garantir Acesso VIP
                        </a>
                        <a href="#pain-points" class="glass text-white hover:bg-slate-800/50 px-8 py-4 rounded-xl font-bold text-lg transition-all text-center">
                            Ver como funciona
                        </a>
                    </div>
                    
                    <div class="pt-8 flex items-center gap-6 text-sm text-slate-500 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Kanban Visual
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Financeiro Integrado
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-sky-500/20 to-indigo-500/20 blur-3xl rounded-full"></div>
                    <div class="glass p-8 rounded-3xl relative z-10 space-y-6">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h3 class="text-white font-bold text-xl">Lista de Espera VIP</h3>
                                <p class="text-sky-400 text-sm">Lote 1: Condição Exclusiva de Lançamento</p>
                            </div>
                            <div class="bg-sky-500/10 text-sky-400 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider border border-sky-500/20">Vagas Limitadas</div>
                        </div>

                        <form id="vip-form" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1.5">Nome completo</label>
                                <input type="text" name="name" placeholder="Seu nome" class="w-full bg-slate-800/50 border border-slate-700/50 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-all" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1.5">Email principal</label>
                                <input type="email" name="email" placeholder="seu@email.com" class="w-full bg-slate-800/50 border border-slate-700/50 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-all" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1.5">WhatsApp</label>
                                <input type="tel" name="phone" placeholder="(00) 00000-0000" class="w-full bg-slate-800/50 border border-slate-700/50 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-all">
                            </div>
                            <button type="submit" class="w-full bg-white text-slate-900 hover:bg-sky-50 py-4 rounded-xl font-bold text-lg transition-all shadow-lg transform hover:scale-[1.02]">
                                Cadastrar Gratuitamente
                            </button>
                            <p class="text-xs text-center text-slate-500">Seus dados estão seguros. Usaremos apenas para o lançamento.</p>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pain Points Section -->
        <section id="pain-points" class="py-24 bg-slate-900/50 border-y border-slate-800/50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <h2 class="text-3xl md:text-4xl font-bold text-white">Se isso acontece na sua confecção, <span class="text-sky-400">o problema não é você</span>.</h2>
                    <p class="text-slate-400 text-lg">O processo produtivo é complexo, mas tentar gerenciar tudo de cabeça só gera prejuízo.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php $__currentLoopData = [
                        ['Pedido perdido', 'Mensagens de WhatsApp que somem e pedidos anotados em papel que você nunca mais encontra.'],
                        ['Prazos estourados', 'Produção parada por falta de material ou peças urgentes que furam a fila sem controle.'],
                        ['Financeiro cego', 'Não saber exatamente quanto lucrou em cada pedido ou se o cliente realmente pagou.'],
                        ['Retrabalho', 'Peças voltando da costura com erro porque a ficha técnica não estava clara.'],
                        ['Estoque confuso', 'Comprar tecido duplicado ou faltar botão na hora de finalizar o pedido.'],
                        ['Desorganização', 'A sensação de trabalhar muito e não ver o dinheiro sobrar no final do mês.']
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="glass p-6 rounded-2xl hover:bg-slate-800/50 transition-colors">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2"><?php echo e($title); ?></h3>
                            <p class="text-slate-400 leading-relaxed"><?php echo e($desc); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

        <!-- Solutions Section -->
        <section id="solutions" class="py-24 relative">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900 to-[#0f172a] opacity-50 pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="order-2 lg:order-1 space-y-8">
                        <div>
                            <span class="text-sky-400 font-bold tracking-wider uppercase text-sm">A Solução Vestalize</span>
                            <h2 class="text-3xl md:text-5xl font-bold text-white mt-2 mb-6">Organização não é burocracia. É <span class="text-gradient">lucro no bolso</span>.</h2>
                            <p class="text-slate-400 text-lg leading-relaxed">
                                Transformamos o caos da produção em um fluxo simples e visual. Você vê exatamente onde cada peça está.
                            </p>
                        </div>

                        <ul class="space-y-5">
                            <?php $__currentLoopData = [
                                'Kanban de Produção: Arraste e solte seus pedidos entre etapas.',
                                'Orçamentos Automáticos: Gere orçamentos profissionais em segundos.',
                                'Multi-tenant: Seus dados isolados e seguros.',
                                'Relatórios Reais: Saiba exatamente seu lucro no mês.'
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-start gap-3">
                                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="text-slate-300 font-medium"><?php echo e($item); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>

                    <div class="order-1 lg:order-2 relative">
                        <!-- Abstract UI Representation -->
                        <div class="glass rounded-2xl p-6 border border-slate-700 shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-500">
                            <div class="flex items-center gap-4 mb-6 border-b border-slate-700/50 pb-4">
                                <div class="flex gap-2">
                                    <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-500/50"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-500/50"></div>
                                </div>
                                <div class="h-2 w-24 bg-slate-700 rounded-full ml-auto"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex gap-4">
                                    <div class="w-1/3 h-32 rounded-xl bg-slate-800/80 p-3 space-y-2 border border-slate-700/30">
                                        <div class="w-16 h-2 bg-sky-500/30 rounded-full mb-4"></div>
                                        <div class="h-16 bg-slate-700/30 rounded-lg"></div>
                                    </div>
                                    <div class="w-1/3 h-32 rounded-xl bg-slate-800/80 p-3 space-y-2 border border-slate-700/30">
                                        <div class="w-20 h-2 bg-indigo-500/30 rounded-full mb-4"></div>
                                        <div class="h-12 bg-slate-700/30 rounded-lg border-l-2 border-indigo-500"></div>
                                        <div class="h-8 bg-slate-700/30 rounded-lg"></div>
                                    </div>
                                    <div class="w-1/3 h-32 rounded-xl bg-slate-800/80 p-3 space-y-2 border border-slate-700/30">
                                        <div class="w-12 h-2 bg-emerald-500/30 rounded-full mb-4"></div>
                                        <div class="h-16 bg-slate-700/30 rounded-lg border-l-2 border-emerald-500"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24">
            <div class="max-w-4xl mx-auto px-6">
                <div class="glass rounded-3xl p-12 text-center relative overflow-hidden border border-sky-500/30">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-sky-500 via-indigo-500 to-sky-500"></div>
                    <div class="relative z-10 space-y-8">
                        <h2 class="text-4xl md:text-5xl font-bold text-white">Pronto para organizar sua confecção?</h2>
                        <p class="text-xl text-slate-400 max-w-2xl mx-auto">
                            O lançamento oficial é dia <span class="text-white font-bold">15/01/26</span>. Entre na lista VIP para destravar condições especiais.
                        </p>
                        <div class="flex justify-center">
                            <a href="#vip-form" class="bg-white text-slate-900 hover:bg-sky-50 px-10 py-5 rounded-xl font-bold text-xl shadow-lg transition-all transform hover:scale-105">
                                Quero meu acesso VIP 🚀
                            </a>
                        </div>
                        <p class="text-sm text-slate-500">Sem cartão de crédito necessário.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-12 border-t border-slate-800 mt-12 bg-[#0b1120]">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-white font-bold text-lg border border-slate-700">V</div>
                <span class="text-slate-300 font-semibold">Vestalize</span>
            </div>
            <p class="text-slate-500 text-sm">© <?php echo e(date('Y')); ?> Vestalize Tecnologia para Confecções. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        document.querySelectorAll('form#vip-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="animate-pulse">Salvando...</span>';

                try {
                    const formData = new FormData(this);
                    // Add CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    const response = await fetch("<?php echo e(route('leads.store')); ?>", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>",
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Success state
                        form.innerHTML = `
                            <div class="text-center py-8 space-y-4">
                                <div class="w-16 h-16 bg-green-500/20 text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="text-2xl font-bold text-white">Parabéns! 🚀</h3>
                                <p class="text-slate-300">Você já está na lista VIP.<br>Verifique seu e-mail para confirmar a inscrição.</p>
                            </div>
                        `;
                    } else {
                        // Error state
                        alert('Erro: ' + (data.message || 'Verifique os dados e tente novamente.'));
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro de conexão. Tente novamente.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/welcome.blade.php ENDPATH**/ ?>