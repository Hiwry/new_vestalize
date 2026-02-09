<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vestalize | Sistema de Gestão para Confecções e Malharias</title>
    <meta name="description" content="O sistema perfeito para sua confecção. Organize sua produção, crie orçamentos em segundos e veja os lucros crescerem.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .text-gradient {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%237c3aed' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .mockup-shadow {
            box-shadow: 0 25px 50px -12px rgba(124, 58, 237, 0.25), 0 0 0 1px rgba(124, 58, 237, 0.1);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(124, 58, 237, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -10px rgba(124, 58, 237, 0.5);
        }
        
        .feature-icon {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        }
        
        .testimonial-quote {
            color: #7c3aed;
            font-size: 4rem;
            line-height: 1;
            opacity: 0.2;
        }
        
        .faq-item {
            border-bottom: 1px solid #e5e7eb;
        }
        .faq-item:last-child {
            border-bottom: none;
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">
    
    <!-- Header -->
    <header class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center">
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-10 w-auto">
            </div>
            
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#hero" class="text-gray-600 hover:text-violet-600 transition-colors">Início</a>
                <a href="#features" class="text-gray-600 hover:text-violet-600 transition-colors">Funcionalidades</a>
                <a href="#pricing" class="text-gray-600 hover:text-violet-600 transition-colors">Preços</a>
                <a href="#faq" class="text-gray-600 hover:text-violet-600 transition-colors">FAQ</a>
            </nav>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="hidden md:block text-sm font-medium text-gray-600 hover:text-violet-600 transition-colors">Entrar</a>
                <a href="#trial" class="btn-primary text-white px-6 py-2.5 rounded-full text-sm font-semibold">
                    Experimente Grátis
                </a>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section id="hero" class="pt-40 pb-20 bg-pattern relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-4xl mx-auto mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-violet-50 border border-violet-100 text-sm font-medium text-violet-700 mb-8">
                        <span class="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></span>
                        Sistema Especializado para Confecções
                    </div>
                    
                    <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        O sistema perfeito para sua <span class="text-gradient">confecção</span>
                    </h1>
                    
                    <p class="text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto mb-10">
                        Organize sua produção, crie orçamentos em segundos e veja os lucros crescerem. 
                        Chega de planilhas e papel!
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="#trial" class="btn-primary text-white px-8 py-4 rounded-xl font-bold text-lg w-full sm:w-auto">
                            Experimente Grátis
                        </a>
                        <a href="#features" class="flex items-center gap-2 text-gray-700 hover:text-violet-600 font-semibold transition-colors">
                            <svg class="w-10 h-10 text-violet-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/>
                            </svg>
                            Saiba mais
                        </a>
                    </div>
                </div>

                <!-- Dashboard Mockup -->
                <div class="relative max-w-5xl mx-auto animate-float">
                    <div class="mockup-shadow rounded-2xl overflow-hidden border border-gray-200 bg-white">
                        <div class="bg-gray-100 px-4 py-3 flex items-center gap-2 border-b border-gray-200">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="flex-1 flex justify-center">
                                <div class="bg-white rounded-lg px-4 py-1 text-xs text-gray-500 border">vestalize.com/dashboard</div>
                            </div>
                        </div>
                        <img src="{{ asset('images/dashboard-mockup.png') }}" alt="Dashboard Vestalize" class="w-full" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="hidden p-8 bg-gradient-to-br from-violet-50 to-purple-50">
                            <div class="grid grid-cols-4 gap-4 mb-6">
                                <div class="bg-white rounded-xl p-4 shadow-sm border">
                                    <div class="text-sm text-gray-500 mb-1">Total de Pedidos</div>
                                    <div class="text-2xl font-bold text-gray-900">247</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 shadow-sm border">
                                    <div class="text-sm text-gray-500 mb-1">Faturamento</div>
                                    <div class="text-2xl font-bold text-green-600">R$ 45.890</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 shadow-sm border">
                                    <div class="text-sm text-gray-500 mb-1">Em Produção</div>
                                    <div class="text-2xl font-bold text-violet-600">32</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 shadow-sm border">
                                    <div class="text-sm text-gray-500 mb-1">Clientes Ativos</div>
                                    <div class="text-2xl font-bold text-gray-900">89</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-2 bg-white rounded-xl p-6 shadow-sm border h-48">
                                    <div class="text-sm font-semibold text-gray-700 mb-4">Vendas por Mês</div>
                                    <div class="flex items-end gap-2 h-32">
                                        <div class="flex-1 bg-violet-200 rounded-t" style="height: 40%"></div>
                                        <div class="flex-1 bg-violet-300 rounded-t" style="height: 60%"></div>
                                        <div class="flex-1 bg-violet-400 rounded-t" style="height: 45%"></div>
                                        <div class="flex-1 bg-violet-500 rounded-t" style="height: 80%"></div>
                                        <div class="flex-1 bg-violet-600 rounded-t" style="height: 70%"></div>
                                        <div class="flex-1 bg-violet-700 rounded-t" style="height: 100%"></div>
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl p-6 shadow-sm border">
                                    <div class="text-sm font-semibold text-gray-700 mb-4">Status</div>
                                    <div class="relative w-32 h-32 mx-auto">
                                        <svg viewBox="0 0 36 36" class="w-full h-full">
                                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#7c3aed" stroke-width="3" stroke-dasharray="75, 100"/>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-2xl font-bold text-gray-900">75%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-violet-600 font-bold tracking-wider uppercase text-sm">Funcionalidades</span>
                    <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mt-4 mb-6">
                        Organize sua produção e veja os <span class="text-gradient">lucros crescerem</span>
                    </h2>
                    <p class="text-lg text-gray-600">
                        Tudo o que você precisa para gerenciar sua confecção em um único lugar.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php
                    $features = [
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Controle de Produção', 'desc' => 'Acompanhe cada etapa da produção com nosso Kanban visual. Saiba exatamente onde cada peça está.'],
                        ['icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'title' => 'Orçamentos Rápidos', 'desc' => 'Crie orçamentos profissionais em segundos. Converta mais vendas com propostas impressionantes.'],
                        ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Controle Financeiro', 'desc' => 'Saiba exatamente quanto lucrou em cada pedido. Controle entradas, saídas e fluxo de caixa.'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'title' => 'Gestão de Clientes', 'desc' => 'Cadastre clientes, histórico de pedidos e preferências. Fidelize com atendimento personalizado.'],
                        ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'title' => 'Controle de Estoque', 'desc' => 'Nunca mais fique sem material. Controle tecidos, aviamentos e insumos com alertas automáticos.'],
                        ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Relatórios Completos', 'desc' => 'Dashboards e relatórios detalhados. Tome decisões baseadas em dados reais do seu negócio.'],
                    ];
                    @endphp
                    
                    @foreach($features as $feature)
                    <div class="bg-white rounded-2xl p-8 card-hover border border-gray-100">
                        <div class="feature-icon w-14 h-14 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- How it Works -->
        <section class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <span class="text-violet-600 font-bold tracking-wider uppercase text-sm">Como Funciona</span>
                        <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mt-4 mb-8">
                            Crie orçamentos em <span class="text-gradient">segundos</span>
                        </h2>
                        
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center font-bold shrink-0">1</div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Selecione os itens</h4>
                                    <p class="text-gray-600">Escolha tecidos, modelos e personalizações do seu catálogo.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center font-bold shrink-0">2</div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Configure quantidades</h4>
                                    <p class="text-gray-600">Defina tamanhos, cores e quantidades por grade.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center font-bold shrink-0">3</div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Envie para o cliente</h4>
                                    <p class="text-gray-600">Gere PDF profissional ou compartilhe link direto pelo WhatsApp.</p>
                                </div>
                            </div>
                        </div>

                        <a href="#trial" class="btn-primary inline-flex items-center gap-2 text-white px-8 py-4 rounded-xl font-bold text-lg mt-8">
                            Experimentar Agora
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>

                    <div class="relative">
                        <div class="mockup-shadow rounded-2xl overflow-hidden border border-gray-200 bg-white p-6">
                            <div class="bg-violet-50 rounded-xl p-6 mb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-bold text-gray-900">Novo Pedido</span>
                                    <span class="text-sm text-violet-600 font-medium">Passo 2 de 4</span>
                                </div>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-violet-600 rounded-full" style="width: 50%"></div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                                    <div class="w-16 h-16 bg-violet-100 rounded-lg"></div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Camiseta Básica</div>
                                        <div class="text-sm text-gray-500">Algodão 30.1 - Branca</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-violet-600">R$ 45,00</div>
                                        <div class="text-sm text-gray-500">x 50 un</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                                    <div class="w-16 h-16 bg-purple-100 rounded-lg"></div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Polo Masculina</div>
                                        <div class="text-sm text-gray-500">Piquet - Azul Marinho</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-violet-600">R$ 65,00</div>
                                        <div class="text-sm text-gray-500">x 30 un</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-between">
                                <span class="text-gray-600">Total do Pedido:</span>
                                <span class="text-2xl font-bold text-violet-600">R$ 4.200,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-violet-600 font-bold tracking-wider uppercase text-sm">Preços</span>
                    <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mt-4 mb-6">
                        Escolha o plano <span class="text-gradient">ideal</span> para você
                    </h2>
                    <p class="text-lg text-gray-600">
                        Do MEI à grande empresa, temos o plano perfeito para o seu negócio.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 pt-4">
                    <!-- Plano Start -->
                    <div class="bg-white rounded-2xl p-6 card-hover border border-gray-200 flex flex-col">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Start</h3>
                            <p class="text-sm text-gray-500 mb-4">Para quem está começando</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900">R$ 99,90</span>
                                <span class="text-gray-500">/mês</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-6 flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Inclui:</p>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Gestão de Pedidos</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">CRM / Clientes</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Gestão Financeira</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Personalização de Marca</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-gray-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span class="text-gray-400">1 usuário</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-gray-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span class="text-gray-400">1 loja</span>
                                </li>
                            </ul>
                        </div>
                        
                        <a href="#trial" class="mt-6 block text-center py-3 px-6 rounded-xl border-2 border-violet-600 text-violet-600 font-semibold hover:bg-violet-50 transition-colors">
                            Começar Agora
                        </a>
                    </div>

                    <!-- Plano Básico -->
                    <div class="bg-white rounded-2xl p-6 card-hover border border-gray-200 flex flex-col">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Básico</h3>
                            <p class="text-sm text-gray-500 mb-4">Para pequenos negócios</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900">R$ 199,90</span>
                                <span class="text-gray-500">/mês</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-6 flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Tudo do Start +</p>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Kanban de Produção</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Módulo de Produção</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Orçamentos em PDF</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Relatórios Simples</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600 font-medium">Até 3 usuários</span>
                                </li>
                            </ul>
                        </div>
                        
                        <a href="#trial" class="mt-6 block text-center py-3 px-6 rounded-xl border-2 border-violet-600 text-violet-600 font-semibold hover:bg-violet-50 transition-colors">
                            Começar Agora
                        </a>
                    </div>

                    <!-- Plano Pro - Destaque -->
                    <div class="bg-white rounded-2xl p-6 card-hover border-2 border-violet-500 flex flex-col relative">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="bg-violet-600 text-white text-xs font-bold px-4 py-1 rounded-full">MAIS POPULAR</span>
                        </div>
                        
                        <div class="mb-6 pt-2">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Profissional</h3>
                            <p class="text-sm text-gray-500 mb-4">Para empresas em crescimento</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-violet-600">R$ 299,90</span>
                                <span class="text-gray-500">/mês</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-violet-100 pt-6 flex-1">
                            <p class="text-xs font-semibold text-violet-600 uppercase tracking-wider mb-4">Tudo do Básico +</p>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Frente de Caixa (PDV)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Controle de Estoque</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Orçamento Online</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Relatórios Completos</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600 font-medium">Até 5 usuários</span>
                                </li>
                            </ul>
                        </div>
                        
                        <a href="#trial" class="mt-6 btn-primary block text-center py-3 px-6 rounded-xl text-white font-semibold">
                            Começar Agora
                        </a>
                    </div>

                    <!-- Plano Premium -->
                    <div class="bg-white rounded-2xl p-6 card-hover border border-gray-200 flex flex-col">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Premium</h3>
                            <p class="text-sm text-gray-500 mb-4">Para empresas consolidadas</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900">R$ 499,90</span>
                                <span class="text-gray-500">/mês</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-6 flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Tudo do Pro +</p>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Todas as funcionalidades</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Usuários ilimitados</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Multi-loja ilimitado</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">Sublimação Total</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-violet-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600 font-medium">Suporte prioritário</span>
                                </li>
                            </ul>
                        </div>
                        
                        <a href="#trial" class="mt-6 block text-center py-3 px-6 rounded-xl border-2 border-violet-600 text-violet-600 font-semibold hover:bg-violet-50 transition-colors">
                            Falar com Consultor
                        </a>
                    </div>
                </div>

                <p class="text-center text-gray-500 mt-8 text-sm">
                    Todos os planos incluem 7 dias de teste grátis. Sem cartão de crédito.
                </p>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-violet-600 font-bold tracking-wider uppercase text-sm">Depoimentos</span>
                    <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mt-4 mb-6">
                        O que dizem nossos <span class="text-gradient">clientes</span>
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php
                    $testimonials = [
                        ['name' => 'Maria Silva', 'role' => 'Dona da Confecção MS', 'text' => 'Com o Vestalize, consegui reduzir em 40% o tempo gasto com gestão de pedidos. Agora tenho mais tempo para focar na produção.', 'rating' => 5],
                        ['name' => 'Carlos Mendes', 'role' => 'Proprietário da CM Malharia', 'text' => 'O Kanban de produção é incrível! Consigo ver exatamente onde cada pedido está e nunca mais perdi prazo de entrega.', 'rating' => 5],
                        ['name' => 'Ana Paula', 'role' => 'Gestora da AP Uniformes', 'text' => 'Os orçamentos automáticos são um diferencial. Meus clientes ficam impressionados com a rapidez e profissionalismo das propostas.', 'rating' => 5],
                    ];
                    @endphp

                    @foreach($testimonials as $testimonial)
                    <div class="bg-white rounded-2xl p-8 card-hover border border-gray-100 relative">
                        <div class="testimonial-quote absolute top-4 right-6">"</div>
                        <div class="flex items-center gap-1 mb-4">
                            @for($i = 0; $i < $testimonial['rating']; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 leading-relaxed mb-6 relative z-10">{{ $testimonial['text'] }}</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                {{ substr($testimonial['name'], 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">{{ $testimonial['name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $testimonial['role'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-24 bg-gray-50">
            <div class="max-w-4xl mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="text-violet-600 font-bold tracking-wider uppercase text-sm">FAQ</span>
                    <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mt-4">
                        Perguntas <span class="text-gradient">Frequentes</span>
                    </h2>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 divide-y divide-gray-200" x-data="{ open: null }">
                    @php
                    $faqs = [
                        ['q' => 'Posso testar o sistema antes de assinar?', 'a' => 'Sim! Oferecemos 7 dias de teste grátis, sem necessidade de cartão de crédito. Você terá acesso a todas as funcionalidades.'],
                        ['q' => 'O sistema funciona no celular?', 'a' => 'Sim, o Vestalize é totalmente responsivo e funciona perfeitamente em smartphones, tablets e computadores.'],
                        ['q' => 'Preciso instalar algum programa?', 'a' => 'Não! O Vestalize é 100% online. Basta acessar pelo navegador de qualquer dispositivo com internet.'],
                        ['q' => 'Meus dados estão seguros?', 'a' => 'Absolutamente. Utilizamos criptografia de ponta a ponta e seus dados ficam hospedados em servidores seguros com backup automático.'],
                        ['q' => 'Posso cancelar a qualquer momento?', 'a' => 'Sim, você pode cancelar sua assinatura a qualquer momento, sem multas ou taxas adicionais.'],
                    ];
                    @endphp

                    @foreach($faqs as $index => $faq)
                    <div class="faq-item" x-data="{ isOpen: false }">
                        <button @click="isOpen = !isOpen" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900">{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 text-violet-600 transform transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="isOpen" x-collapse class="px-6 pb-5">
                            <p class="text-gray-600">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section id="trial" class="py-24">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-gradient-to-br from-violet-600 to-purple-700 rounded-3xl p-10 md:p-16 text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <defs>
                                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#grid)"/>
                        </svg>
                    </div>
                    
                    <div class="relative z-10">
                        <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">
                            Pronto para organizar sua confecção?
                        </h2>
                        <p class="text-xl text-violet-100 mb-10 max-w-2xl mx-auto">
                            Comece agora mesmo com 7 dias grátis. Sem cartão de crédito, sem compromisso.
                        </p>
                        <a href="{{ route('register') ?? '#' }}" class="inline-block bg-white text-violet-700 hover:bg-violet-50 px-10 py-5 rounded-xl font-bold text-xl shadow-lg transition-all transform hover:scale-105">
                            Começar Teste Grátis 
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-16 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-10 w-auto">
                    </div>
                    <p class="text-gray-600 mb-6 max-w-sm">
                        O sistema de gestão completo para confecções e malharias. Organize sua produção e aumente seus lucros.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-violet-100 flex items-center justify-center text-gray-600 hover:text-violet-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-violet-100 flex items-center justify-center text-gray-600 hover:text-violet-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Produto</h4>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-gray-600 hover:text-violet-600 transition-colors">Funcionalidades</a></li>
                        <li><a href="#pricing" class="text-gray-600 hover:text-violet-600 transition-colors">Preços</a></li>
                        <li><a href="#faq" class="text-gray-600 hover:text-violet-600 transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-gray-900 mb-4">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('privacy.show') }}" class="text-gray-600 hover:text-violet-600 transition-colors">Privacidade</a></li>
                        <li><a href="{{ route('terms.show') }}" class="text-gray-600 hover:text-violet-600 transition-colors">Termos de Uso</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-500 text-sm">Copyright {{ date('Y') }} Vestalize. Todos os direitos reservados.</p>
                <p class="text-gray-500 text-sm">Feito com  para confecções brasileiras</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for FAQ accordion -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.directive('collapse', (el) => {
                el.style.overflow = 'hidden';
                el.style.height = '0px';
                el.style.transition = 'height 0.2s ease-out';
                
                const show = () => {
                    el.style.height = el.scrollHeight + 'px';
                };
                const hide = () => {
                    el.style.height = '0px';
                };
                
                if (el._x_show) {
                    show();
                } else {
                    hide();
                }
            });
        });
    </script>
</body>
</html>
