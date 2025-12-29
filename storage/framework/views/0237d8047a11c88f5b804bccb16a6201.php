<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vestalize | Landing de Lançamento</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        :root {
            --bg: #f8fafc;
            --ink: #0f172a;
            --muted: #475569;
            --line: #e2e8f0;
            --panel: #ffffff;
            --accent: #111827;
            --pill: #0ea5e9;
        }
        body {
            font-family: 'Space Grotesk', 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(circle at 20% 20%, #eef2ff 0, transparent 28%), radial-gradient(circle at 80% 0%, #e0f2fe 0, transparent 25%), var(--bg);
            color: var(--ink);
        }
        .container { max-width: 1120px; margin: 0 auto; padding: 0 1.5rem; }
        .section { padding: 4.5rem 0; }
        .card { background: var(--panel); border: 1px solid var(--line); border-radius: 18px; box-shadow: 0 18px 45px -28px rgba(15,23,42,0.35); }
        .pill { display: inline-flex; align-items: center; gap: .45rem; padding: .55rem 1rem; border-radius: 999px; background: #0ea5e910; color: #0284c7; font-weight: 600; letter-spacing: .02em; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; padding: .95rem 1.4rem; border-radius: 14px; font-weight: 700; transition: transform .15s ease, box-shadow .25s ease, background .2s ease, color .2s ease; }
        .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 16px 40px -24px rgba(0,0,0,0.5); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 20px 45px -22px rgba(0,0,0,0.6); }
        .btn-ghost { background: #0f172a0a; color: var(--ink); border: 1px solid var(--line); }
        .lead-list { display: grid; gap: .75rem; }
        .lead-item { display: flex; align-items: flex-start; gap: .6rem; }
        .lead-dot { width: 12px; height: 12px; border-radius: 999px; background: #0ea5e9; margin-top: .3rem; }
        .badge { font-size: .9rem; color: var(--muted); }
        .grid-2 { display: grid; gap: 1.2rem; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }
        .grid-3 { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); }
        .headline { font-size: clamp(2.4rem, 3.8vw, 3.4rem); line-height: 1.1; letter-spacing: -.02em; }
        .sub { color: var(--muted); font-size: 1.05rem; line-height: 1.6; }
        .section-title { font-size: 2rem; font-weight: 700; letter-spacing: -.01em; }
        .muted { color: var(--muted); }
        .divider { height: 1px; background: var(--line); margin: 3rem 0; }
    </style>
</head>
<body class="antialiased">
    <header class="sticky top-0 z-30 backdrop-blur-xl bg-white/70 border-b border-[var(--line)]">
        <div class="container h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-black text-white flex items-center justify-center font-bold text-lg">V</div>
                <span class="font-semibold text-lg tracking-tight">Vestalize</span>
            </div>
            <nav class="hidden md:flex items-center gap-6 text-sm text-[var(--muted)] font-semibold">
                <a href="#hero" class="hover:text-black">Hero</a>
                <a href="#dor" class="hover:text-black">Dores reais</a>
                <a href="#virada" class="hover:text-black">Virada</a>
                <a href="#vestalize" class="hover:text-black">O sistema</a>
                <a href="#lancamento" class="hover:text-black">Lançamento</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="#vip-form" class="btn btn-primary text-sm px-4 py-2">Entrar na Lista VIP</a>
            </div>
        </div>
    </header>

    <main>
        <!-- HERO -->
        <section id="hero" class="section">
            <div class="container grid lg:grid-cols-[1.15fr_0.85fr] gap-10 items-center">
                <div class="space-y-6">
                    <div class="pill">
                        <span>🧩 Lançamento Vestalize</span>
                        <span class="badge">Sistema para confecções</span>
                    </div>
                    <div class="space-y-4">
                        <h1 class="headline">Pare de se perder em pedidos e atrasar a produção.</h1>
                        <p class="sub">O Vestalize é o sistema feito exclusivamente para confecções que querem organizar pedidos, produção e financeiro em um só lugar.</p>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <?php $__currentLoopData = [
                            'Pedidos organizados',
                            'Produção visível por etapas',
                            'Menos erro, menos atraso',
                            'Mais controle e previsibilidade'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="card px-4 py-3 flex items-center gap-3 border-dashed">
                                <div class="w-9 h-9 rounded-full bg-black text-white flex items-center justify-center text-lg">✓</div>
                                <p class="font-semibold"><?php echo e($item); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="space-y-2">
                        <a href="#vip-form" class="btn btn-primary text-base">👉 Entrar na Lista VIP do Lançamento</a>
                        <p class="text-sm muted">Lançamento oficial em 15/01/26 · Condição especial para os primeiros clientes</p>
                    </div>
                </div>
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)] font-bold">Lista VIP</p>
                            <p class="font-semibold text-lg">Garanta seu acesso antecipado</p>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full bg-black text-white font-semibold">Lote 1</span>
                    </div>
                    <form id="vip-form" class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-[var(--muted)]">Nome</label>
                            <input type="text" name="name" placeholder="Seu nome" class="mt-1 w-full rounded-xl border border-[var(--line)] bg-[#f8fafc] px-4 py-3 focus:outline-none focus:border-black" required>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-[var(--muted)]">E-mail</label>
                            <input type="email" name="email" placeholder="voce@exemplo.com" class="mt-1 w-full rounded-xl border border-[var(--line)] bg-[#f8fafc] px-4 py-3 focus:outline-none focus:border-black" required>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-[var(--muted)]">WhatsApp</label>
                            <input type="tel" name="phone" placeholder="(00) 00000-0000" class="mt-1 w-full rounded-xl border border-[var(--line)] bg-[#f8fafc] px-4 py-3 focus:outline-none focus:border-black">
                        </div>
                        <button type="submit" class="btn btn-primary w-full justify-center">Quero entrar na Lista VIP</button>
                        <p class="text-xs muted text-center">Sem compromisso. Usaremos seu contato apenas para o lançamento.</p>
                    </form>
                </div>
            </div>
        </section>

        <section id="dor" class="section">
            <div class="container space-y-6">
                <div class="space-y-2">
                    <p class="pill w-fit">Dor real da confecção</p>
                    <h2 class="section-title">Se isso acontece na sua confecção, o problema não é você.</h2>
                </div>
                <div class="grid-2">
                    <?php $__currentLoopData = [
                        'Pedido perdido no WhatsApp',
                        'Informação espalhada em papel, planilha e conversa',
                        'Produção sem saber prioridade',
                        'Prazo estourando mesmo trabalhando muito',
                        'Financeiro sempre no escuro',
                        'A maioria das confecções cresce no improviso. Até o improviso virar prejuízo.'
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card p-5">
                            <div class="lead-item">
                                <div class="lead-dot"></div>
                                <p class="font-semibold leading-relaxed"><?php echo e($item); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

        <section id="virada" class="section">
            <div class="container grid lg:grid-cols-[0.95fr_1.05fr] gap-10 items-center">
                <div class="space-y-3">
                    <p class="pill w-fit">Virada de chave</p>
                    <h2 class="section-title">Organização não é burocracia. É lucro escondido.</h2>
                    <p class="sub">Quando você tem controle:</p>
                    <div class="lead-list">
                        <?php $__currentLoopData = [
                            'O pedido entra certo',
                            'A produção flui',
                            'O atraso diminui',
                            'O financeiro para de ser surpresa'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="lead-item">
                                <div class="w-9 h-9 rounded-full bg-[#0ea5e9] text-white flex items-center justify-center font-bold text-lg">✓</div>
                                <p class="font-semibold"><?php echo e($item); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p class="sub">O Vestalize nasceu para organizar o caos real da confecção, não para ser mais um sistema genérico.</p>
                </div>
                <div class="card p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-[var(--muted)]">Fluxo proposto</p>
                        <span class="px-3 py-1 rounded-full bg-black text-white text-xs font-bold">Do pedido ao financeiro</span>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <?php $__currentLoopData = [
                            ['Recepção de pedido', 'Briefing completo, sem ruído.'],
                            ['Kanban de produção', 'Cada etapa visível, zero gargalo.'],
                            ['Qualidade', 'Checklist por peça, sem retrabalho.'],
                            ['Financeiro', 'Entradas, saídas e previsões claras.']
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl border border-[var(--line)] bg-[#f8fafc] px-4 py-3">
                                <p class="font-semibold"><?php echo e($title); ?></p>
                                <p class="text-sm muted mt-1"><?php echo e($desc); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="vestalize" class="section">
            <div class="container space-y-10">
                <div class="space-y-3">
                    <p class="pill w-fit">O que é o Vestalize</p>
                    <h2 class="section-title">O sistema que conecta tudo.</h2>
                    <p class="sub">Tudo integrado. Tudo visível. Tudo simples.</p>
                </div>
                <div class="grid-2">
                    <?php $__currentLoopData = [
                        ['Pedidos', 'Todas as informações em um só lugar, sem depender de conversa perdida.'],
                        ['Orçamentos', 'Clareza no valor, no prazo e no que foi combinado.'],
                        ['Produção (Kanban)', 'Visualize cada pedido por etapa e evite gargalos.'],
                        ['Financeiro', 'Saiba o que entra, o que sai e o que está pendente.']
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card p-6 space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-[#0ea5e9]/15 text-[#0ea5e9] flex items-center justify-center font-bold text-lg"><?php echo e(substr($title, 0, 1)); ?></div>
                            <p class="text-xl font-semibold"><?php echo e($title); ?></p>
                            <p class="muted"><?php echo e($desc); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container grid lg:grid-cols-[1fr_0.9fr] gap-8 items-center">
                <div class="space-y-3">
                    <p class="pill w-fit">Diferencial</p>
                    <h2 class="section-title">Não é um sistema genérico. É feito para confecção.</h2>
                    <p class="sub">Sem excesso de funções inúteis. Sem complicação.</p>
                </div>
                <div class="grid-2">
                    <?php $__currentLoopData = [
                        'Fardamentos',
                        'Uniformes escolares',
                        'Produções sob medida',
                        'Pequenas e médias confecções'
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card px-4 py-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center font-bold">★</div>
                            <p class="font-semibold"><?php echo e($item); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

        <section class="section bg-[#f1f5f9]">
            <div class="container grid lg:grid-cols-2 gap-8">
                <div class="card p-6 space-y-3">
                    <p class="pill w-fit">Para quem é</p>
                    <h3 class="text-xl font-bold">✔ É para você se:</h3>
                    <div class="lead-list">
                        <?php $__currentLoopData = [
                            'Tem confecção e quer crescer com controle',
                            'Já se perdeu em pedidos ou prazos',
                            'Quer parar de apagar incêndio',
                            'Quer enxergar a produção de verdade'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="lead-item">
                                <div class="w-8 h-8 rounded-full bg-[#0ea5e9] text-white flex items-center justify-center font-bold">✓</div>
                                <p class="font-semibold"><?php echo e($item); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="card p-6 space-y-3">
                    <p class="pill w-fit">Para quem não é</p>
                    <h3 class="text-xl font-bold">❌ Não é para você se:</h3>
                    <div class="lead-list">
                        <?php $__currentLoopData = [
                            'Gosta de trabalhar no improviso',
                            'Não quer mudar processos',
                            'Acha que bagunça faz parte do negócio'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="lead-item">
                                <div class="w-8 h-8 rounded-full bg-[#ef4444] text-white flex items-center justify-center font-bold">×</div>
                                <p class="font-semibold"><?php echo e($item); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="lancamento" class="section">
            <div class="container grid lg:grid-cols-[1.1fr_0.9fr] gap-8 items-center">
                <div class="space-y-3">
                    <p class="pill w-fit">Lançamento</p>
                    <h2 class="section-title">Lançamento oficial — 15/01/26</h2>
                    <p class="sub">As primeiras confecções vão ajudar a moldar o Vestalize.</p>
                </div>
                <div class="card p-6 space-y-3">
                    <h3 class="text-lg font-bold">Quem entrar no lançamento:</h3>
                    <div class="lead-list">
                        <?php $__currentLoopData = [
                            '✔ Acesso antecipado',
                            '✔ Condição especial',
                            '✔ Acompanhamento inicial',
                            '✔ Sem reajuste para os primeiros clientes'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="lead-item">
                                <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center font-bold">✓</div>
                                <p class="font-semibold"><?php echo e($item); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container card p-8 text-center space-y-4">
                <h2 class="section-title">Organize hoje o que hoje te dá dor de cabeça.</h2>
                <p class="sub max-w-2xl mx-auto">Sem compromisso. Vagas limitadas no lançamento.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="#vip-form" class="btn btn-primary text-base">🚀 Quero entrar na Lista VIP</a>
                    <a href="#virada" class="btn btn-ghost">Ver como o Vestalize ajuda</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="px-6 py-8 border-t border-[var(--line)] bg-white/80">
        <div class="container flex flex-col md:flex-row items-start md:items-center justify-between gap-3 text-sm muted">
            <div class="flex items-center gap-2 text-black">
                <span class="w-9 h-9 rounded-xl bg-black text-white font-bold flex items-center justify-center">V</span>
                <span class="font-semibold">Vestalize</span>
            </div>
            <p>Vestalize — Sistema de gestão para confecções</p>
            <p>Criado por quem entende o problema real da produção</p>
        </div>
    </footer>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/welcome.blade.php ENDPATH**/ ?>