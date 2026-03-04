<?php
header("Content-Type: application/json; charset=utf-8");
include('db.php');

$id = (int)($_POST['id'] ?? 0);
$usuario_id = (int)($_POST['usuario_id'] ?? 0);
$titulo = $_POST['titulo'] ?? '';
$conteudo = $_POST['conteudo'] ?? '';

if ($id <= 0) {
    echo "Erro";
    exit;
}

// Verificar se a página pertence ao usuário
$stmt = $conn->prepare("
    SELECT pg.id FROM paginas pg
    JOIN pastas p ON pg.pasta_id = p.id
    JOIN disciplinas d ON p.disciplina_id = d.id
    WHERE pg.id = ? AND d.usuario_id = ?
");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    echo "Erro";
    exit;
}

// Atualizar página
$stmt = $conn->prepare("UPDATE paginas SET titulo = ?, conteudo = ? WHERE id = ?");
$stmt->bind_param("ssi", $titulo, $conteudo, $id);

echo $stmt->execute() ? "Sucesso" : "Erro";
?>
