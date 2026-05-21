// ARQUIVO: assets/js/categoria.js
// FUNÇÃO: Controla a abertura e fecho dos modais de descrição completa

/**
 * Abre o modal com a descrição completa do desejo
 * @param {number} id - O id do desejo (usado para encontrar o modal correto)
 */
function abrirModal(id) {
    const modal = document.getElementById('modal-' + id);
    if (modal) {
        modal.classList.add('ativo');
        document.body.style.overflow = 'hidden'; // impede scroll da página enquanto modal está aberto
    }
}

/**
 * Fecha o modal do desejo
 * @param {number} id - O id do desejo
 */
function fecharModal(id) {
    const modal = document.getElementById('modal-' + id);
    if (modal) {
        modal.classList.remove('ativo');
        document.body.style.overflow = ''; // restaura o scroll da página
    }
}

// Fecha qualquer modal aberto ao pressionar a tecla Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modaisAtivos = document.querySelectorAll('.modal-overlay.ativo');
        modaisAtivos.forEach(function (modal) {
            modal.classList.remove('ativo');
        });
        document.body.style.overflow = '';
    }
});