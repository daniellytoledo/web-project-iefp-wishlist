<?php
// recebe o id da categoria via GET: novo-desejo.php?categoria=2

require_once '../config/database.php';

$mensagem         = '';
$tipo_mensagem    = '';

// lê o id da categoria passado na URL
// se não existir ou não for número, redireciona para o index
if(!isset($_GET['categoria']) || !is_numeric($_GET['categoria'])) {
    header('Location: ../index.php');
    exit;
}

$id_categoria = (int) $_GET['categoria'];

// busca o nome da categoria para mostrar no título da página
$sql_cat    = "SELECT * FROM categorias WHERE id_c = :id"; // aqui cria a consulta SQL
$stmt       = $pdo->prepare($sql_cat); // o PDO prepara a consulta para execução, o $pdo é a conexão com o banco e o prepare prepara o SQL antes de executar e o resultado fica armazenado em $stmt
$stmt->execute([':id' => $id_categoria]); // aqui o array é executado, se o id é igual ao $id_categoria, então ele executa as info que é desse id da categoria apenas
$categoria  = $stmt->fetch(); // e o fetch pega o resultado retornado pelo banco

// se a categoria não existir, redireciona
if(!$categoria) {
    header('Location: ../index.php');
    exit;
}

// verifica se o formulário foi submetido
// o trim serve para remover espaços vazio no inicio e no fim de uma string
// se o usuário colocar por exemplo "  esporte ", o trim vai remover os espaços e deixar "esporte"
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_d      = trim($_POST['nome_d']);
    $desc_d      = trim($_POST['desc_d']);
    $preco_d     = trim($_POST['preco_d']);
    $categoria_d = trim($_POST['categoria_d']);

// valida se o nome do desejo foi preenchido (único campo obrigatório)
if(empty($nome_d)) {
    $mensagem      = 'O nome do desejo é obrigatório.';
    $tipo_mensagem = 'erro';
    } else {
        // Converte o preço para null se estiver vazio, ou para decimal
        $preco_d = $preco_d !== '' ? (float) str_replace(',', '.', $preco_d) : null;
 
        // Insere o novo desejo no banco de dados
        $sql  = "INSERT INTO desejos (nome_d, desc_d, preco_d, categoria_d)
                 VALUES (:nome, :desc, :preco, :categoria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'      => $nome_d,
            ':desc'      => $desc_d !== '' ? $desc_d : null,
            ':preco'     => $preco_d,
            ':categoria' => $categoria_d,
        ]);
 
        $mensagem      = '"' . htmlspecialchars($nome_d) . '" adicionado com sucesso!';
        $tipo_mensagem = 'sucesso';
    }
}

// busca todas as categorias para o select de categoria do formulário
$sql_cat    = "SELECT * FROM categorias ORDER BY nome_c ASC";
$stmt       = $pdo->query($sql_cat);
$categorias = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Desejo — <?= htmlspecialchars($categoria['nome_c']) ?></title>
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
 
            <!-- Breadcrumb: indica de onde veio o utilizador -->
            <div class="breadcrumb">
                <a href="categoria.php?id=<?= $id_categoria ?>">
                    ← <?= htmlspecialchars($categoria['nome_c']) ?>
                </a>
            </div>
 
            <h1 class="form-titulo">Novo Desejo</h1>
            <p class="form-subtitulo">A adicionar em <b><?= htmlspecialchars($categoria['nome_c']) ?></b></p>
 
            <!-- Mensagem de feedback -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?= $tipo_mensagem ?>">
                    <?= $mensagem ?>
                    <?php if ($tipo_mensagem === 'sucesso'): ?>
                        <span class="mensagem-links">
                            <a href="categoria.php?id=<?= $id_categoria ?>">← Voltar à categoria</a> ou
                            <a href="novo-desejo.php?categoria=<?= $id_categoria ?>">adicionar outro</a>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
 
            <!-- Formulário -->
            <form method="POST" action="novo-desejo.php?categoria=<?= $id_categoria ?>" class="form-desejo">
 
                <!-- Nome do desejo (obrigatório) -->
                <div class="campo-grupo">
                    <label for="nome_d" class="campo-label">
                        Nome <span class="campo-obrigatorio">*</span>
                    </label>
                    <input
                        type="text"
                        id="nome_d"
                        name="nome_d"
                        class="campo-input"
                        placeholder="ex: Sony A7 Mark IV, Livro X..."
                        maxlength="100"
                        autocomplete="off"
                        value="<?= (isset($_POST['nome_d']) && $tipo_mensagem === 'erro') ? htmlspecialchars($_POST['nome_d']) : '' ?>"
                    >
                </div>
 
                <!-- Descrição (opcional) -->
                <div class="campo-grupo">
                    <label for="desc_d" class="campo-label">Descrição <span class="campo-opcional">(opcional)</span></label>
                    <textarea
                        id="desc_d"
                        name="desc_d"
                        class="campo-textarea"
                        placeholder="Detalhes sobre o produto, modelo, onde comprar..."
                        rows="4"
                    ><?= (isset($_POST['desc_d']) && $tipo_mensagem === 'erro') ? htmlspecialchars($_POST['desc_d']) : '' ?></textarea>
                </div>
 
                <!-- Linha com preço e categoria lado a lado -->
                <div class="campos-linha">
 
                    <!-- Preço (opcional) -->
                    <div class="campo-grupo">
                        <label for="preco_d" class="campo-label">Preço € <span class="campo-opcional">(opcional)</span></label>
                        <input
                            type="text"
                            id="preco_d"
                            name="preco_d"
                            class="campo-input"
                            placeholder="ex: 29.99"
                            value="<?= (isset($_POST['preco_d']) && $tipo_mensagem === 'erro') ? htmlspecialchars($_POST['preco_d']) : '' ?>"
                        >
                    </div>
 
                    <!-- Categoria (pré-selecionada com a categoria atual) -->
                    <div class="campo-grupo">
                        <label for="categoria_d" class="campo-label">Categoria</label>
                        <select name="categoria_d" id="categoria_d" class="campo-select">
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_c'] ?>"
                                    <?= $cat['id_c'] == $id_categoria ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome_c']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
 
                </div>
 
                <!-- Botões -->
                <div class="form-acoes">
                    <a href="categoria.php?id=<?= $id_categoria ?>" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Adicionar Desejo</button>
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