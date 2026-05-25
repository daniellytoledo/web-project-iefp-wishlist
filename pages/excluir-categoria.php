<?php
// ARQUIVO: pages/excluir-categoria.php
// FUNÇÃO: Página para excluir uma categoria do banco de dados

require_once '../config/database.php';

$mensagem = '';
$tipo_mensagem = '';

// Verifica se o formulário de exclusão foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_categoria = (int) $_POST['id_categoria'];

    // Busca o nome da categoria antes de excluir (para mostrar na mensagem)
    $sql_nome = "SELECT nome_c FROM categorias WHERE id_c = :id";
    $stmt = $pdo->prepare($sql_nome);
    $stmt->execute([':id' => $id_categoria]);
    $categoria = $stmt->fetch();

    if ($categoria) {
        // Exclui a categoria do banco de dados
        $sql_excluir = "DELETE FROM categorias WHERE id_c = :id";
        $stmt = $pdo->prepare($sql_excluir);
        $stmt->execute([':id' => $id_categoria]);

        $mensagem = 'A categoria "' . htmlspecialchars($categoria['nome_c']) . '" foi excluída com sucesso.';
        $tipo_mensagem = 'sucesso';
    } else {
        $mensagem = 'Categoria não encontrada.';
        $tipo_mensagem = 'erro';
    }
}

// Busca todas as categorias disponíveis para preencher o select
$sql_categorias = "SELECT * FROM categorias ORDER BY nome_c ASC";
$stmt = $pdo->query($sql_categorias);
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Categoria — Wishlist</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/excluir-categoria.css">
</head>
<body>

    <!-- ========== NAVEGAÇÃO ========== -->
    <nav class="navbar">
        <a href="../index.php" class="nav-logo">wishlist</a>
        <ul class="nav-links">
            <li><a href="nova-categoria.php">+ Nova Categoria</a></li>
            <li><a href="excluir-categoria.php" class="nav-link-ativo">− Excluir Categoria</a></li>
        </ul>
    </nav>

    <!-- ========== CONTEÚDO PRINCIPAL ========== -->
    <main class="main-content">

        <div class="form-container">

            <h1 class="form-titulo">Excluir Categoria</h1>
            <p class="form-subtitulo">Seleciona a categoria que desejas remover.</p>

            <!-- Mensagem de feedback (sucesso ou erro) -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?= $tipo_mensagem ?>">
                    <?= $mensagem ?>
                    <?php if ($tipo_mensagem === 'sucesso'): ?>
                        <span class="mensagem-links">
                            <a href="../index.php">← Voltar ao início</a> ou
                            <a href="excluir-categoria.php">excluir outra</a>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Formulário de exclusão -->
            <form method="POST" action="excluir-categoria.php" class="form-categoria" id="form-excluir">

                <div class="campo-grupo">
                    <label for="id_categoria" class="campo-label">Categoria</label>

                    <?php if (empty($categorias)): ?>
                        <!-- Mensagem caso não haja categorias no banco -->
                        <p class="sem-categorias">Nenhuma categoria disponível para excluir.</p>
                    <?php else: ?>
                        <!-- Select com todas as categorias vindas do banco -->
                        <select name="id_categoria" id="id_categoria" class="campo-select">
                            <option value="" disabled selected>Escolhe uma categoria...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_c'] ?>">
                                    <?= htmlspecialchars($cat['nome_c']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Botões de ação -->
                <div class="form-acoes">
                    <a href="../index.php" class="btn-cancelar">Cancelar</a>
                    <?php if (!empty($categorias)): ?>
                        <!-- O botão confirmar aciona o modal de confirmação via JS -->
                        <button type="button" class="btn-excluir" onclick="abrirConfirmacao()">
                            Confirmar Exclusão
                        </button>
                    <?php endif; ?>
                </div>

            </form>

        </div>

    </main>

    <!-- ========== JANELA FLUTUANTE DE CONFIRMAÇÃO ========== -->
    <!-- Só aparece quando o utilizador clica em "Confirmar Exclusão" -->
    <div class="modal-overlay" id="modal-confirmacao" onclick="fecharConfirmacao()">
        <div class="modal-caixa" onclick="event.stopPropagation()">

            <!-- Ícone de aviso -->
            <div class="modal-icone">!</div>

            <h3 class="modal-titulo">Tens a certeza?</h3>
            <p class="modal-texto">
                Esta ação é <strong>irreversível</strong>. A categoria será permanentemente removida do banco de dados.
            </p>

            <div class="modal-acoes">
                <!-- Cancela e fecha o modal -->
                <button type="button" class="btn-cancelar" onclick="fecharConfirmacao()">
                    Não, cancelar
                </button>
                <!-- Submete o formulário de exclusão -->
                <button type="button" class="btn-excluir-confirmar" onclick="confirmarExclusao()">
                    Sim, excluir
                </button>
            </div>

        </div>
    </div>

    <!-- ========== require puxando o arquivo footer ========== -->
    <?php require_once '../includes/footer.php' ?>

    <script src="../assets/js/excluir-categoria.js"></script>

</body>
</html>