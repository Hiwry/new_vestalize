/**
 * Global Utility Functions
 */

/**
 * Copies text to the clipboard and provides visual feedback.
 * @param {string} text - The text to copy.
 * @param {HTMLElement} element - The button element that triggered the copy.
 */
window.copyToClipboard = function(text, element) {
    if (!navigator.clipboard) {
        // Fallback for older browsers
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showFeedback(element);
        } catch (err) {
            console.error('Falha ao copiar:', err);
        }
        document.body.removeChild(textArea);
        return;
    }

    navigator.clipboard.writeText(text).then(function() {
        showFeedback(element);
    }, function(err) {
        console.error('Erro ao copiar para a área de transferência: ', err);
    });

    function showFeedback(el) {
        if (!el) return;
        
        const originalHTML = el.innerHTML;
        const originalText = el.textContent;
        
        // Simple visual feedback
        if (el.tagName === 'BUTTON') {
            const hasIcon = el.querySelector('svg, i');
            if (hasIcon) {
                el.innerHTML = '<i class="fas fa-check mr-1"></i> Copiado!';
            } else {
                el.textContent = 'Copiado!';
            }
            
            el.classList.add('bg-green-600', 'text-white');
            el.disabled = true;
            
            setTimeout(() => {
                el.innerHTML = originalHTML;
                el.classList.remove('bg-green-600', 'text-white');
                el.disabled = false;
            }, 2000);
        } else {
            alert('Copiado para a área de transferência!');
        }
    }
};
