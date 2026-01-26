/**
 * Modal de Upload por Ctrl+V
 * Interface visual para colar arquivos da área de transferência
 */

class PasteModal {
    constructor() {
        this.modal = null;
        this.currentInput = null;
        this.files = [];
        this.createModal();
        this.attachGlobalListeners();
    }

    createModal() {
        const modalHTML = `
            <div id="paste-modal" class="fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
                <div class="paste-modal-overlay absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                <div class="paste-modal-content relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-[90%] max-w-xl max-h-[80vh] flex flex-col transform scale-95 transition-transform duration-300">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white m-0">Adicionar Arquivos</h3>
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" onclick="window.pasteModal.close()">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="p-6 overflow-y-auto flex-1">
                        <div class="paste-zone border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center bg-gray-50 dark:bg-gray-900/50 hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-all cursor-pointer" id="paste-zone">
                            <div class="mb-4 text-gray-400 dark:text-gray-600">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="mb-6">
                                <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 paste-zone-primary">
                                    Use <kbd class="px-2 py-1 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded">Ctrl+V</kbd> para colar
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 paste-zone-secondary">
                                    Ou arraste arquivos aqui
                                </p>
                            </div>
                            <button type="button" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white !text-white stay-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg" onclick="document.getElementById('paste-modal-file-input').click()">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Selecionar Arquivos
                            </button>
                            <input type="file" id="paste-modal-file-input" class="hidden" multiple>
                        </div>
                        
                        <div id="paste-files-preview" class="grid grid-cols-2 gap-4 mt-6"></div>
                    </div>
                    
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3 bg-gray-50 dark:bg-gray-900/30 rounded-b-2xl">
                        <button type="button" class="px-6 py-3 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="window.pasteModal.close()">
                            Cancelar
                        </button>
                        <button type="button" class="inline-flex items-center px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white !text-white stay-white font-semibold rounded-lg transition-colors shadow-md hover:shadow-lg" onclick="window.pasteModal.confirm()">
                            Adicionar <span id="paste-file-count" class="ml-1"></span>
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('paste-modal');
        this.pasteZone = document.getElementById('paste-zone');
        this.preview = document.getElementById('paste-files-preview');
        this.fileCount = document.getElementById('paste-file-count');
        
        this.attachModalListeners();
    }

    attachGlobalListeners() {
        // Listeners globais já configurados no attachModalListeners
    }

    attachModalListeners() {
        // Fechar ao clicar no overlay
        this.modal.querySelector('.paste-modal-overlay').addEventListener('click', () => this.close());
        
        // Eventos de paste
        document.addEventListener('paste', (e) => {
            if (!this.modal.classList.contains('hidden')) {
                this.handlePaste(e);
            }
        });
        
        // Drag & drop
        this.pasteZone.addEventListener('dragover', (e) => this.handleDragOver(e));
        this.pasteZone.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        this.pasteZone.addEventListener('drop', (e) => this.handleDrop(e));
        
        // Seleção de arquivo tradicional
        document.getElementById('paste-modal-file-input').addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            files.forEach(file => this.addFile(file));
            this.updatePreview();
        });
        
        // ESC para fechar
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.modal.classList.contains('hidden')) {
                this.close();
            }
        });
    }

    open(inputElement) {
        this.currentInput = inputElement;
        this.files = [];
        this.updatePreview();
        
        // Remover classes de hidden
        this.modal.classList.remove('opacity-0', 'pointer-events-none', 'hidden');
        
        // Atualizar título do modal baseado no tipo
        const isImagesOnly = inputElement.dataset.pasteImagesOnly === 'true';
        const modalTitle = this.modal.querySelector('h3');
        modalTitle.textContent = isImagesOnly ? 'Adicionar Imagens' : 'Adicionar Arquivos';
        
        // Atualizar texto da zona de paste
        const primaryText = this.pasteZone.querySelector('.paste-zone-primary');
        primaryText.innerHTML = isImagesOnly 
            ? 'Use <kbd class="px-2 py-1 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded">Ctrl+V</kbd> para colar imagens'
            : 'Use <kbd class="px-2 py-1 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded">Ctrl+V</kbd> para colar arquivos';
        
        // Focar na zona de paste
        setTimeout(() => {
            this.pasteZone.focus();
        }, 100);
        
        // Adicionar classe para animação
        const modalContent = this.modal.querySelector('.paste-modal-content');
        setTimeout(() => {
             modalContent.classList.remove('scale-95');
             modalContent.classList.add('scale-100');
        }, 10);
    }

    close() {
        const modalContent = this.modal.querySelector('.paste-modal-content');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        
        this.modal.classList.add('opacity-0', 'pointer-events-none');
        
        setTimeout(() => {
            this.modal.classList.add('hidden');
            this.files = [];
            this.currentInput = null;
            this.updatePreview();
        }, 300);
    }

    handlePaste(e) {
        const items = e.clipboardData?.items;
        if (!items) return;

        e.preventDefault();
        let hasFiles = false;

        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            
            if (item.kind === 'file') {
                hasFiles = true;
                const file = item.getAsFile();
                if (file) {
                    this.addFile(file);
                }
            }
        }

        if (hasFiles) {
            this.updatePreview();
            this.showNotification('✅ Arquivo(s) colado(s) com sucesso!', 'success');
        }
    }

    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        this.pasteZone.classList.add('drag-over');
    }

    handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        this.pasteZone.classList.remove('drag-over');
    }

    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.pasteZone.classList.remove('drag-over');

        const files = Array.from(e.dataTransfer.files);
        files.forEach(file => this.addFile(file));
        this.updatePreview();
    }

    addFile(file) {
        // Validar tamanho (usar data-attribute ou padrão)
        const maxSize = parseInt(this.currentInput?.dataset.pasteMaxSize) || 
                       (this.currentInput?.dataset.pasteImagesOnly === 'true' ? 5 : 10);
        const sizeMB = file.size / 1024 / 1024;
        
        if (sizeMB > maxSize) {
            this.showNotification(`❌ Arquivo muito grande: ${file.name} (${sizeMB.toFixed(2)}MB). Máximo: ${maxSize}MB`, 'error');
            return;
        }

        // Validar extensão (usar data-attribute ou padrão)
        const extension = file.name.split('.').pop().toLowerCase();
        const extensionsAttr = this.currentInput?.dataset.pasteExtensions;
        const allowedExtensions = extensionsAttr 
            ? extensionsAttr.split(',').map(ext => ext.trim().toLowerCase())
            : (this.currentInput?.dataset.pasteImagesOnly === 'true' 
                ? ['jpg', 'jpeg', 'png', 'gif']
                : ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'cdr', 'ai', 'svg']);
        
        if (!allowedExtensions.includes(extension)) {
            this.showNotification(`❌ Tipo de arquivo não permitido: .${extension}. Permitidos: ${allowedExtensions.join(', ')}`, 'error');
            return;
        }

        this.files.push(file);
    }

    updatePreview() {
        this.preview.innerHTML = '';
        
        if (this.files.length === 0) {
            this.fileCount.textContent = '';
            return;
        }

        this.fileCount.textContent = `(${this.files.length})`;

        this.files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'relative flex flex-col p-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-500 dark:hover:border-indigo-500 transition-all group shadow-sm';
            
            const isImage = file.type.startsWith('image/');

            const removeButtonHtml = `
                <button type="button" class="absolute -top-2 -right-2 w-6 h-6 flex items-center justify-center bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-md hover:bg-red-600 hover:scale-110 transform" onclick="window.pasteModal.removeFile(${index})">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            `;
            
            if (isImage) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    fileItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}" class="w-full h-24 object-cover rounded mb-2 bg-gray-100 dark:bg-gray-700">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate" title="${file.name}">${file.name}</span>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-mono">${this.formatFileSize(file.size)}</span>
                        </div>
                        ${removeButtonHtml}
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                fileItem.innerHTML = `
                    <div class="w-full h-24 flex items-center justify-center text-4xl bg-gray-100 dark:bg-gray-700 rounded mb-2 text-gray-500 dark:text-gray-400">
                        ${this.getFileIcon(file.name)}
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate" title="${file.name}">${file.name}</span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 font-mono">${this.formatFileSize(file.size)}</span>
                    </div>
                    ${removeButtonHtml}
                `;
            }
            
            this.preview.appendChild(fileItem);
        });
    }

    removeFile(index) {
        this.files.splice(index, 1);
        this.updatePreview();
    }

    confirm() {
        if (!this.currentInput || this.files.length === 0) {
            // Tentar usar o sistema global de notificação se disponível
            if (typeof notify === 'function') {
                notify('Nenhum arquivo selecionado', 'error');
            } else {
                this.showNotification('❌ Nenhum arquivo selecionado', 'error');
            }
            return;
        }

        // Transferir arquivos para o input
        const dataTransfer = new DataTransfer();
        
        // Se o input já tem arquivos, manter os existentes (para múltiplos uploads)
        if (this.currentInput.files && this.currentInput.files.length > 0) {
            Array.from(this.currentInput.files).forEach(file => {
                dataTransfer.items.add(file);
            });
        }
        
        // Adicionar novos arquivos
        this.files.forEach(file => {
            dataTransfer.items.add(file);
        });

        this.currentInput.files = dataTransfer.files;
        
        // Disparar evento change
        this.currentInput.dispatchEvent(new Event('change', { bubbles: true }));
        
        if (typeof notify === 'function') {
            notify(`✅ ${this.files.length} arquivo(s) adicionado(s)!`, 'success');
        } else {
            this.showNotification(`✅ ${this.files.length} arquivo(s) adicionado(s)!`, 'success');
        }
        
        this.close();
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            pdf: '<i class="fa-solid fa-file-pdf"></i>',
            cdr: '<i class="fa-solid fa-file-pen"></i>',
            ai: '<i class="fa-solid fa-file-pen"></i>',
            svg: '<i class="fa-solid fa-image"></i>',
            png: '<i class="fa-solid fa-image"></i>',
            jpg: '<i class="fa-solid fa-image"></i>',
            jpeg: '<i class="fa-solid fa-image"></i>',
            webp: '<i class="fa-solid fa-image"></i>',
        };
        return icons[ext] || '<i class="fa-solid fa-paperclip"></i>';
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `paste-notification paste-notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 10);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Inicializar automaticamente
window.addEventListener('DOMContentLoaded', () => {
    window.pasteModal = new PasteModal();
});

