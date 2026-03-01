<?php

// ===== CORS =====
header("Access-Control-Allow-Origin: *"); // ajuste depois para produção
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Resposta para preflight (OBRIGATÓRIO)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// =================

// Mostrar erros (somente desenvolvimento)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('db.php'); // INCLUIR APENAS UMA VEZ

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$usuario_id = 1; // Usuário fixo (simulação login)


// =========================
// LISTAR COMUNIDADES
// =========================
if ($acao == 'listar') {

    $sql = "SELECT c.* FROM comunidades c
            JOIN membros_comunidade m ON c.id = m.comunidade_id
            WHERE m.usuario_id = $usuario_id";

    $result = $conn->query($sql);

    $lista = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lista[] = $row;
        }
    }

    echo json_encode($lista);
}


// =========================
// DETALHES COMUNIDADE
// =========================
elseif ($acao == 'detalhes') {

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $res = $conn->query("SELECT * FROM comunidades WHERE id = $id");
    $comunidade = $res ? $res->fetch_assoc() : null;

    if ($comunidade) {

        $sql = "SELECT q.*, u.nome as autor_nome
                FROM questionarios q
                LEFT JOIN usuarios u ON q.criador_id = u.id
                WHERE q.comunidade_id = $id
                AND (q.publicado = 1 OR q.criador_id = $usuario_id)
                ORDER BY q.id DESC";

        $res_quest = $conn->query($sql);

        $questionarios = [];
        if ($res_quest) {
            while ($q = $res_quest->fetch_assoc()) {
                $questionarios[] = $q;
            }
        }

        echo json_encode([
            "status" => "sucesso",
            "dados" => $comunidade,
            "quiz" => $questionarios,
            "user_id" => $usuario_id
        ]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Comunidade não encontrada"]);
    }
}


// =========================
// CRIAR COMUNIDADE
// =========================
elseif ($acao == 'criar') {

    $nome = $conn->real_escape_string($_POST['nome'] ?? '');
    $cor = $_POST['cor'] ?? '#0d6efd';
    $codigo = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    $sql = "INSERT INTO comunidades (nome, codigo, cor)
            VALUES ('$nome', '$codigo', '$cor')";

    if ($conn->query($sql)) {

        $id = $conn->insert_id;

        $conn->query("INSERT INTO membros_comunidade (comunidade_id, usuario_id)
                      VALUES ($id, $usuario_id)");

        echo json_encode(["status" => "sucesso", "codigo" => $codigo]);

    } else {
        echo json_encode(["status" => "erro", "msg" => $conn->error]);
    }
}


// =========================
// ENTRAR EM COMUNIDADE
// =========================
elseif ($acao == 'entrar') {

    $codigo = $conn->real_escape_string($_POST['codigo'] ?? '');

    $res = $conn->query("SELECT id FROM comunidades WHERE codigo = '$codigo'");

    if ($res && $res->num_rows > 0) {

        $id_com = $res->fetch_assoc()['id'];

        $conn->query("INSERT IGNORE INTO membros_comunidade (comunidade_id, usuario_id)
                      VALUES ($id_com, $usuario_id)");

        echo json_encode(["status" => "sucesso"]);

    } else {
        echo json_encode(["status" => "erro", "msg" => "Código inválido"]);
    }
}


// =========================
// AÇÃO INVÁLIDA
// =========================
else {

    echo json_encode([
        "status" => "erro",
        "msg" => "Ação inválida ou não enviada. Ação recebida: " . $acao
    ]);
}

?>