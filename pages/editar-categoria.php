<?php

require_once '../config/database.php';

$mensagem = '';
$tipo_mensagem = '';

// verifica se o formulário de edição foi submetido
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_categoria = (int) $_POST['id_categoria'];
    $novo_nome    = trim($_POST['novo_nome']);

    // valida se o campo do novo nome não está vazio
    if(empty($novo_nome)) {
        $mensagem      = 'Por favor, escreva o novo nome da categoria!';
        $tipo_mensagem = 'erro';
    } else {
        // atualiza o nome da categoria no banco de dados
        // usa prepared statement para evitar SQL Injection
        $sql   = "UPDATE categorias SET nome_c = :nome WHERE id_c = :id";
        $smtm  = $pdo->prepare($sql);
        $smtm->execute([
            ':nome' => $novo_nome,
            ':id'   => $id_categoria,
        ]);

        $mensagem      = 'Categoria atualizada para "' . htmlspecialchars($novo_nome) . '" com sucesso!';
        $tipo_mensagem = 'sucesso';
    }
}

// busca todas as categorias para preencher o select
$sql_categoria = "SELECT * FROM categorias ORDER BY nome_c ASC";
$smtm          = $pdo->query($sql_categoria);
$categorias    = $smtm->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria — Wishlist</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/editar-categoria.css">
</head>
<body>
 
    <!-- ========== NAVEGAÇÃO ========== -->
    <nav class="navbar">
        <a href="../index.php" class="nav-logo">wishlist</a>
        <ul class="nav-links">
            <li><a href="nova-categoria.php">+ Nova Categoria</a></li>
            <li><a href="excluir-categoria.php">− Excluir Categoria</a></li>
        </ul>
    </nav>
 
    <!-- ========== CONTEÚDO PRINCIPAL ========== -->
    <main class="main-content">
 
        <div class="form-container">
 
            <h1 class="form-titulo">Editar Categoria</h1>
            <p class="form-subtitulo">Seleciona a categoria e escreve o novo nome.</p>
 
            <!-- Mensagem de feedback (sucesso ou erro) -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?= $tipo_mensagem ?>">
                    <?= $mensagem ?>
                    <?php if ($tipo_mensagem === 'sucesso'): ?>
                        <span class="mensagem-links">
                            <a href="../index.php">← Voltar ao início</a> ou
                            <a href="editar-categoria.php">editar outra</a>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
 
            <!-- Formulário de edição -->
            <form method="POST" action="editar-categoria.php" class="form-categoria" id="form-editar">
 
                <!-- Campo para escolher qual categoria editar -->
                <div class="campo-grupo">
                    <label for="id_categoria" class="campo-label">Categoria</label>
 
                    <?php if (empty($categorias)): ?>
                        <p class="sem-categorias">Nenhuma categoria disponível para editar.</p>
                    <?php else: ?>
                        <!-- data-nomes guarda todos os nomes em JSON para o JS pré-preencher o campo novo nome -->
                        <select
                            name="id_categoria"
                            id="id_categoria"
                            class="campo-select"
                            onchange="preencherNomeAtual(this)"
                            data-nomes='<?= json_encode(array_column($categorias, 'nome_c', 'id_c')) ?>'
                        >
                            <option value="" disabled selected>Escolhe uma categoria...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_c'] ?>"
                                    <?= (isset($_POST['id_categoria']) && $_POST['id_categoria'] == $cat['id_c']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome_c']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
 
                <!-- Campo para escrever o novo nome — aparece após escolher a categoria -->
                <div class="campo-grupo campo-novo-nome" id="grupo-novo-nome">
                    <label for="novo_nome" class="campo-label">
                        Novo Nome
                        <!-- Mostra o nome atual como referência -->
                        <span class="nome-atual" id="nome-atual"></span>
                    </label>
                    <input
                        type="text"
                        id="novo_nome"
                        name="novo_nome"
                        class="campo-input"
                        placeholder="Escreve o novo nome..."
                        maxlength="50"
                        autocomplete="off"
                        value="<?= (isset($_POST['novo_nome']) && $tipo_mensagem === 'erro') ? htmlspecialchars($_POST['novo_nome']) : '' ?>"
                    >
                    <span class="campo-contador">máx. 50 caracteres</span>
                </div>
 
                <!-- Botões de ação -->
                <div class="form-acoes">
                    <a href="editar-categoria.php" class="btn-cancelar">Cancelar</a>
                    <?php if (!empty($categorias)): ?>
                        <button type="submit" class="btn-guardar">Guardar Alteração</button>
                    <?php endif; ?>
                </div>
 
            </form>
 
        </div>
 
    </main>
 
    <!-- ========== require puxando o arquivo footer ========== -->
    <?php require_once '../includes/footer.php' ?>
 
    <script src="../assets/js/editar-categoria.js"></script>
 
</body>
</html>