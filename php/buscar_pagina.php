<?php
header("Content-Type: application/json; charset=utf-8");
include('db.php');

$id = (int)($_GET['id'] ?? 0);
$usuario_id = (int)($_GET['usuario_id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["status" => "erro", "msg" => "ID inválido"]);
    exit;
}

$stmt = $conn->prepare("
    SELECT pg.* FROM paginas pg
    JOIN pastas p ON pg.pasta_id = p.id
    JOIN disciplinas d ON p.disciplina_id = d.id
    WHERE pg.id = ? AND d.usuario_id = ?
");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "erro", "msg" => "Página não encontrada"]);
    exit;
}

echo json_encode($result->fetch_assoc());
?>
