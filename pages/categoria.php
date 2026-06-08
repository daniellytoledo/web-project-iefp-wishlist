<?php
// ARQUIVO: pages/categoria.php
// FUNÇÃO: Exibe os desejos de uma categoria específica de forma dinâmica

require_once '../config/database.php';

// Verifica se o id da categoria foi passado na URL
// Exemplo: categoria.php?id=2
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$id_categoria = (int) $_GET['id'];

// Busca o nome da categoria pelo id
$sql_categoria = "SELECT * FROM categorias WHERE id_c = :id";
$stmt = $pdo->prepare($sql_categoria);
$stmt->execute([':id' => $id_categoria]);
$categoria = $stmt->fetch();

// Se a categoria não existir, redireciona para o index
if (!$categoria) {
    header('Location: ../index.php');
    exit;
}

// Busca todos os desejos que pertencem a esta categoria
$sql_desejos = "SELECT * FROM desejos WHERE categoria_d = :id ORDER BY nome_d ASC";
$stmt = $pdo->prepare($sql_desejos);
$stmt->execute([':id' => $id_categoria]);
$desejos = $stmt->fetchAll();

// Limite de caracteres antes de mostrar o botão "ver +"
define('DESC_LIMITE', 80);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($categoria['nome_c']) ?> — Wishlist</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/categoria.css">
</head>

<body>

    <!-- ========== require puxando o arquivo nav ========== -->
    <?php require_once '../includes/nav.php' ?>

    <!-- ========== CABEÇALHO DA CATEGORIA ========== -->
    <header class="categoria-header">
        <a href="../index.php" class="btn-voltar">← voltar</a>
        <h1 class="categoria-titulo"><?= htmlspecialchars($categoria['nome_c']) ?></h1>
        <a href="editar-categoria.php?id=<?= $id_categoria ?>" class="btn-editar-categoria">✎ editar</a>
        <a href="novo-desejo.php?categoria=<?= $id_categoria ?>" class="btn-novo-desejo">+ Novo Desejo</a>
        <span class="categoria-contagem"><?= count($desejos) ?> desejo<?= count($desejos) !== 1 ? 's' : '' ?></span>

    </header>

    <!-- ========== CONTEÚDO PRINCIPAL ========== -->
    <main class="main-content">

        <?php if (empty($desejos)): ?>
            <p class="sem-desejos">Nenhum desejo nesta categoria ainda.</p>
        <?php else: ?>

            <div class="desejos-grid">
                <?php foreach ($desejos as $desejo): ?>

                    <?php
                    $descricao_completa = $desejo['desc_d'] ?? '';
                    $descricao_curta    = mb_substr($descricao_completa, 0, DESC_LIMITE);
                    $tem_mais           = mb_strlen($descricao_completa) > DESC_LIMITE;
                    ?>

                    <div class="desejo-card">

                        <!-- Área reservada para imagem -->
                        <!-- onload: quando a imagem carrega com sucesso, garante que o span placeholder fica escondido -->
                        <!-- onerror: quando a imagem não existe ou falha, esconde a img e mostra o span no lugar -->
                        <div class="desejo-imagem">
                            <img src="../img_artigos/<?= $desejo['id_d'] ?>.jpg"
                                alt="<?= htmlspecialchars($desejo['nome_d']) ?>"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                onload="this.nextElementSibling.style.display='none';">
                            <span class="imagem-placeholder">imagem em breve</span>
                        </div>

                        <!-- Informações do desejo -->
                        <div class="desejo-info">

                            <h2 class="desejo-nome"><?= htmlspecialchars($desejo['nome_d']) ?></h2>
                            <div class="desejo-acoes">
                                <a href="editar-desejo.php?id=<?= $desejo['id_d'] ?>" class="btn-editar-desejo">✎ editar</a>
                                <a href="#" class="btn-editar-desejo" onclick="abrirModalApagar(<?= $desejo['id_d'] ?>, '<?= htmlspecialchars($desejo['nome_d'], ENT_QUOTES) ?>'); return false;">✕ apagar</a>
                            </div>

                            <?php if ($desejo['preco_d']): ?>
                                <span class="desejo-preco">€ <?= number_format($desejo['preco_d'], 2, ',', '.') ?></span>
                            <?php endif; ?>

                            <p class="desejo-descricao">
                                <?php if ($tem_mais): ?>
                                    <?= htmlspecialchars($descricao_curta) ?>...
                                    <button
                                        class="btn-ver-mais"
                                        onclick="abrirModal(<?= $desejo['id_d'] ?>)">ver +</button>
                                <?php else: ?>
                                    <?= htmlspecialchars($descricao_completa) ?>
                                <?php endif; ?>
                            </p>

                        </div>
                    </div>

                    <?php if ($tem_mais): ?>
                        <!-- Modal com descrição completa -->
                        <div class="modal-overlay" id="modal-<?= $desejo['id_d'] ?>" onclick="fecharModal(<?= $desejo['id_d'] ?>)">
                            <div class="modal-caixa" onclick="event.stopPropagation()">
                                <button class="modal-fechar" onclick="fecharModal(<?= $desejo['id_d'] ?>)">✕</button>
                                <h3 class="modal-titulo"><?= htmlspecialchars($desejo['nome_d']) ?></h3>
                                <p class="modal-descricao"><?= htmlspecialchars($descricao_completa) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </main>

    <!-- ========== require puxando o arquivo footer ========== -->
    <?php require_once '../includes/footer.php' ?>

    <!-- Formulário oculto submetido pelo JS ao confirmar apagar -->
    <form id="form-apagar" action="apagar-desejo.php" method="POST" style="display:none;">
        <input type="hidden" id="input-id-desejo" name="id_desejo" value="">
        <input type="hidden" name="id_categoria" value="<?= $id_categoria ?>">
    </form>

    <script src="../assets/js/categoria.js"></script>

    <!-- MODAL DE CONFIRMAÇÃO DE APAGAR DESEJO -->
    <div class="modal-apagar-overlay" id="modal-apagar" style="display:none;" onclick="fecharModalApagar()">
        <div class="modal-apagar-caixa" onclick="event.stopPropagation()">
            <div class="modal-icone">!</div>
            <h3 class="modal-apagar-titulo">Apagar desejo?</h3>
            <p class="modal-texto">
                Vais apagar <strong id="modal-nome-desejo"></strong>. Esta ação é irreversível.
            </p>
            <div class="modal-acoes">
                <button type="button" class="btn-cancelar" onclick="fecharModalApagar()">Não, cancelar</button>
                <button type="button" class="btn-apagar-confirmar" onclick="confirmarApagar()">Sim, apagar</button>
            </div>
        </div>
    </div>

</body>

</html>