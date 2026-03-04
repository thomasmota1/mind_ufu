<?php
header("Content-Type: application/json; charset=utf-8");
include('db.php');

$tipo = $_POST['tipo'] ?? '';
$nome = trim($_POST['nome'] ?? 'Novo');
$pai_id = (int)($_POST['pai_id'] ?? 0);
$usuario_id = (int)($_POST['usuario_id'] ?? 0);

if (empty($nome)) {
    echo json_encode(["status" => "erro", "msg" => "Nome obrigatório"]);
    exit;
}

$nome = $conn->real_escape_string($nome);

if ($tipo == 'disciplina') {
    if ($usuario_id <= 0) {
        echo json_encode(["status" => "erro", "msg" => "Usuário não identificado"]);
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO disciplinas (nome, usuario_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nome, $usuario_id);
} elseif ($tipo == 'pasta') {
    $stmt = $conn->prepare("INSERT INTO pastas (disciplina_id, nome) VALUES (?, ?)");
    $stmt->bind_param("is", $pai_id, $nome);
} elseif ($tipo == 'pagina') {
    $stmt = $conn->prepare("INSERT INTO paginas (pasta_id, titulo, conteudo) VALUES (?, ?, '')");
    $stmt->bind_param("is", $pai_id, $nome);
} else {
    echo json_encode(["status" => "erro", "msg" => "Tipo inválido"]);
    exit;
}

if ($stmt->execute()) {
    echo json_encode(["status" => "sucesso", "id" => $conn->insert_id]);
} else {
    echo json_encode(["status" => "erro", "msg" => "Erro ao criar"]);
}
?>
