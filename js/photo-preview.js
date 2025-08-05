/**
 * Sistema de Preview de Fotos para ShieldTech
 * Permite visualizar fotos antes de salvar
 */

class PhotoPreview {
    /**
     * Configura preview de foto para um campo
     * @param {string} inputId - ID do campo de input
     * @param {string} previewId - ID do elemento de preview
     * @param {string} imgId - ID da imagem de preview
     */
    static setupPreview(inputId, previewId, imgId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const img = document.getElementById(imgId);

        if (!input || !preview || !img) return;

        let previewTimeout;

        input.addEventListener('input', function() {
            const url = this.value.trim();
            
            // Limpar timeout anterior
            clearTimeout(previewTimeout);
            
            if (!url) {
                preview.style.display = 'none';
                return;
            }

            // Validar se é uma URL válida
            if (!PhotoPreview.isValidImageUrl(url)) {
                preview.style.display = 'none';
                return;
            }

            // Aguardar um pouco antes de carregar a imagem
            previewTimeout = setTimeout(() => {
                PhotoPreview.loadImagePreview(url, preview, img);
            }, 500);
        });
    }

    /**
     * Verifica se a URL parece ser de uma imagem
     * @param {string} url - URL a ser verificada
     * @returns {boolean}
     */
    static isValidImageUrl(url) {
        try {
            new URL(url);
            // Verificar extensões comuns de imagem
            const imageExtensions = /\.(jpg|jpeg|png|gif|bmp|webp|svg)(\?.*)?$/i;
            return imageExtensions.test(url) || 
                   url.includes('drive.google.com') || 
                   url.includes('dropbox.com') ||
                   url.includes('imgur.com') ||
                   url.includes('cloudinary.com');
        } catch {
            return false;
        }
    }

    /**
     * Carrega preview da imagem
     * @param {string} url - URL da imagem
     * @param {HTMLElement} preview - Elemento de preview
     * @param {HTMLElement} img - Elemento da imagem
     */
    static loadImagePreview(url, preview, img) {
        // Mostrar loading
        preview.style.display = 'block';
        img.style.opacity = '0.5';
        img.src = '';
        
        // Criar nova imagem para testar carregamento
        const testImg = new Image();
        
        testImg.onload = function() {
            img.src = url;
            img.style.opacity = '1';
            img.style.border = '2px solid #28a745';
            
            // Adicionar feedback visual de sucesso
            const successIcon = document.createElement('div');
            successIcon.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745; position: absolute; top: -5px; right: -5px; background: white; border-radius: 50%; padding: 2px;"></i>';
            successIcon.style.position = 'relative';
            
            // Remover ícone após 3 segundos
            setTimeout(() => {
                img.style.border = '2px solid #3498db';
            }, 3000);
        };
        
        testImg.onerror = function() {
            preview.style.display = 'none';
            
            // Mostrar mensagem de erro temporária
            const errorMsg = document.createElement('div');
            errorMsg.style.cssText = `
                color: #dc3545;
                font-size: 0.8em;
                margin-top: 0.25rem;
                padding: 0.25rem;
                background: #f8d7da;
                border-radius: 4px;
                border: 1px solid #f5c6cb;
            `;
            errorMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Não foi possível carregar a imagem. Verifique se a URL está correta.';
            
            const input = document.getElementById(preview.previousElementSibling?.id || '');
            if (input) {
                input.parentNode.appendChild(errorMsg);
                
                // Remover mensagem após 5 segundos
                setTimeout(() => {
                    if (errorMsg.parentNode) {
                        errorMsg.remove();
                    }
                }, 5000);
            }
        };
        
        testImg.src = url;
    }

    /**
     * Inicializa preview para todos os campos de foto na página
     */
    static initializeAll() {
        // Configurar para campo de foto padrão
        this.setupPreview('foto', 'foto-preview', 'preview-img');
        
        // Configurar para outros campos se existirem
        const photoInputs = document.querySelectorAll('input[name*="foto"], input[id*="foto"]');
        photoInputs.forEach((input, index) => {
            const previewId = `foto-preview-${index}`;
            const imgId = `preview-img-${index}`;
            
            // Criar elementos de preview se não existirem
            if (!document.getElementById(previewId)) {
                const preview = document.createElement('div');
                preview.id = previewId;
                preview.style.cssText = 'margin-top: 0.5rem; display: none;';
                
                const img = document.createElement('img');
                img.id = imgId;
                img.style.cssText = 'width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #3498db;';
                
                preview.appendChild(img);
                input.parentNode.appendChild(preview);
                
                this.setupPreview(input.id, previewId, imgId);
            }
        });
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    PhotoPreview.initializeAll();
});

// Exportar para uso global
window.PhotoPreview = PhotoPreview;