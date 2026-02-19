<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vestalize | Sistema de Gestão para Confecções</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.025em; }
        .font-serif-heading { font-family: 'Lora', serif; }
        
        .blob-bg {
            background-color: #7c3aed;
            filter: blur(80px);
            opacity: 0.08;
        }

        /* HubSpot Style Gradient Text (Purple Version) */
        .text-hubspot-gradient {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="antialiased bg-hubspot-light text-hubspot-slate overflow-x-hidden">

    <!-- Header -->
    <header class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 transition-all duration-300" id="main-header">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Logo -->
            <a href="#" class="flex items-center gap-2 group">
                <div class="w-9 h-9 rounded bg-hubspot-purple flex items-center justify-center text-white font-bold text-xl shadow-sm group-hover:scale-110 transition-transform duration-300">V</div>
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-10 w-auto">
            </a>
            
            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-10 text-[15px] font-medium text-hubspot-dark/90">
                <a href="#features" class="hover:text-hubspot-purple transition-colors">Funcionalidades</a>
                <a href="#solutions" class="hover:text-hubspot-purple transition-colors">Soluções</a>
                <a href="#testimonials" class="hover:text-hubspot-purple transition-colors">Depoimentos</a>
                <a href="#pricing" class="hover:text-hubspot-purple transition-colors">Planos</a>
            </nav>

            <!-- CTA Buttons -->
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="hidden md:block text-[15px] font-bold text-hubspot-dark hover:text-hubspot-purple transition-colors">Login</a>
                <a href="#vip-form" class="bg-hubspot-purple text-white px-6 py-2.5 rounded hover:bg-hubspot-purple-hover transition-all font-bold text-[15px] shadow-sm transform hover:-translate-y-0.5">
                    Começar Agora
                </a>
            </div>
        </div>
    </header>

    <main class="pt-20">
        <!-- Hero Section -->
        <section class="relative pt-16 pb-24 md:pt-32 md:pb-32 overflow-hidden">
            <!-- Decorative Blobs -->
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/3 w-[800px] h-[800px] blob-bg rounded-full"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-[600px] h-[600px] rounded-full bg-indigo-500/5 blur-[100px]"></div>

            <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center relative z-10">
                
                <!-- Left Content -->
                <div class="space-y-8 animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 border border-purple-100 text-xs font-bold uppercase tracking-wide text-hubspot-purple">
                        <span class="w-2 h-2 rounded-full bg-hubspot-purple"></span>
                        Gestão Inteligente
                    </div>

                    <h1 class="text-5xl md:text-7xl font-bold text-hubspot-dark leading-[1.1] tracking-tight">
                        A plataforma de CRM que sua confecção <span class="text-hubspot-gradient">precisa para crescer</span>.
                    </h1>
                    
                    <p class="text-lg md:text-xl text-hubspot-slate leading-relaxed max-w-lg">
                        Pare de usar planilhas complicadas. O Vestalize organiza seus pedidos, estoque e produção em um único lugar, permitindo que você foque no que importa: vender mais.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 pt-2">
                        <a href="#vip-form" class="inline-flex items-center justify-center bg-hubspot-purple text-white px-8 py-4 rounded font-bold text-lg hover:bg-hubspot-purple-hover transition-all shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Comece Gratuitamente
                        </a>
                        <a href="#features" class="inline-flex items-center justify-center bg-white text-hubspot-dark border-2 border-slate-200 px-8 py-4 rounded font-bold text-lg hover:border-hubspot-purple hover:text-hubspot-purple transition-all">
                            Ver Funcionalidades
                        </a>
                    </div>
                    
                    <div class="pt-8 flex items-center gap-6 text-sm text-hubspot-slate/80 font-medium border-t border-gray-200/60 mt-8 max-w-md">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Sem cartão de crédito
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Instalação em segundos
                        </div>
                    </div>
                </div>

                <!-- Right Image (Form/Abstract) -->
                <div class="relative lg:-mr-12">
                     <div class="relative rounded-2xl bg-white p-3 shadow-2xl border border-gray-200/50 transform rotate-1 hover:rotate-0 transition-transform duration-500 hover:shadow-3xl">
                        <!-- Browser Header Mockup -->
                        <div class="h-8 bg-gray-50 rounded-t-lg border-b border-gray-100 flex items-center px-4 gap-2 mb-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <!-- Hero Image -->
                        <div class="rounded-lg overflow-hidden bg-gray-100 relative aspect-[4/3] flex items-center justify-center group">
                            <!-- Try to load image, fallback to placeholder if not exists -->
                            <img src="{{ asset('images/dashboard-hero.png') }}" alt="Dashboard Vestalize" class="object-cover w-full h-full transform group-hover:scale-105 transition-transform duration-700" onerror="this.onerror=null;this.src='https://placehold.co/800x600/f8fafc/e2e8f0?text=Dashboard+Preview';">
                            
                            <!-- Floating Card 1 -->
                            <div class="absolute -left-6 bottom-12 bg-white p-4 rounded-xl shadow-xl border border-gray-100 animate-float-slow hidden md:block">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-semibold uppercase">Receita Mensal</p>
                                        <p class="text-lg font-bold text-hubspot-dark">R$ 48.250,00</p>
                                    </div>
                                </div>
                            </div>
                         </div>
                     </div>
                </div>
            </div>
        </section>

        <!-- Features Section (Grid) -->
        <section id="features" class="py-24 bg-white relative">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-20">
                    <h2 class="text-3xl md:text-4xl font-bold text-hubspot-dark mb-6">
                        Tudo o que você precisa para <br>
                        <span class="text-hubspot-purple">escalar sua produção</span>
                    </h2>
                    <p class="text-lg text-hubspot-slate">
                        O Vestalize centraliza todas as etapas da sua confecção em uma interface intuitiva, poderosa e fácil de usar.
                    </p>
                </div>

                <!-- Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    @foreach ([
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Gestão de Pedidos', 'desc' => 'Acompanhe cada pedido do orçamento até a entrega. Status em tempo real para toda a equipe.'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Prazos Automáticos', 'desc' => 'Nunca mais perca uma data de entrega. O sistema calcula prazos baseados na sua capacidade produtiva.'],
                        ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Controle Financeiro', 'desc' => 'Saiba exatamente quanto custa cada peça e qual sua margem de lucro real. Fluxo de caixa integrado.'],
                        ['icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'title' => 'Gestão de Estoque', 'desc' => 'Baixa automática de materiais conforme a produção avança. Alertas de estoque mínimo.'],
                        ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Orçamentos em PDF', 'desc' => 'Gere propostas profissionais e envie por WhatsApp com um clique. Aumente sua conversão.'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z', 'title' => 'Acesso Multi-usuário', 'desc' => 'Dê acesso controlado para costureiras, vendedores e gerentes. Cada um vê o que precisa.']
                    ] as $feature)
                        <div class="group bg-hubspot-light hover:bg-white p-8 rounded-xl border border-gray-100 hover:border-purple-100 hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-300">
                            <div class="w-12 h-12 rounded bg-purple-50 text-hubspot-purple flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-hubspot-dark mb-3">{{ $feature['title'] }}</h3>
                            <p class="text-hubspot-slate leading-relaxed">
                                {{ $feature['desc'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Social Proof / Stats -->
        <section class="py-20 bg-hubspot-purple text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
            
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="grid md:grid-cols-4 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-white/10">
                    <div class="p-4">
                        <div class="text-4xl md:text-5xl font-bold text-white mb-2">35%</div>
                        <div class="text-sm font-medium text-gray-300 uppercase tracking-wide">Aumento em Vendas</div>
                    </div>
                    <div class="p-4">
                        <div class="text-4xl md:text-5xl font-bold text-white mb-2">12h</div>
                        <div class="text-sm font-medium text-gray-300 uppercase tracking-wide">Economizadas por Semana</div>
                    </div>
                    <div class="p-4">
                        <div class="text-4xl md:text-5xl font-bold text-white mb-2">100%</div>
                        <div class="text-sm font-medium text-gray-300 uppercase tracking-wide">Controle de Estoque</div>
                    </div>
                    <div class="p-4">
                        <div class="text-4xl md:text-5xl font-bold text-white mb-2">24/7</div>
                        <div class="text-sm font-medium text-gray-300 uppercase tracking-wide">Disponibilidade</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Form / CTA Section -->
        <section id="vip-form" class="py-24 bg-purple-50/50">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-white rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 flex flex-col md:flex-row gap-12 items-center">
                    
                    <div class="md:w-1/2 space-y-6">
                        <h2 class="text-3xl font-bold text-hubspot-dark">Garanta sua vaga no Lote 1</h2>
                        <p class="text-hubspot-slate">
                            Estamos liberando acessos gradualmente. Cadastre-se agora para garantir condições especiais de lançamento e acesso prioritário.
                        </p>
                        <ul class="space-y-4 pt-4">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-sm font-medium text-hubspot-dark">30 dias de teste grátis</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-sm font-medium text-hubspot-dark">Onboarding guiado</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-sm font-medium text-hubspot-dark">Migração de dados inclusa</span>
                            </li>
                        </ul>
                    </div>

                    <div class="md:w-1/2 w-full">
                        <form id="lead-form" class="space-y-4">
                             <div>
                                <label class="block text-sm font-bold text-hubspot-dark mb-1">Nome Completo</label>
                                <input type="text" name="name" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-hubspot-dark focus:ring-2 focus:ring-hubspot-purple focus:border-hubspot-purple transition-all placeholder-gray-400" placeholder="Ex: João Silva" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-hubspot-dark mb-1">Email Corporativo</label>
                                <input type="email" name="email" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-hubspot-dark focus:ring-2 focus:ring-hubspot-purple focus:border-hubspot-purple transition-all placeholder-gray-400" placeholder="Ex: contato@suaconfeccao.com" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-hubspot-dark mb-1">Telefone / WhatsApp</label>
                                <input type="tel" name="phone" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-hubspot-dark focus:ring-2 focus:ring-hubspot-purple focus:border-hubspot-purple transition-all placeholder-gray-400" placeholder="(00) 00000-0000">
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="w-full bg-hubspot-purple text-white py-3.5 rounded-lg font-bold text-lg shadow hover:bg-hubspot-purple-hover transition-all transform hover:scale-[1.02]">
                                    Entrar na Lista VIP
                                </button>
                            </div>
                            <p class="text-xs text-center text-gray-500">
                                Ao se cadastrar, você concorda com nossos Termos de Uso.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white text-hubspot-slate border-t border-gray-100 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div class="space-y-6 col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2">
                         <div class="w-8 h-8 rounded bg-hubspot-purple flex items-center justify-center text-white font-bold text-lg">V</div>
                        <span class="font-bold text-2xl tracking-tight text-hubspot-dark">Vestalize</span>
                    </div>
                    <p class="text-hubspot-slate text-sm leading-relaxed">
                        A plataforma completa para gestão de confecções que une vendas, produção e financeiro.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="text-gray-400 hover:text-hubspot-purple transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                        <a href="#" class="text-gray-400 hover:text-hubspot-purple transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.072 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                        <a href="#" class="text-gray-400 hover:text-hubspot-purple transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.072 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.072 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                    </div>
                </div>

                <!-- Links Column -->
                <div>
                    <h4 class="font-bold text-lg mb-6 text-hubspot-dark">Plataforma</h4>
                    <ul class="space-y-4 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-hubspot-purple transition-colors">Funcionalidades</a></li>
                        <li><a href="#" class="hover:text-hubspot-purple transition-colors">Para Confecções</a></li>
                        <li><a href="#" class="hover:text-hubspot-purple transition-colors">Integrações</a></li>
                        <li><a href="#" class="hover:text-hubspot-purple transition-colors">Preços</a></li>
                    </ul>
                </div>

                <!-- Links Column -->
                <div>
                    <h4 class="font-bold text-lg mb-6 text-hubspot-dark">Empresa</h4>
                    <ul class="space-y-4 text-sm text-gray-500">
                         <li><a href="#" class="hover:text-hubspot-purple transition-colors">Sobre Nós</a></li>
                        <li><a href="#" class="hover:text-hubspot-purple transition-colors">Carreiras</a></li>
                        <li><a href="#" class="hover:text-hubspot-purple transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-hubspot-purple transition-colors">Contato</a></li>
                    </ul>
                </div>
                
                 <!-- Contact Column -->
                 <div>
                    <h4 class="font-bold text-lg mb-6 text-hubspot-dark">Fale Conosco</h4>
                    <ul class="space-y-4 text-sm text-gray-500">
                         <li class="flex items-center gap-2"><svg class="w-4 h-4 text-hubspot-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> suporte@vestalize.com</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-hubspot-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> (11) 99999-9999</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Vestalize Tecnologia. Todos os direitos reservados.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-hubspot-purple transition-colors">Privacidade</a>
                    <a href="#" class="hover:text-hubspot-purple transition-colors">Termos de Uso</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts & Animations -->
    <script>
        // Form Handling
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = 'Enviando...';
                
                // Simulate submission
                setTimeout(() => {
                    alert('Cadastro realizado com sucesso! Em breve entraremos em contato.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    form.reset();
                }, 1500);
            });
        });

        // Sticky Header Effect
        const header = document.getElementById('main-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('shadow-md', 'bg-white/95');
                header.classList.remove('bg-white/90');
            } else {
                 header.classList.remove('shadow-md', 'bg-white/95');
                 header.classList.add('bg-white/90');
            }
        });
    </script>
</body>
</html>
