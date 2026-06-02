<?php
// FUNÇÃO: Formulário para editar os campos de um desejo existente
// Recebe o id do desejo via GET: editar-desejo.php?id=3

require_once '../config/database.php';

$mensagem      = '';
$tipo_mensagem = '';

// Verifica se o id do desejo foi passado na URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$id_desejo = (int) $_GET['id'];

// Busca os dados atuais do desejo
$sql_desejo = "SELECT * FROM desejos WHERE id_d = :id";
$stmt       = $pdo->prepare($sql_desejo);
$stmt->execute([':id' => $id_desejo]);
$desejo = $stmt->fetch();

// Se o desejo não existir, redireciona
if (!$desejo) {
    header('Location: ../index.php');
    exit;
}

// Guarda o id da categoria de origem para o botão "Voltar"
$id_categoria_origem = $desejo['categoria_d'];

// Verifica se o formulário de edição foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome_d      = trim($_POST['nome_d']);
    $desc_d      = trim($_POST['desc_d']);
    $preco_d     = trim($_POST['preco_d']);
    $categoria_d = (int) $_POST['categoria_d'];

    // Valida se o nome foi preenchido
    if (empty($nome_d)) {
        $mensagem      = 'O nome do desejo é obrigatório.';
        $tipo_mensagem = 'erro';

    } else {
        // Converte o preço para null se vazio, ou para decimal
        $preco_d = $preco_d !== '' ? (float) str_replace(',', '.', $preco_d) : null;

        // Atualiza todos os campos do desejo no banco de dados
        $sql  = "UPDATE desejos
                 SET nome_d = :nome, desc_d = :desc, preco_d = :preco, categoria_d = :categoria
                 WHERE id_d = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'      => $nome_d,
            ':desc'      => $desc_d !== '' ? $desc_d : null,
            ':preco'     => $preco_d,
            ':categoria' => $categoria_d,
            ':id'        => $id_desejo,
        ]);

        // Atualiza os dados locais para mostrar os valores novos no formulário
        $desejo['nome_d']      = $nome_d;
        $desejo['desc_d']      = $desc_d;
        $desejo['preco_d']     = $preco_d;
        $desejo['categoria_d'] = $categoria_d;
        $id_categoria_origem   = $categoria_d;

        $mensagem      = 'Desejo atualizado com sucesso!';
        $tipo_mensagem = 'sucesso';
    }
}

// Busca todas as categorias para o select
$sql_cats   = "SELECT * FROM categorias ORDER BY nome_c ASC";
$stmt       = $pdo->query($sql_cats);
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Desejo — Wishlist</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/desejo-form.css">
</head>
<body>

    <!-- ========== NAVEGAÇÃO ========== -->
    <nav class="navbar">
        <a href="../index.php" class="nav-logo">wishlist</a>
        <ul class="nav-links">
            <li><a href="nova-categoria.php">+ Nova Categoria</a></li>
        </ul>
    </nav>

    <!-- ========== CONTEÚDO PRINCIPAL ========== -->
    <main class="main-content">

        <div class="form-container">

            <!-- Breadcrumb: volta à categoria de origem -->
            <div class="breadcrumb">
                <a href="categoria.php?id=<?= $id_categoria_origem ?>">
                    ← Voltar à categoria
                </a>
            </div>

            <h1 class="form-titulo">Editar Desejo</h1>
            <p class="form-subtitulo">A editar: <b><?= htmlspecialchars($desejo['nome_d']) ?></b></p>

            <!-- Mensagem de feedback -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?= $tipo_mensagem ?>">
                    <?= $mensagem ?>
                    <?php if ($tipo_mensagem === 'sucesso'): ?>
                        <span class="mensagem-links">
                            <a href="categoria.php?id=<?= $id_categoria_origem ?>">← Voltar à categoria</a>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Formulário de edição pré-preenchido com os dados atuais -->
            <form method="POST" action="editar-desejo.php?id=<?= $id_desejo ?>" class="form-desejo">

                <!-- Nome -->
                <div class="campo-grupo">
                    <label for="nome_d" class="campo-label">
                        Nome <span class="campo-obrigatorio">*</span>
                    </label>
                    <input
                        type="text"
                        id="nome_d"
                        name="nome_d"
                        class="campo-input"
                        maxlength="100"
                        autocomplete="off"
                        value="<?= htmlspecialchars($desejo['nome_d']) ?>"
                    >
                </div>

                <!-- Descrição -->
                <div class="campo-grupo">
                    <label for="desc_d" class="campo-label">Descrição <span class="campo-opcional">(opcional)</span></label>
                    <textarea
                        id="desc_d"
                        name="desc_d"
                        class="campo-textarea"
                        rows="4"
                    ><?= htmlspecialchars($desejo['desc_d'] ?? '') ?></textarea>
                </div>

                <!-- Preço e Categoria lado a lado -->
                <div class="campos-linha">

                    <!-- Preço -->
                    <div class="campo-grupo">
                        <label for="preco_d" class="campo-label">Preço € <span class="campo-opcional">(opcional)</span></label>
                        <input
                            type="text"
                            id="preco_d"
                            name="preco_d"
                            class="campo-input"
                            placeholder="ex: 29.99"
                            value="<?= $desejo['preco_d'] !== null ? htmlspecialchars($desejo['preco_d']) : '' ?>"
                        >
                    </div>

                    <!-- Categoria -->
                    <div class="campo-grupo">
                        <label for="categoria_d" class="campo-label">Categoria</label>
                        <select name="categoria_d" id="categoria_d" class="campo-select">
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_c'] ?>"
                                    <?= $cat['id_c'] == $desejo['categoria_d'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome_c']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <!-- Botões -->
                <div class="form-acoes">
                    <a href="categoria.php?id=<?= $id_categoria_origem ?>" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Guardar Alterações</button>
                </div>

            </form>

        </div>

    </main>

    <!-- ========== RODAPÉ ========== -->
    <footer class="footer">
        <p>wishlist &mdash; danielly toledo</p>
    </footer>

</body>
</html>