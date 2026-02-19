/**
 * Sistema de Upload por Ctrl+V (Colar da Área de Transferência)
 * Suporta imagens e arquivos
 */

class PasteUpload {
    constructor(inputElement, options = {}) {
        this.input = inputElement;
        this.options = {
            acceptImages: options.acceptImages !== false, // padrão: true
            acceptFiles: options.acceptFiles || false, // padrão: false
            maxSize: options.maxSize || 10, // MB
            allowedExtensions: options.allowedExtensions || ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'cdr', 'ai', 'svg'],
            previewContainer: options.previewContainer || null,
            onPaste: options.onPaste || null,
            onError: options.onError || null,
            multiple: options.multiple !== false, // padrão: true
        };
        
        this.files = [];
        this.init();
    }

    init() {
        // Criar área de drop visual se não existir
        this.createDropZone();
        
        // Eventos de paste
        document.addEventListener('paste', (e) => this.handlePaste(e));
        
        // Eventos de drag & drop
        this.dropZone.addEventListener('dragover', (e) => this.handleDragOver(e));
        this.dropZone.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        this.dropZone.addEventListener('drop', (e) => this.handleDrop(e));
        
        // Click para abrir seletor de arquivos
        this.dropZone.addEventListener('click', () => this.input.click());
        
        // Mudança no input file normal
        this.input.addEventListener('change', (e) => this.handleFileSelect(e));
    }

    createDropZone() {
        // Procurar por drop zone existente próximo ao input
        let dropZone = this.input.parentElement.querySelector('.paste-drop-zone');
        
        if (!dropZone) {
            // Criar drop zone se não existir
            dropZone = document.createElement('div');
            dropZone.className = 'paste-drop-zone';
            dropZone.innerHTML = `
                <div class="paste-drop-content">
                    <svg class="paste-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="paste-text">
                        <span class="paste-primary">Clique, arraste ou <kbd>Ctrl+V</kbd></span>
                        <span class="paste-secondary">para adicionar arquivos</span>
                    </p>
                </div>
                <div class="paste-preview"></div>
            `;
            
            // Inserir após o input (que geralmente está escondido)
            if (this.input.nextSibling) {
                this.input.parentElement.insertBefore(dropZone, this.input.nextSibling);
            } else {
                this.input.parentElement.appendChild(dropZone);
            }
        }
        
        this.dropZone = dropZone;
        this.previewContainer = dropZone.querySelector('.paste-preview');
    }

    handlePaste(e) {
        // Verificar se estamos focados perto do input
        const isNearInput = this.input.parentElement.contains(e.target) || 
                           document.activeElement === document.body ||
                           e.target.closest('.paste-drop-zone');
        
        if (!isNearInput && !this.options.globalPaste) return;

        const items = e.clipboardData?.items;
        if (!items) return;

        let hasFiles = false;
        
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            
            // Verificar se é arquivo/imagem
            if (item.kind === 'file') {
                e.preventDefault();
                hasFiles = true;
                
                const file = item.getAsFile();
                if (file) {
                    this.addFile(file);
                }
            }
        }

        if (hasFiles) {
            this.updatePreview();
            if (this.options.onPaste) {
                this.options.onPaste(this.files);
            }
        }
    }

    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        this.dropZone.classList.add('drag-over');
    }

    handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        this.dropZone.classList.remove('drag-over');
    }

    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.dropZone.classList.remove('drag-over');

        const files = e.dataTransfer.files;
        for (let i = 0; i < files.length; i++) {
            this.addFile(files[i]);
        }

        this.updatePreview();
    }

    handleFileSelect(e) {
        const files = e.target.files;
        for (let i = 0; i < files.length; i++) {
            this.addFile(files[i]);
        }
        this.updatePreview();
    }

    addFile(file) {
        // Validar tipo de arquivo
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (!this.options.allowedExtensions.includes(extension)) {
            this.showError(`Tipo de arquivo não permitido: .${extension}`);
            return false;
        }

        // Validar tamanho
        const sizeMB = file.size / 1024 / 1024;
        if (sizeMB > this.options.maxSize) {
            this.showError(`Arquivo muito grande: ${file.name} (${sizeMB.toFixed(2)}MB). Máximo: ${this.options.maxSize}MB`);
            return false;
        }

        // Se não for múltiplo, limpar arquivos anteriores
        if (!this.options.multiple) {
            this.files = [];
        }

        this.files.push(file);
        return true;
    }

    updatePreview() {
        if (!this.previewContainer) return;

        this.previewContainer.innerHTML = '';
        
        if (this.files.length === 0) return;

        this.files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'paste-file-item';
            
            const isImage = file.type.startsWith('image/');
            
            if (isImage) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    fileItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}" class="paste-file-thumb">
                        <div class="paste-file-info">
                            <span class="paste-file-name">${file.name}</span>
                            <span class="paste-file-size">${this.formatFileSize(file.size)}</span>
                        </div>
                        <button type="button" class="paste-file-remove" data-index="${index}">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                fileItem.innerHTML = `
                    <div class="paste-file-icon">
                        ${this.getFileIcon(file.name)}
                    </div>
                    <div class="paste-file-info">
                        <span class="paste-file-name">${file.name}</span>
                        <span class="paste-file-size">${this.formatFileSize(file.size)}</span>
                    </div>
                    <button type="button" class="paste-file-remove" data-index="${index}">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                `;
            }
            
            this.previewContainer.appendChild(fileItem);
        });

        // Adicionar eventos de remoção
        this.previewContainer.querySelectorAll('.paste-file-remove').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const index = parseInt(btn.dataset.index);
                this.removeFile(index);
            });
        });

        // Atualizar o input file com os arquivos
        this.updateInputFiles();
    }

    updateInputFiles() {
        // Criar um DataTransfer para adicionar os arquivos ao input
        const dataTransfer = new DataTransfer();
        
        this.files.forEach(file => {
            dataTransfer.items.add(file);
        });

        this.input.files = dataTransfer.files;
        
        // Disparar evento change
        this.input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    removeFile(index) {
        this.files.splice(index, 1);
        this.updatePreview();
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
            doc: '📝',
            docx: '📝',
            xls: '📊',
            xlsx: '📊',
        };
        return icons[ext] || '📎';
    }

    showError(message) {
        if (this.options.onError) {
            this.options.onError(message);
        } else {
            // Criar notificação de erro
            const errorDiv = document.createElement('div');
            errorDiv.className = 'paste-error-toast';
            errorDiv.innerHTML = `
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>${message}</span>
            `;
            document.body.appendChild(errorDiv);
            
            setTimeout(() => {
                errorDiv.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                errorDiv.classList.remove('show');
                setTimeout(() => errorDiv.remove(), 300);
            }, 4000);
        }
    }

    getFiles() {
        return this.files;
    }

    clear() {
        this.files = [];
        this.updatePreview();
    }
}

// Exportar para uso global
window.PasteUpload = PasteUpload;

// Função helper para inicializar automaticamente
window.initPasteUpload = function(selector, options = {}) {
    const elements = document.querySelectorAll(selector);
    const instances = [];
    
    elements.forEach(element => {
        instances.push(new PasteUpload(element, options));
    });
    
    return instances.length === 1 ? instances[0] : instances;
};

