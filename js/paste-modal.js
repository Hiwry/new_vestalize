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
            <div id="paste-modal" class="paste-modal hidden">
                <div class="paste-modal-overlay"></div>
                <div class="paste-modal-content">
                    <div class="paste-modal-header">
                        <h3>📋 Adicionar Arquivos</h3>
                        <button type="button" class="paste-modal-close" onclick="window.pasteModal.close()">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="paste-modal-body">
                        <div class="paste-zone" id="paste-zone">
                            <div class="paste-zone-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="paste-zone-text">
                                <p class="paste-zone-primary">
                                    Use <kbd>Ctrl+V</kbd> para colar
                                </p>
                                <p class="paste-zone-secondary">
                                    Ou arraste arquivos aqui
                                </p>
                            </div>
                            <button type="button" class="paste-zone-button" onclick="document.getElementById('paste-modal-file-input').click()">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Selecionar Arquivos
                            </button>
                            <input type="file" id="paste-modal-file-input" class="hidden" multiple>
                        </div>
                        
                        <div id="paste-files-preview" class="paste-files-preview"></div>
                    </div>
                    
                    <div class="paste-modal-footer">
                        <button type="button" class="paste-btn-secondary" onclick="window.pasteModal.close()">
                            Cancelar
                        </button>
                        <button type="button" class="paste-btn-primary" onclick="window.pasteModal.confirm()">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Adicionar <span id="paste-file-count"></span>
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
        this.modal.classList.remove('hidden');
        
        // Atualizar título do modal baseado no tipo
        const isImagesOnly = inputElement.dataset.pasteImagesOnly === 'true';
        const modalTitle = this.modal.querySelector('.paste-modal-header h3');
        modalTitle.textContent = isImagesOnly ? '🖼️ Adicionar Imagens' : '📋 Adicionar Arquivos';
        
        // Atualizar texto da zona de paste
        const primaryText = this.pasteZone.querySelector('.paste-zone-primary');
        primaryText.innerHTML = isImagesOnly 
            ? 'Use <kbd>Ctrl+V</kbd> para colar imagens'
            : 'Use <kbd>Ctrl+V</kbd> para colar arquivos';
        
        // Focar na zona de paste
        setTimeout(() => {
            this.pasteZone.focus();
        }, 100);
        
        // Adicionar classe para animação
        setTimeout(() => {
            this.modal.classList.add('show');
        }, 10);
    }

    close() {
        this.modal.classList.remove('show');
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
            fileItem.className = 'paste-file-preview-item';
            
            const isImage = file.type.startsWith('image/');
            
            if (isImage) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    fileItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <div class="paste-file-preview-info">
                            <span class="paste-file-preview-name">${file.name}</span>
                            <span class="paste-file-preview-size">${this.formatFileSize(file.size)}</span>
                        </div>
                        <button type="button" class="paste-file-preview-remove" onclick="window.pasteModal.removeFile(${index})">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                fileItem.innerHTML = `
                    <div class="paste-file-preview-icon">
                        ${this.getFileIcon(file.name)}
                    </div>
                    <div class="paste-file-preview-info">
                        <span class="paste-file-preview-name">${file.name}</span>
                        <span class="paste-file-preview-size">${this.formatFileSize(file.size)}</span>
                    </div>
                    <button type="button" class="paste-file-preview-remove" onclick="window.pasteModal.removeFile(${index})">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
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
            this.showNotification('❌ Nenhum arquivo selecionado', 'error');
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
        
        this.showNotification(`✅ ${this.files.length} arquivo(s) adicionado(s)!`, 'success');
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
            pdf: '📄',
            cdr: '🎨',
            ai: '🎨',
            svg: '🖼️',
        };
        return icons[ext] || '📎';
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

