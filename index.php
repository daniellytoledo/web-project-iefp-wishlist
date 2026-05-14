<?php
// ARQUIVO: index.php
// FUNÇÃO: Página principal - exibe as categorias da wishlist como botões
 
require_once 'config/database.php';
 
// Busca todas as categorias do banco de dados
$sql = "SELECT * FROM categorias ORDER BY nome_c ASC";
$stmt = $pdo->query($sql);
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
                    <a href="categoria.php?id=<?= $categoria['id_c'] ?>" class="categoria-btn">
                        <?= htmlspecialchars($categoria['nome_c']) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
 
    </main>
 

<?php require_once 'includes/footer.php' ?>
 
</body>
</html>
