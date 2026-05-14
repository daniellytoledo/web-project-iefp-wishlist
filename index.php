<?php
// ============================================
// ARQUIVO: index.php
// FUNÇÃO: Página principal da Wishlist
// Exibe todos os itens e formulário para adicionar novos
// ============================================

// Inclui o arquivo de conexão com o banco de dados
// O require_once garante que o arquivo é incluído apenas uma vez
// Se o arquivo não existir, o PHP para a execução com erro fatal
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="pt-br"> <!-- lang="pt-br" define o idioma como português do Brasil -->

<head>
    <meta charset="UTF-8"> <!-- UTF-8 permite acentos e caracteres especiais (ç, ã, é) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Torna o site responsivo em dispositivos móveis -->
    <title>Minha Wishlist</title> <!-- Título que aparece na aba do navegador -->
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Link para o arquivo CSS (estilos) -->
</head>

<body>
    <div class="container"> <!-- Div principal que agrupa todo o conteúdo -->
        <h1>📋 Minha Wishlist</h1> <!-- Título principal da página com emoji -->

        <!-- ========================================== -->
        <!-- FORMULÁRIO PARA ADICIONAR NOVO ITEM          -->
        <!-- ========================================== -->

        <!-- 
            action: "includes/add_item.php" - Para onde os dados serão enviados
            method: "POST" - Método HTTP POST (envia dados no corpo da requisição)
            POST é mais seguro que GET para envio de formulários
        -->
        <form action="includes/add_item.php" method="POST">

            <!-- Campo: Nome do produto -->
            <!-- type="text" - campo de texto simples -->
            <!-- name="nome_d" - nome do campo (mesmo nome da coluna no banco) -->
            <!-- required - obrigatório (não envia o formulário se estiver vazio) -->
            <input type="text" name="nome_d" placeholder="Nome do produto" required>

            <!-- Campo: Descrição -->
            <!-- textarea - campo de texto multilinha -->
            <!-- rows="3" - altura inicial de 3 linhas -->
            <textarea name="desc_d" placeholder="Descrição" rows="3"></textarea>

            <!-- Campo: Preço -->
            <!-- type="number" - campo numérico (abre teclado numérico no celular) -->
            <!-- step="0.01" - permite centavos (incrementos de 0.01) -->
            <input type="number" step="0.01" name="preco_d" placeholder="Preço (R$)">

            <!-- Campo: Seleção de categoria -->
            <!-- select - menu suspenso/dropdown -->
            <!-- name="categoria_d" - nome do campo (FK para categorias) -->
            <!-- required - obrigatório escolher uma categoria -->
            <select name="categoria_d" required>
                <option value="">Selecione a categoria</option> <!-- opção padrão vazia -->

                <?php
                // ==========================================
                // BLOCO PHP: Carregar categorias do banco
                // ==========================================

                // $pdo é a variável de conexão criada no database.php
                // query() executa uma consulta SQL diretamente
                // SELECT * FROM categorias - busca todas as colunas da tabela categorias
                // ORDER BY nome_c - ordena pelo nome da categoria em ordem alfabética
                $categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome_c");

                // while - estrutura de repetição
                // $categorias->fetch() - busca uma linha por vez do resultado
                // Quando não há mais linhas, fetch() retorna false e o loop termina
                while ($cat = $categorias->fetch()) {
                    // $cat['id_c'] - ID da categoria (valor que será enviado ao servidor)
                    // $cat['nome_c'] - Nome da categoria (texto visível para o usuário)
                ?>
                    <option value="<?= $cat['id_c'] ?>"><?= $cat['nome_c'] ?></option>
                <?php
                } 
                ?>
            </select>

            <!-- Botão de envio -->
            <!-- type="submit" - ao clicar, envia o formulário -->
            <button type="submit">Adicionar à Wishlist</button>
        </form>

        <!-- ========================================== -->
        <!-- LISTAGEM DOS ITENS DA WISHLIST              -->
        <!-- ========================================== -->

        <div class="wishlist-items">
            <?php
            // ==========================================
            // BLOCO PHP: Buscar todos os itens da wishlist
            // ==========================================

            // Consulta SQL com JOIN para trazer dados de duas tabelas
            // SELECT d.* - seleciona todas as colunas da tabela desejos (d)
            // c.nome_c - seleciona apenas o nome da categoria da tabela categorias (c)
            // FROM desejos d - tabela principal com apelido 'd'
            // JOIN categorias c - junção com a tabela categorias com apelido 'c'
            // ON d.categoria_d = c.id_c - condição de junção (FK = PK)
            // ORDER BY d.id_d DESC - ordena do mais novo para o mais antigo (DESC = decrescente)
            $desejos = $pdo->query("
                SELECT d.*, c.nome_c 
                FROM desejos d
                JOIN categorias c ON d.categoria_d = c.id_c
                ORDER BY d.id_d DESC
            ");

            // while - percorre cada item do resultado
            while ($item = $desejos->fetch()):
            ?>

                <!-- ========================================== -->
                <!-- CARD DE CADA ITEM DA WISHLIST              -->
                <!-- ========================================== -->

                <div class="item">
                    <!-- Nome do produto -->
                    <!-- htmlspecialchars() - converte caracteres especiais em entidades HTML -->
                    <!-- Previne ataques XSS (Cross-Site Scripting) -->
                    <h3><?= htmlspecialchars($item['nome_d']) ?></h3>

                    <!-- Descrição do produto -->
                    <!-- nl2br() - converte quebras de linha (\n) em tags <br> -->
                    <p><?= nl2br(htmlspecialchars($item['desc_d'])) ?></p>

                    <!-- Preço formatado -->
                    <!-- number_format() - formata números decimais -->
                    <!-- Parâmetros: valor, casas decimais, separador decimal, separador milhar -->
                    <p class="preco">R$ <?= number_format($item['preco_d'], 2, ',', '.') ?></p>

                    <!-- Categoria com emoji -->
                    <p class="categoria">🏷️ <?= $item['nome_c'] ?></p>

                    <!-- Botões de ação (Editar e Deletar) -->
                    <div class="actions">
                        <!-- 
                        Link para edição: edit.php?id=<?= $item['id_d'] ?>
                        $_GET['id'] - passa o ID pela URL (método GET)
                        Exemplo: edit.php?id=9
                    -->
                        <a href="edit.php?id=<?= $item['id_d'] ?>">✏️ Editar</a>

                        <!-- 
                        Link para exclusão: includes/delete_item.php?id=<?= $item['id_d'] ?>
                        onclick - evento JavaScript ao clicar
                        return confirm('Tem certeza?') - mostra caixa de diálogo de confirmação
                        Se o usuário clicar "Cancelar", o link não é seguido
                    -->
                        <a href="includes/delete_item.php?id=<?= $item['id_d'] ?>"
                            onclick="return confirm('Tem certeza que deseja excluir este item?')">
                            🗑️ Deletar
                        </a>
                    </div>
                </div>

            <?php endwhile; // Fim do loop de itens 
            ?>

            <!-- Mensagem caso a wishlist esteja vazia -->
            <?php if ($desejos->rowCount() == 0): ?>
                <p class="vazio">Sua wishlist está vazia. Adicione seus desejos acima!</p>
            <?php endif; ?>

        </div>
    </div>
</body>

</html>