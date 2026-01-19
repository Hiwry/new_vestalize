(function() {
    'use strict';

    const getSearchUrl = () => {
        return document.body?.dataset?.clientSearchUrl || '/api/clients/search';
    };

    const renderCard = (client) => {
        const clientJson = JSON.stringify(client)
            .replace(/</g, '\\u003c')
            .replace(/>/g, '\\u003e')
            .replace(/&/g, '\\u0026')
            .replace(/'/g, '&#39;');

        const emailPart = client.email ? ` &bull; ${client.email}` : '';

        return `
        <div class="p-4 bg-white dark:bg-slate-800 rounded-lg border-2 border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/20 cursor-pointer transition-all group"
             onclick='window.fillClientData && window.fillClientData(${clientJson})'>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-[#7c3aed] to-[#7c3aed] dark:from-[#7c3aed] dark:to-[#7c3aed] force-white rounded-lg flex items-center justify-center shadow-lg shadow-[#7c3aed]/20 dark:shadow-[#7c3aed]/20 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">${client.name || 'Sem nome'}</div>
                    <div class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">
                        ${(client.phone_primary || '')}${emailPart}
                    </div>
                </div>
                <div class="text-[#7c3aed] dark:text-[#7c3aed] group-hover:translate-x-1 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>`;
    };

    const ensureFillClientData = () => {
        if (typeof window.fillClientData === 'function') return;

        window.fillClientData = function(client) {
            const safeSetValue = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val || '';
            };

            if (!client) return;

            safeSetValue('client_id', client.id);
            safeSetValue('name', client.name);
            safeSetValue('phone_primary', client.phone_primary);
            safeSetValue('phone_secondary', client.phone_secondary);
            safeSetValue('email', client.email);
            safeSetValue('cpf_cnpj', client.cpf_cnpj);
            safeSetValue('address', client.address);
            safeSetValue('city', client.city);
            safeSetValue('state', client.state);
            safeSetValue('zip_code', client.zip_code);
            safeSetValue('category', client.category);

            const resultsDiv = document.getElementById('search-results');
            if (resultsDiv) {
                resultsDiv.innerHTML =
                    '<div class="p-4 bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-900/10 border-2 border-emerald-200 dark:border-emerald-800/30 rounded-xl shadow-sm">' +
                    '<div class="flex items-center space-x-3">' +
                    '<div class="w-10 h-10 bg-emerald-600 dark:bg-emerald-500 rounded-lg flex items-center justify-center shadow-lg shadow-emerald-600/20">' +
                    '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' +
                    '</svg>' +
                    '</div>' +
                    '<div class="flex-1">' +
                    '<p class="text-sm font-bold text-gray-900 dark:text-white">Cliente selecionado com sucesso!</p>' +
                    '<p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">VocÇ¦ pode editar os dados se necessÇ­rio antes de continuar.</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }
        };
    };

    const ensureRunClientSearch = () => {
        if (typeof window.runClientSearch === 'function') return;

        window.runClientSearch = function() {
            const input = document.getElementById('search-client');
            const resultsDiv = document.getElementById('search-results');

            if (!input || !resultsDiv) return;

            const query = input.value || '';
            if (query.length < 3) {
                resultsDiv.innerHTML = '<p class="text-sm text-gray-500">Digite ao menos 3 caracteres para buscar</p>';
                return;
            }

            const url = getSearchUrl() + '?q=' + encodeURIComponent(query);

            fetch(url)
                .then(resp => resp.json())
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        resultsDiv.innerHTML = '<p class="text-sm text-gray-500">Nenhum cliente encontrado</p>';
                        return;
                    }

                    resultsDiv.innerHTML = data.map(renderCard).join('');
                })
                .catch(err => {
                    console.error('Erro na busca de clientes (fallback):', err);
                    resultsDiv.innerHTML = '<p class="text-sm text-red-600">Erro ao buscar clientes</p>';
                });
        };
    };

    const bootstrap = () => {
        ensureFillClientData();
        ensureRunClientSearch();
    };

    document.addEventListener('DOMContentLoaded', bootstrap);
    document.addEventListener('ajax-content-loaded', bootstrap);
    document.addEventListener('content-loaded', bootstrap);
})();
