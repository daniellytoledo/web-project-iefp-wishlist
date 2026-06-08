<?php
// FUNÇÃO: Recebe o POST do modal de confirmação e apaga o desejo do banco
// Não tem HTML — só processa e redireciona

require_once '../config/database.php';

// Aceita apenas requisições POST para evitar apagamentos acidentais via URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id_desejo    = (int) $_POST['id_desejo'];
$id_categoria = (int) $_POST['id_categoria'];

// Apaga o desejo do banco de dados
$sql  = "DELETE FROM desejos WHERE id_d = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_desejo]);

// Redireciona de volta para a categoria após apagar
header('Location: categoria.php?id=' . $id_categoria);
exit;