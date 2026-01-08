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
            const isActive = currentPath === linkPath ||
                (linkPath !== '/' && currentPath.startsWith(linkPath));

            // Atualizar classes
            if (isActive) {
                link.classList.remove('text-gray-700', 'dark:text-gray-300', 'hover:bg-blue-50', 'dark:hover:bg-gray-700', 'hover:text-blue-600', 'dark:hover:text-white');
                link.classList.add('bg-blue-600', 'text-white');

                // Atualizar ícone
                const icon = link.querySelector('svg');
                if (icon) {
                    icon.classList.remove('text-gray-500', 'dark:text-gray-400');
                    icon.classList.add('text-white');
                }
            } else {
                link.classList.remove('bg-blue-600', 'text-white');
                link.classList.add('text-gray-700', 'dark:text-gray-300', 'hover:bg-blue-50', 'dark:hover:bg-gray-700', 'hover:text-blue-600', 'dark:hover:text-white');

                // Atualizar ícone
                const icon = link.querySelector('svg');
                if (icon) {
                    icon.classList.remove('text-white');
                    icon.classList.add('text-gray-500', 'dark:text-gray-400');
                }
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

        // Nunca usar AJAX para o catálogo público (/catalogo)
        if (isCatalogPublicUrl(url)) {
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

        // Mostrar loading
        const originalContent = mainContent.innerHTML;
        const loadingHtml = `
            <div class="flex items-center justify-center min-h-[60vh]">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Carregando...</p>
                </div>
            </div>
        `;
        mainContent.innerHTML = loadingHtml;

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

            // Extrair conteúdo principal
            const newContent = extractMainContent(html);
            if (!newContent) {
                window.location.href = url;
                return;
            }

            // Atualizar conteúdo de forma que preserve e execute scripts
            // Primeiro, criar um container temporário
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newContent;

            // Coletar informações dos scripts ANTES de mover o conteúdo
            const scriptData = Array.from(tempDiv.querySelectorAll('script')).map(script => ({
                src: script.src,
                text: script.textContent,
                attributes: Array.from(script.attributes).map(attr => ({
                    name: attr.name,
                    value: attr.value
                })),
                async: script.async,
                defer: script.defer
            }));

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
                // Para scripts inline, envolver em um IIFE ou verificar se já existe
                if (!scriptInfo.src && scriptInfo.text) {
                    // Tentar executar o script em um escopo isolado
                    try {
                        // Criar um script com conteúdo modificado para evitar redeclarações
                        const scriptText = scriptInfo.text;

                        // Verificar se contém declarações const/let que podem causar conflito
                        const hasVariableDeclarations = /(?:^|\s)(const|let)\s+(\w+)/g.test(scriptText);

                        if (hasVariableDeclarations) {
                            // Envolver em um bloco para criar novo escopo
                            const wrappedScript = `(function() { ${scriptText} })();`;
                            const newScript = document.createElement('script');
                            newScript.textContent = wrappedScript;
                            document.body.appendChild(newScript);
                            // Remover após um tempo para evitar acúmulo
                            setTimeout(() => {
                                if (newScript.parentNode) {
                                    newScript.parentNode.removeChild(newScript);
                                }
                            }, 1000);
                        } else {
                            // Script sem declarações de variáveis, executar normalmente
                            const newScript = document.createElement('script');
                            newScript.textContent = scriptText;
                            document.body.appendChild(newScript);
                            setTimeout(() => {
                                if (newScript.parentNode) {
                                    newScript.parentNode.removeChild(newScript);
                                }
                            }, 1000);
                        }
                    } catch (error) {
                        console.warn('Erro ao executar script inline:', error);
                    }
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
            mainContent.innerHTML = originalContent;
            window.location.href = url;
        } finally {
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

    // Interceptar cliques nos links da sidebar
    function setupSidebarNavigation() {
        const sidebar = document.querySelector('#sidebar');
        if (!sidebar) {
            return;
        }

        // Usar delegação de eventos para links que podem ser adicionados dinamicamente
        sidebar.addEventListener('click', async (e) => {
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
                link.classList.contains('no-ajax') ||
                link.hasAttribute('download')) {
                return;
            }

            // Ignorar se for um link dentro de um formulário
            if (link.closest('form')) {
                return;
            }

            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('#')) {
                return;
            }

            // Verificar se é uma URL interna
            let url;
            try {
                url = new URL(href, window.location.origin);
                if (url.origin !== window.location.origin) {
                    return; // Link externo, deixar comportamento padrão
                }
            } catch (e) {
                return; // URL inválida
            }

            // Nunca usar AJAX para o catálogo público (/catalogo)
            if (isCatalogPublicUrl(url.href)) {
                return; // deixar o navegador fazer um load completo
            }

            // Verificar se já está na mesma URL
            const currentPath = window.location.pathname + window.location.search;
            const targetPath = url.pathname + url.search;
            if (currentPath === targetPath) {
                return; // Já está na mesma página
            }

            // Prevenir comportamento padrão
            e.preventDefault();
            e.stopPropagation();

            // Carregar página via AJAX
            await loadPage(url.href);
        }, true); // Usar capture phase para garantir que interceptamos antes de outros handlers
    }

    // Lidar com navegação do navegador (voltar/avançar)
    window.addEventListener('popstate', (e) => {
        if (e.state && e.state.url) {
            // Se a URL for do catálogo público, forçar carregamento completo da página
            if (isCatalogPublicUrl(e.state.url)) {
                window.location.href = e.state.url;
                return;
            }

            loadPage(e.state.url);
        } else {
            window.location.reload();
        }
    });

    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupSidebarNavigation);
    } else {
        setupSidebarNavigation();
    }

    // Atualizar links ativos na inicialização
    updateActiveLink(window.location.href);

    // Expor função global para uso externo (caso necessário forçar reload)
    window.ajaxNavigation = {
        loadPage: loadPage,
        updateActiveLink: updateActiveLink
    };

})();

