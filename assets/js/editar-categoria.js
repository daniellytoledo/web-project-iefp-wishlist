// ARQUIVO: assets/js/editar-categoria.js
// FUNÇÃO: Pré-preenche o campo "Novo Nome" com o nome atual da categoria selecionada

/**
 * Chamada pelo onchange do select de categorias
 * Lê o nome atual da categoria selecionada e preenche o campo de texto
 * @param {HTMLSelectElement} select - O elemento select que foi alterado
 */
function preencherNomeAtual(select) {

    // Lê o objeto JSON guardado no atributo data-nomes do select
    // Formato: { "1": "Tecnologia", "2": "Fotografia", ... }
    const nomes = JSON.parse(select.getAttribute('data-nomes'));

    // Obtém o id da categoria selecionada
    const idSelecionado = select.value;

    // Encontra o nome atual da categoria pelo id
    const nomeAtual = nomes[idSelecionado];

    // Referências aos elementos do DOM
    const campoInput  = document.getElementById('novo_nome');
    const grupoNome   = document.getElementById('grupo-novo-nome');
    const labelAtual  = document.getElementById('nome-atual');

    // Pré-preenche o campo com o nome atual para a utilizadora ver o que vai editar
    campoInput.value = nomeAtual;

    // Atualiza o texto de referência ao lado do label
    labelAtual.textContent = '(atual: ' + nomeAtual + ')';

    // Mostra o campo "Novo Nome" com animação (adiciona a classe CSS)
    grupoNome.classList.add('visivel');

    // Coloca o foco no campo de texto e seleciona o texto para edição rápida
    campoInput.focus();
    campoInput.select();
}