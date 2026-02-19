<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $tenant->name ?? 'Catálogo')</title>
    <meta name="description" content="Catálogo online - {{ $tenant->name ?? '' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @php
        $primaryColor = $tenant->primary_color ?? '#6366f1';
        $secondaryColor = $tenant->secondary_color ?? '#8b5cf6';
    @endphp

    <style>
        :root {
            --primary: {{ $primaryColor }};
            --primary-light: {{ $primaryColor }}22;
            --primary-dark: {{ $tenant->primary_color_dark ?? $primaryColor }};
            --secondary: {{ $secondaryColor }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

        /* ─── Header ─── */
        .catalog-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: #1e293b;
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 40;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        }

        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #1e293b;
        }

        .header-logo img {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: contain;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .header-logo-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
        }

        .header-logo .store-name {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #1e293b;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ─── Cart Button ─── */
        .cart-btn {
            position: relative;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            border-radius: 14px;
            padding: 10px 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cart-btn:hover {
            background: white;
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(239,68,68,0.4);
        }

        .whatsapp-btn {
            background: #25d366;
            color: white;
            border: none;
            border-radius: 14px;
            padding: 10px;
            width: 44px;
            height: 44px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(37,211,102,0.2);
        }

        .whatsapp-btn:hover {
            background: #20bd5a;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37,211,102,0.3);
        }

        /* ─── Main ─── */
        .catalog-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 16px 100px;
        }

        /* ─── Category Tabs ─── */
        .category-bar {
            padding: 16px 0 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .category-bar::-webkit-scrollbar { display: none; }

        .category-tabs {
            display: flex;
            gap: 8px;
            padding: 0 4px;
        }

        .category-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .category-tab:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: white;
            transform: translateY(-1px);
        }

        .category-tab.active {
            background: #1e293b;
            color: white;
            border-color: #1e293b;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        /* ─── Product Grid ─── */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            padding: 8px 0;
        }

        @media (min-width: 640px) {
            .products-grid { grid-template-columns: repeat(3, 1fr); gap: 16px; }
        }
        @media (min-width: 1024px) {
            .products-grid { grid-template-columns: repeat(4, 1fr); gap: 20px; }
        }
        @media (min-width: 1280px) {
            .products-grid { grid-template-columns: repeat(5, 1fr); }
        }

        .product-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            border: 1px solid #f1f5f9;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
        }

        .product-card-image {
            position: relative;
            width: 100%;
            padding-top: 100%; /* 1:1 aspect ratio */
            background: #f8fafc;
            overflow: hidden;
        }

        .product-card-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .product-card:hover .product-card-image img {
            transform: scale(1.05);
        }

        .product-card-image .no-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            font-size: 32px;
        }

        .product-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #10b981;
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-badge.out-of-stock {
            background: #ef4444;
        }

        .product-card-body {
            padding: 10px 12px 12px;
        }

        .product-card-category {
            font-size: 10px;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .product-card-title {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card-prices {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .product-price-retail {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
        }

        .product-price-wholesale {
            font-size: 11px;
            color: #10b981;
            font-weight: 600;
        }

        .product-quick-add {
            margin-top: 8px;
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .product-quick-add:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        /* ─── Slide-Over Cart ─── */
        .cart-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 50;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .cart-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .cart-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 420px;
            height: 100%;
            background: white;
            z-index: 51;
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
            display: flex;
            flex-direction: column;
        }

        .cart-panel.open {
            transform: translateX(0);
        }

        .cart-panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-panel-header h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .cart-panel-close {
            background: none;
            border: none;
            color: #64748b;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
        }

        .cart-panel-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px 20px;
        }

        .cart-empty {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }

        .cart-empty i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .cart-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .cart-item-image {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            background: #f8fafc;
            overflow: hidden;
            flex-shrink: 0;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item-title {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .cart-item-variant {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .cart-item-qty {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-item-qty button {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .cart-item-qty button:hover {
            background: #f1f5f9;
        }

        .cart-item-qty span {
            font-size: 14px;
            font-weight: 600;
            min-width: 20px;
            text-align: center;
        }

        .cart-item-price {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
        }

        .cart-item-remove {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 12px;
            cursor: pointer;
            padding: 2px;
            opacity: 0.7;
        }

        .cart-item-remove:hover { opacity: 1; }

        .cart-panel-footer {
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .cart-total-label {
            font-size: 14px;
            color: #64748b;
        }

        .cart-total-value {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
        }

        .cart-wholesale-label {
            font-size: 11px;
            color: #10b981;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .cart-checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 14px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .cart-checkout-btn:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            color: white;
        }

        /* ─── Toast ─── */
        .toast {
            position: fixed;
            bottom: 140px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            padding: 12px 24px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 700;
            z-index: 9999;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: none;
            text-align: center;
            width: fit-content;
            max-width: 85vw;
            opacity: 0;
            visibility: hidden;
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .toast.success { background: rgba(16, 185, 129, 0.95); }
        .toast.error { background: rgba(239, 68, 68, 0.95); }

        /* ─── Footer ─── */
        .catalog-footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 24px 16px;
            text-align: center;
            font-size: 13px;
            width: 100vw;
            margin-left: calc(-50vw + 50%);
        }

        .catalog-footer a {
            color: #e2e8f0;
            text-decoration: none;
        }

        /* ─── Empty State ─── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.4;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 8px;
        }

        /* ─── Animations ─── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        .product-card {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* ─── Buttons ─── */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            color: white;
        }

        /* ─── Form Styles ─── */
        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: white;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 80px;
        }
    </style>

    @yield('extra-styles')
</head>
<body>

    <!-- Header -->
    <header class="catalog-header">
        <div class="header-inner">
            <a href="{{ route('catalog.show', $storeCode) }}" class="header-logo">
                @if($tenant->logo_path)
                    <img src="{{ asset('storage/' . $tenant->logo_path) }}" 
                         alt="{{ $tenant->name }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="header-logo-placeholder" style="display:none;">
                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                    </div>
                @else
                    <div class="header-logo-placeholder">
                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                    </div>
                @endif
                <span class="store-name">{{ $tenant->name }}</span>
            </a>

            <div class="header-actions">
                @if($tenant->phone)
                    <a href="https://wa.me/55{{ preg_replace('/\D/', '', $tenant->phone) }}" target="_blank" class="whatsapp-btn" title="Falar com vendedor">
                        <i class="fab fa-whatsapp"></i>
                        <span class="hidden sm:inline" style="display: none;">Contato</span>
                    </a>
                @endif

                <button class="cart-btn" onclick="toggleCart()" id="cart-toggle-btn">
                    <i class="fas fa-shopping-bag"></i>
                    <span id="cart-btn-total">R$ 0,00</span>
                    <div class="cart-badge" id="cart-badge" style="display: none;">0</div>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="catalog-main">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="catalog-footer">
        <p>&copy; {{ date('Y') }} {{ $tenant->name }}. Todos os direitos reservados.</p>
        <p style="margin-top: 4px; font-size: 11px; opacity: 0.6;">Powered by Vestalize</p>
    </footer>

    <!-- Cart Slide-Over -->
    <div class="cart-overlay" id="cart-overlay" onclick="toggleCart()"></div>
    <div class="cart-panel" id="cart-panel">
        <div class="cart-panel-header">
            <h3><i class="fas fa-shopping-bag" style="color: var(--primary); margin-right: 6px;"></i> Carrinho</h3>
            <button class="cart-panel-close" onclick="toggleCart()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="cart-panel-body" id="cart-panel-body">
            <div class="cart-empty" id="cart-empty">
                <i class="fas fa-shopping-bag"></i>
                <p style="font-weight: 600; color: #64748b;">Carrinho vazio</p>
                <p style="font-size: 12px; margin-top: 4px;">Adicione produtos para começar</p>
            </div>
            <div id="cart-items-container"></div>
        </div>

        <div class="cart-panel-footer" id="cart-footer" style="display: none;">
            <div class="cart-total-row">
                <span class="cart-total-label">Total</span>
                <span class="cart-total-value" id="cart-total-value">R$ 0,00</span>
            </div>
            <div class="cart-wholesale-label" id="cart-wholesale-label" style="display: none;">
                <i class="fas fa-tag"></i> Preço atacado aplicado
            </div>
            <a href="{{ route('catalog.checkout', $storeCode) }}" class="cart-checkout-btn" id="cart-checkout-btn">
                <i class="fas fa-lock"></i> Finalizar Pedido
            </a>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script>
        const STORE_CODE = '{{ $storeCode }}';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        // ─── Cart State ───
        let cartData = { items: {}, total_items: 0, subtotal: 0, total: 0, is_wholesale: false };

        // Load initial cart state from server
        fetch(`/catalogo/${STORE_CODE}/carrinho`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => { if (data.success) updateCartUI(data.cart); })
        .catch(() => {});

        function formatMoney(value) {
            return 'R$ ' + Number(value).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // ─── Toggle Cart ───
        function toggleCart() {
            document.getElementById('cart-overlay').classList.toggle('open');
            document.getElementById('cart-panel').classList.toggle('open');
            document.body.style.overflow = document.getElementById('cart-panel').classList.contains('open') ? 'hidden' : '';
        }

        // ─── Update Cart UI ───
        function updateCartUI(data) {
            cartData = data;
            const badge = document.getElementById('cart-badge');
            const btnTotal = document.getElementById('cart-btn-total');
            const itemsContainer = document.getElementById('cart-items-container');
            const empty = document.getElementById('cart-empty');
            const footer = document.getElementById('cart-footer');
            const totalValue = document.getElementById('cart-total-value');
            const wholesaleLabel = document.getElementById('cart-wholesale-label');

            badge.textContent = data.total_items;
            badge.style.display = data.total_items > 0 ? 'flex' : 'none';
            btnTotal.textContent = formatMoney(data.total);

            if (data.total_items === 0) {
                empty.style.display = 'block';
                itemsContainer.innerHTML = '';
                footer.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            footer.style.display = 'block';
            totalValue.textContent = formatMoney(data.total);
            wholesaleLabel.style.display = data.is_wholesale ? 'block' : 'none';

            let html = '';
            for (const [key, item] of Object.entries(data.items)) {
                const variant = [item.size, item.color].filter(Boolean).join(' · ') || '';
                html += `
                    <div class="cart-item" style="${item.is_incomplete ? 'border-left: 3px solid #ef4444; background: #fff1f2;' : ''}">
                        <div class="cart-item-image">
                            ${item.image ? `<img src="${item.image}" alt="">` : `<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#cbd5e1;"><i class="fas fa-image"></i></div>`}
                        </div>
                        <div class="cart-item-info">
                            <div class="cart-item-title">${item.title}</div>
                            ${variant ? `<div class="cart-item-variant">${variant}</div>` : ''}
                            ${item.is_incomplete ? `<div style="font-size:10px; color:#ef4444; font-weight:700; margin-bottom:4px;"><i class="fas fa-exclamation-triangle"></i> Seleção incompleta</div><a href="/catalogo/${STORE_CODE}/produto/${item.product_id}" style="font-size:10px; color:var(--primary); font-weight:700; text-decoration:underline;">Escolher tamanho/cor</a>` : ''}
                            <div class="cart-item-qty">
                                <button onclick="updateCartQty('${key}', ${item.quantity - 1})"><i class="fas fa-minus" style="font-size:10px;"></i></button>
                                <span>${item.quantity}</span>
                                <button onclick="updateCartQty('${key}', ${item.quantity + 1})"><i class="fas fa-plus" style="font-size:10px;"></i></button>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div class="cart-item-price">${formatMoney(item.line_total)}</div>
                            ${item.is_wholesale ? '<div style="font-size:10px;color:#10b981;font-weight:600;">Atacado</div>' : ''}
                            <button class="cart-item-remove" onclick="removeCartItem('${key}')"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                `;
            }
            itemsContainer.innerHTML = html;

            // Bloquear checkout se houver itens incompletos
            const hasIncomplete = Object.values(data.items).some(item => item.is_incomplete);
            const checkoutBtn = document.getElementById('cart-checkout-btn');
            if (hasIncomplete) {
                checkoutBtn.classList.add('opacity-50', 'pointer-events-none');
                checkoutBtn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Complete a seleção para finalizar';
            } else {
                checkoutBtn.classList.remove('opacity-50', 'pointer-events-none');
                checkoutBtn.innerHTML = '<i class="fas fa-lock"></i> Finalizar Pedido';
            }
        }

        // ─── Add to Cart ───
        async function addToCart(productId, size, color, qty, items = null) {
            try {
                const body = {
                    product_id: productId,
                };

                if (items && items.length > 0) {
                    body.items = items;
                } else {
                    body.quantity = qty || 1;
                    body.size = size || null;
                    body.color = color || null;
                }

                const res = await fetch(`/catalogo/${STORE_CODE}/carrinho/adicionar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify(body),
                });

                const data = await res.json();
                
                if (res.status === 422) {
                    showToast(data.message || 'Dados inválidos. Verifique as seleções.', 'error');
                    return;
                }

                if (data.success) {
                    updateCartUI(data.cart);
                    showToast(data.message || 'Adicionado ao carrinho!');
                }
            } catch (e) {
                console.error('Erro ao adicionar ao carrinho:', e);
            }
        }

        // ─── Update Cart Quantity ───
        async function updateCartQty(cartKey, qty) {
            try {
                const res = await fetch(`/catalogo/${STORE_CODE}/carrinho/atualizar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ cart_key: cartKey, quantity: qty }),
                });
                const data = await res.json();
                if (data.success) updateCartUI(data.cart);
            } catch (e) {
                console.error('Erro ao atualizar carrinho:', e);
            }
        }

        // ─── Remove from Cart ───
        async function removeCartItem(cartKey) {
            try {
                const res = await fetch(`/catalogo/${STORE_CODE}/carrinho/remover`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ cart_key: cartKey }),
                });
                const data = await res.json();
                if (data.success) updateCartUI(data.cart);
            } catch (e) {
                console.error('Erro ao remover do carrinho:', e);
            }
        }

        // ─── Toast ───
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.classList.remove('success', 'error');
            toast.classList.add(type);
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // Init cart UI
        document.addEventListener('DOMContentLoaded', () => updateCartUI(cartData));
    </script>

    @yield('extra-scripts')
</body>
</html>
