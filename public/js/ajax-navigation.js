/**
 * Sistema de Navegação AJAX para manter a sidebar fixa
 * Intercepta cliques nos links da sidebar e carrega apenas o conteúdo principal
 */

(function () {
    'use strict';

    // Estado da navegação
    let isNavigating = false;
    let currentUrl = window.location.href;

    // Função para extrair o conteúdo principal de uma página HTML
    function extractMainContent(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Procurar pelo elemento main ou #main-content
        const mainContent = doc.querySelector('#main-content main') ||
            doc.querySelector('main') ||
            doc.querySelector('#main-content');

        if (!mainContent) {
            console.error('Conteúdo principal não encontrado na resposta');
            return null;
        }

        return mainContent.innerHTML;
    }

    // Função para extrair o título da página
    function extractTitle(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        return doc.querySelector('title')?.textContent || document.title;
    }

    // Função para atualizar o estado ativo dos links da sidebar
    function updateActiveLink(url) {
        const sidebarLinks = document.querySelectorAll('#sidebar nav a');
        const currentPath = new URL(url, window.location.origin).pathname;

        sidebarLinks.forEach(link => {
            const linkPath = new URL(link.href, window.location.origin).pathname;

            // Verificação mais precisa para determinar se está ativo
            const isExactMatch = currentPath === linkPath;
            // Verifica prefixo, mas ignora rotas raiz ou excessivamente genéricas
            const isPrefixMatch = linkPath !== '/' &&
                linkPath !== '/dashboard' &&
                currentPath.startsWith(linkPath);

            // Lógica especial para Vendas/Sales
            const isSalesMatch = (linkPath === '/sales' || linkPath === '/vendas') &&
                (currentPath.startsWith('/sales') || currentPath.startsWith('/vendas'));

            const isActive = isExactMatch || isPrefixMatch || isSalesMatch;

            // Classes padrão para links INATIVOS (copiado do Blade)
            // text-muted hover:bg-white/5 hover:text-white

            if (isActive) {
                // Estado ATIVO
                link.classList.add('active-link');
                link.classList.remove('text-muted', 'hover:bg-white/5', 'hover:text-white');

                // Limpar classes legacy se existirem
                link.classList.remove('bg-purple-600', 'bg-blue-600', 'bg-indigo-600', 'text-white', 'text-gray-400');
            } else {
                // Estado INATIVO
                link.classList.remove('active-link');
                link.classList.add('text-muted', 'hover:bg-white/5', 'hover:text-white');

                // Limpar classes legacy se existirem
                link.classList.remove('bg-purple-600', 'bg-blue-600', 'bg-indigo-600', 'text-white', 'text-gray-400');
            }

            // Atualizar ícone se existir (opcional, já que o CSS lida com isso via .active-link)
            // Mas mantemos limpeza de classes antigas
            const icon = link.querySelector('svg, i');
            if (icon) {
                icon.classList.remove('text-gray-500', 'dark:text-gray-400', 'text-white', 'text-primary');
            }
        });
    }

    // Função para carregar página via AJAX
    async function loadPage(url) {
        // Debug: Log URL and type
        console.log('AJAX: loadPage called with:', {
            url: url,
            type: typeof url,
            isElement: url instanceof HTMLElement,
            stack: new Error().stack
        });

        // Guard: If URL is an object (common bug with ID shadowing), try to recover
        if (typeof url !== 'string' && url !== null) {
            console.error('AJAX ERROR: url is not a string!', url);
            if (url instanceof HTMLElement) {
                console.warn('AJAX: Recovering from HTMLElement URL...');
                if (url.value && typeof url.value === 'string') {
                    url = url.value;
                } else if (url.href && typeof url.href === 'string') {
                    url = url.href;
                } else {
                    url = String(url);
                }
            } else {
                url = String(url);
            }
        }

        // Nunca usar AJAX para o catálogo público (/catalogo) ou orçamento (/orcamento)
        if (isCatalogPublicUrl(url) || (typeof url === 'string' && url.includes('/orcamento'))) {
            window.location.href = url;
            return;
        }

        if (isNavigating) {
            return;
        }

        // Se a URL for a mesma que a atual, não fazer nada
        if (url === currentUrl || url === window.location.href) {
            return;
        }

        isNavigating = true;
        const mainContent = document.querySelector('#main-content main');
        const mainContentWrapper = document.querySelector('#main-content');

        if (!mainContent) {
            console.error('Área de conteúdo principal não encontrada');
            isNavigating = false;
            // Fallback: recarregar página normalmente
            window.location.href = url;
            return;
        }

        // Garantir dark mode antes de mostrar loading
        const isDarkMode = localStorage.getItem('dark') === 'true';
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        }

        // Mostrar indicador de progresso no topo (mais moderno e rápido que limpar a tela)
        let progressBar = document.getElementById('ajax-progress-bar');
        if (!progressBar) {
            progressBar = document.createElement('div');
            progressBar.id = 'ajax-progress-bar';
            progressBar.style.position = 'fixed';
            progressBar.style.top = '0';
            progressBar.style.left = '0';
            progressBar.style.height = '3px';
            progressBar.style.backgroundColor = '#8b5cf6';
            progressBar.style.zIndex = '9999';
            progressBar.style.transition = 'width 0.3s ease-out, opacity 0.5s ease';
            progressBar.style.width = '0%';
            document.body.appendChild(progressBar);
        }

        progressBar.style.opacity = '1';
        progressBar.style.width = '30%';

        // Timer para progresso falso enquanto carrega
        const progressTimer = setInterval(() => {
            const currentWidth = parseFloat(progressBar.style.width);
            if (currentWidth < 90) {
                progressBar.style.width = (currentWidth + (90 - currentWidth) * 0.1) + '%';
            }
        }, 300);

        try {
            // Fazer requisição AJAX
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html,application/xhtml+xml',
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                // Se for redirecionamento ou erro, recarregar página normalmente
                if (response.status >= 300 && response.status < 400) {
                    window.location.href = url;
                    return;
                }
                throw new Error(`Erro ao carregar página: ${response.status}`);
            }

            const html = await response.text();

            // Verificar se a resposta é HTML válido
            if (!html || html.trim().length === 0) {
                throw new Error('Resposta vazia do servidor');
            }

            // Parsear uma vez para reutilizar DOM e capturar scripts fora do <main>
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Extrair conteúdo principal
            const mainEl = doc.querySelector('#main-content main') ||
                doc.querySelector('main') ||
                doc.querySelector('#main-content');

            if (!mainEl) {
                window.location.href = url;
                return;
            }

            const newContent = mainEl.innerHTML;

            // Atualizar conteúdo de forma que preserve e execute scripts
            // Primeiro, criar um container temporário
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newContent;

            // Coletar informações dos scripts ANTES de mover o conteúdo
            // Inclui scripts dentro e fora do <main> (ex.: @stack('scripts'))
            const mainScripts = Array.from(tempDiv.querySelectorAll('script'));
            const extraScripts = Array.from(doc.querySelectorAll('script')).filter(s => !mainEl.contains(s));
            const scriptData = [...mainScripts, ...extraScripts].map(script => ({
                src: script.src,
                text: script.textContent,
                attributes: Array.from(script.attributes).map(attr => ({
                    name: attr.name,
                    value: attr.value
                })),
                async: script.async,
                defer: script.defer
            }));

            console.log(`AJAX: Found ${scriptData.length} scripts to execute`);

            // Remover scripts do tempDiv antes de mover (para não duplicar)
            tempDiv.querySelectorAll('script').forEach(script => script.remove());

            // Limpar o conteúdo atual
            mainContent.innerHTML = '';

            // Mover todos os nós para o conteúdo principal
            while (tempDiv.firstChild) {
                mainContent.appendChild(tempDiv.firstChild);
            }

            // Executar scripts coletados
            scriptData.forEach((scriptInfo, index) => {
                try {
                    if (!scriptInfo.src && scriptInfo.text) {
                        // Evitar re-execução de scripts de tema/localStorage já carregados
                        if (scriptInfo.text.includes('localStorage.getItem') && scriptInfo.text.includes('dark')) return;

                        const newScript = document.createElement('script');
                        newScript.textContent = `(function() { \ntry{\n${scriptInfo.text}\n}catch(e){console.error('AJAX Script Error:', e);}\n })();`;

                        document.body.appendChild(newScript);
                        setTimeout(() => {
                            if (newScript.parentNode) {
                                newScript.parentNode.removeChild(newScript);
                            }
                        }, 500);
                    } else if (scriptInfo.src) {
                        // Para scripts externos, verificar se já foram carregados
                        const existingScript = document.querySelector(`script[src="${scriptInfo.src}"]`);
                        if (!existingScript) {
                            const newScript = document.createElement('script');
                            scriptInfo.attributes.forEach(attr => {
                                newScript.setAttribute(attr.name, attr.value);
                            });
                            newScript.src = scriptInfo.src;
                            newScript.async = scriptInfo.async;
                            newScript.defer = scriptInfo.defer;
                            document.body.appendChild(newScript);
                        }
                    }
                } catch (e) {
                    console.error(`AJAX: Error executing script ${index}:`, e);
                }
            });

            // Atualizar título
            const newTitle = extractTitle(html);
            if (newTitle && newTitle !== document.title) {
                document.title = newTitle;
            }

            // Atualizar URL sem recarregar
            try {
                const finalUrl = typeof url === 'string' ? url : String(url);
                window.history.pushState({ url: finalUrl }, newTitle || document.title, finalUrl);
            } catch (e) {
                console.error('AJAX: Failed to pushState', e);
            }
            currentUrl = url;

            // Atualizar links ativos na sidebar
            updateActiveLink(url);

            // Atualizar navegação mobile inferior
            if (typeof window.updateBottomNav === 'function') {
                window.updateBottomNav();
            }

            // Garantir que o dark mode seja mantido após carregar novo conteúdo
            if (isDarkMode) {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            }

            // Atualizar ícones do dark mode
            const moonIcon = document.getElementById('moon-icon');
            const sunIcon = document.getElementById('sun-icon');
            if (moonIcon && sunIcon) {
                if (isDarkMode) {
                    moonIcon.classList.add('hidden');
                    sunIcon.classList.remove('hidden');
                } else {
                    moonIcon.classList.remove('hidden');
                    sunIcon.classList.add('hidden');
                }
            }

            // Reinicializar scripts se necessário
            reinitializeScripts();

            // Reinicializar sistema de notificações se existir
            if (typeof window.fetchNotifications === 'function') {
                window.fetchNotifications();
            }

            // Scroll para o topo suavemente
            if (mainContentWrapper) {
                mainContentWrapper.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

        } catch (error) {
            console.error('Erro ao carregar página via AJAX:', error);
            // Se falhar o AJAX, recarregar a página da forma tradicional
            window.location.href = url;
        } finally {
            clearInterval(progressTimer);
            if (progressBar) {
                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressBar.style.opacity = '0';
                    setTimeout(() => {
                        progressBar.style.width = '0%';
                    }, 500);
                }, 200);
            }
            isNavigating = false;
        }
    }

    // Função para reinicializar scripts após carregar novo conteúdo
    function reinitializeScripts() {
        // Disparar evento para scripts que precisam ser reinicializados
        document.dispatchEvent(new CustomEvent('content-loaded', {
            bubbles: true,
            cancelable: true
        }));

        // Reinicializar Alpine.js se necessário
        if (window.Alpine && typeof window.Alpine.initTree === 'function') {
            try {
                const mainContent = document.querySelector('#main-content');
                if (mainContent) {
                    window.Alpine.initTree(mainContent);
                }
            } catch (error) {
                console.error('Erro ao reinicializar Alpine.js:', error);
            }
        }

        // Reexecutar scripts inline que possam estar no novo conteúdo
        // Nota: Scripts inline no conteúdo serão executados automaticamente pelo navegador
        // quando inserirmos o HTML. Aqui apenas garantimos que scripts dinâmicos sejam processados.

        // Aguardar um pouco para garantir que o DOM foi atualizado
        setTimeout(() => {
            // Disparar evento adicional para scripts que precisam ser executados após um delay
            document.dispatchEvent(new CustomEvent('ajax-content-loaded', {
                bubbles: true,
                cancelable: true
            }));
        }, 100);
    }

    // Verificar se uma URL ou caminho pertence ao catálogo público (/catalogo)
    function isCatalogPublicUrl(urlOrPath) {
        try {
            const url = typeof urlOrPath === 'string' && urlOrPath.startsWith('/')
                ? new URL(urlOrPath, window.location.origin)
                : new URL(urlOrPath, window.location.origin);

            const path = url.pathname || '';
            return path === '/catalogo' || path.startsWith('/catalogo/');
        } catch (e) {
            return false;
        }
    }

    // Interceptar cliques em links globalmente para navegação SPA
    function setupGlobalNavigation() {
        document.body.addEventListener('click', async (e) => {
            // Encontrar o link mais próximo
            let link = e.target;
            while (link && link.tagName !== 'A') {
                link = link.parentElement;
            }

            if (!link || link.tagName !== 'A') {
                return;
            }

            // Ignorar links externos, com target="_blank", ou com atributos especiais
            if (link.target === '_blank' ||
                link.href.startsWith('mailto:') ||
                link.href.startsWith('tel:') ||
                link.href.startsWith('javascript:') ||
                link.hasAttribute('data-no-ajax') ||
                link.hasAttribute('data-no-js-nav') ||
                link.classList.contains('no-ajax') ||
                link.hasAttribute('download')) {
                return;
            }

            // Ignorar se for um link dentro de um formulário que não seja apenas navegação
            if (link.closest('form') && link.type !== 'button') {
                return;
            }

            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('#')) {
                return;
            }

            // Verificar se é uma URL interna
            let url;
            try {
                url = new URL(link.href);
                if (url.origin !== window.location.origin) {
                    return; // Link externo, deixar comportamento padrão
                }
            } catch (e) {
                return; // URL inválida
            }

            // Nunca usar AJAX para o catálogo público (/catalogo) ou orçamento (/orcamento) ou rotas de logout/login explicitas
            const path = url.pathname;
            if (isCatalogPublicUrl(url.href) ||
                path.startsWith('/orcamento') ||
                path.startsWith('/logout') ||
                path.startsWith('/login') ||
                path.startsWith('/register')) {
                return;
            }

            // Se for o mesmo caminho e mesma query, não fazer nada
            const currentPath = window.location.pathname + window.location.search;
            const targetPath = url.pathname + url.search;
            if (currentPath === targetPath) {
                e.preventDefault();
                return;
            }

            // Prevenir comportamento padrão
            e.preventDefault();
            e.stopPropagation();

            // Carregar página via AJAX
            await loadPage(url.href);
        }, true);
    }

    // Lidar com navegação do navegador (voltar/avançar)
    window.addEventListener('popstate', (e) => {
        if (e.state && e.state.url) {
            // Se a URL for do catálogo público ou orçamento, forçar carregamento completo da página
            if (isCatalogPublicUrl(e.state.url) || (typeof e.state.url === 'string' && e.state.url.includes('/orcamento'))) {
                window.location.href = e.state.url;
                return;
            }

            loadPage(e.state.url);
        } else {
            // Fallback para quando o estado é perdido
            loadPage(window.location.href);
        }
    });

    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupGlobalNavigation);
    } else {
        setupGlobalNavigation();
    }

    // Atualizar links ativos na inicialização
    updateActiveLink(window.location.href);

    // Expor função global para uso externo (caso necessário forçar reload)
    window.ajaxNavigation = {
        loadPage: loadPage,
        updateActiveLink: updateActiveLink
    };

})();

