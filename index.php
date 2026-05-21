<?php
// ARQUIVO: index.php
// FUNÇÃO: Página principal - exibe as categorias da wishlist como botões
 
require_once 'config/database.php';
 
// Busca todas as categorias do banco de dados
$sql = "SELECT * FROM categorias ORDER BY nome_c ASC";
// $pdo é a conexão com a base de dados e ->query executa o comando $sql que neste caso é o select * from acima
$stmt = $pdo->query($sql);
// agora criamos um array chamado $categorias com os dados do comando acima.
// o $stmt é um objeto que representa a conexão com os resultados
// o fetchAll pega o resultado do objeto e passa para o array
$categorias = $stmt->fetchAll();
?>
 
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
 
    <!-- ========== NAVEGAÇÃO ========== -->
    <nav class="navbar">
        <div class="nav-logo">wishlist</div>
        <ul class="nav-links">
            <li><a href="pages/nova-categoria.php">+ Nova Categoria</a></li>
        </ul>
    </nav>
 
    <!-- ========== CONTEÚDO PRINCIPAL ========== -->
    <main class="main-content">
 
        <h1 class="page-title">O que desejas?</h1>
 
        <!-- Grade de botões de categorias -->
        <div class="categorias-grid">
            <?php if (empty($categorias)): ?>
                <p class="sem-categorias">Nenhuma categoria encontrada. Adiciona a primeira!</p>
            <?php else: ?>
                <?php foreach ($categorias as $categoria): ?>
                    <a href="pages/categoria.php?id=<?= $categoria['id_c'] ?>" class="categoria-btn">
                        <?= htmlspecialchars($categoria['nome_c']) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
 
    </main>
 

<?php require_once 'includes/footer.php' ?>
 
</body>
</html>
