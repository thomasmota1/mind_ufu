<?php
header("Content-Type: application/json; charset=utf-8");
include('db.php');

$tipo = $_POST['tipo'] ?? '';
$id = (int)($_POST['id'] ?? 0);
$usuario_id = (int)($_POST['usuario_id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["status" => "erro", "msg" => "ID inválido"]);
    exit;
}

// Verificar propriedade antes de excluir
if ($tipo == 'disciplina') {
    // Verificar se a disciplina pertence ao usuário
    $stmt = $conn->prepare("SELECT id FROM disciplinas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Disciplina não encontrada"]);
        exit;
    }

    // Excluir (CASCADE vai apagar pastas e páginas)
    $stmt = $conn->prepare("DELETE FROM disciplinas WHERE id = ?");
    $stmt->bind_param("i", $id);

} elseif ($tipo == 'pasta') {
    // Verificar se a pasta pertence a uma disciplina do usuário
    $stmt = $conn->prepare("
        SELECT p.id FROM pastas p
        JOIN disciplinas d ON p.disciplina_id = d.id
        WHERE p.id = ? AND d.usuario_id = ?
    ");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Pasta não encontrada"]);
        exit;
    }

    // Excluir (CASCADE vai apagar páginas)
    $stmt = $conn->prepare("DELETE FROM pastas WHERE id = ?");
    $stmt->bind_param("i", $id);

} elseif ($tipo == 'pagina') {
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
        echo json_encode(["status" => "erro", "msg" => "Página não encontrada"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM paginas WHERE id = ?");
    $stmt->bind_param("i", $id);

} else {
    echo json_encode(["status" => "erro", "msg" => "Tipo inválido"]);
    exit;
}

if ($stmt->execute()) {
    echo json_encode(["status" => "sucesso"]);
} else {
    echo json_encode(["status" => "erro", "msg" => "Erro ao excluir"]);
}
?>
