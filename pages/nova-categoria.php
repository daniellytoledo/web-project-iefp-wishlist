<?php
// ARQUIVO: nova-categoria.php
// FUNÇÃO: Formulário para adicionar uma nova categoria ao banco de dados

// Quando a página abre normalmente → só mostra o formulário
// Quando o formulário é submetido (POST) → valida se o campo está preenchido
// Se estiver vazio → mostra mensagem de erro e mantém o que foi escrito no campo
// Se estiver preenchido → faz o INSERT com prepared statement (seguro contra SQL Injection) e mostra mensagem de sucesso com links para voltar ou adicionar outra

require_once '../config/database.php';

$mensagem = '';
$tipo_mensagem = '';

// Verifica se o formulário foi submetido
// $_SERVER['REQUEST_METHOD'] retorna o método HTTP usado (GET ou POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recolhe e limpa o valor enviado pelo formulário
    // trim() remove espaços em branco no início e no fim
    $nome_categoria = trim($_POST['nome_categoria']);

    // Valida se o campo não está vazio
    if (empty($nome_categoria)) {
        $mensagem = 'Por favor, escreve o nome da categoria.';
        $tipo_mensagem = 'erro';
    } else {
        // Insere a nova categoria no banco de dados
        // Usa prepared statement para evitar SQL Injection
        $sql = "INSERT INTO categorias (nome_c) VALUES (:nome)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nome' => $nome_categoria]);

        $mensagem = 'Categoria "' . htmlspecialchars($nome_categoria) . '" adicionada com sucesso!';
        $tipo_mensagem = 'sucesso';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Categoria — Wishlist</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/nova-categoria.css">
</head>

<body>

    <!-- ========== require puxando o arquivo nav ========== -->
    <?php require_once '../includes/nav.php' ?>

    <!-- ========== CONTEÚDO PRINCIPAL ========== -->
    <main class="main-content">

        <div class="form-container">

            <h1 class="form-titulo">Nova Categoria</h1>
            <p class="form-subtitulo">Adicionar uma nova categoria à tua wishlist.</p>

            <!-- Mensagem de feedback (sucesso ou erro) -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?= $tipo_mensagem ?>">
                    <?= $mensagem ?>
                    <?php if ($tipo_mensagem === 'sucesso'): ?>
                        <span class="mensagem-links">
                            <a href="index.php">← Voltar ao início</a> ou
                            <a href="nova-categoria.php">adicionar outra</a>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="POST" action="nova-categoria.php" class="form-categoria">

                <div class="campo-grupo">
                    <label for="nome_categoria" class="campo-label">Nome da Categoria</label>
                    <input
                        type="text"
                        id="nome_categoria"
                        name="nome_categoria"
                        class="campo-input"
                        placeholder="ex: Tecnologia, Livros, Viagens..."
                        maxlength="50"
                        autocomplete="off"
                        value="<?= isset($_POST['nome_categoria']) && $tipo_mensagem === 'erro' ? htmlspecialchars($_POST['nome_categoria']) : '' ?>">
                    <span class="campo-contador">máx. 50 caracteres</span>
                </div>

                <div class="form-acoes">
                    <a href="index.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Guardar Categoria</button>
                </div>

            </form>

        </div>

    </main>

    <!-- ========== require puxando o arquivo footer ========== -->
    <?php require_once '../includes/footer.php' ?>

</body>

</html>