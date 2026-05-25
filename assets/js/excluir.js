// FUNÇÃO: Controla o modal de confirmação antes de excluir uma categoria

/**
 * Abre o modal de confirmação ao clicar em "Confirmar Exclusão"
 * Antes de abrir, verifica se o utilizador selecionou uma categoria
 */
function abrirConfirmacao() {
    const select = document.getElementById('id_categoria');

    // Verifica se uma categoria foi selecionada no campo select
    if (!select.value) {
        // Destaca o select visualmente para indicar que é obrigatório
        select.style.borderColor = 'rgba(220, 80, 80, 0.6)';

        // Remove o destaque após 2 segundos
        setTimeout(function () {
            select.style.borderColor = '';
        }, 2000);

        return; // Interrompe a função — não abre o modal
    }

    // Se uma categoria está selecionada, abre o modal
    const modal = document.getElementById('modal-confirmacao');
    modal.classList.add('ativo');

    // Impede o scroll da página enquanto o modal está aberto
    document.body.style.overflow = 'hidden';
}

/**
 * Fecha o modal de confirmação sem excluir nada
 */
function fecharConfirmacao() {
    const modal = document.getElementById('modal-confirmacao');
    modal.classList.remove('ativo');

    // Restaura o scroll da página
    document.body.style.overflow = '';
}

/**
 * Confirma a exclusão: submete o formulário principal
 * Esta função só é chamada quando o utilizador clica em "Sim, excluir" no modal
 */
function confirmarExclusao() {
    // Fecha o modal antes de submeter
    fecharConfirmacao();

    // Submete o formulário de exclusão para o PHP processar
    document.getElementById('form-excluir').submit();
}

// Fecha o modal ao pressionar a tecla Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        fecharConfirmacao();
    }
});