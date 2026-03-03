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

// Obter usuario_id do request (sem fallback para evitar vazamento de dados)
$usuario_id = (int)($_POST['usuario_id'] ?? $_GET['usuario_id'] ?? 0);

// Validar usuario_id para ações que precisam de autenticação
$acoes_publicas = []; // Nenhuma ação é pública
if (!in_array($acao, $acoes_publicas) && $usuario_id <= 0) {
    echo json_encode(["status" => "erro", "msg" => "Usuário não identificado"]);
    exit;
}


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
// GET QUIZ FULL (com perguntas e alternativas)
// =========================
elseif ($acao == 'get_quiz_full') {

    $quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

    // Buscar dados do quiz
    $stmt = $conn->prepare("SELECT * FROM questionarios WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();

    if (!$quiz) {
        echo json_encode(["status" => "erro", "msg" => "Quiz não encontrado"]);
        exit;
    }

    // Buscar perguntas do quiz
    $stmt = $conn->prepare("SELECT * FROM perguntas WHERE questionario_id = ? ORDER BY id");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $res_perguntas = $stmt->get_result();

    $perguntas = [];
    while ($p = $res_perguntas->fetch_assoc()) {

        // Buscar alternativas de cada pergunta
        $stmt_alt = $conn->prepare("SELECT * FROM alternativas WHERE pergunta_id = ? ORDER BY id");
        $stmt_alt->bind_param("i", $p['id']);
        $stmt_alt->execute();
        $res_alt = $stmt_alt->get_result();

        $alternativas = [];
        while ($alt = $res_alt->fetch_assoc()) {
            $alternativas[] = $alt;
        }

        $p['alternativas'] = $alternativas;
        $perguntas[] = $p;
    }

    echo json_encode([
        "status" => "sucesso",
        "quiz" => $quiz,
        "perguntas" => $perguntas
    ]);
}


// =========================
// SALVAR QUIZ NA LISTA DO USUÁRIO
// =========================
elseif ($acao == 'salvar_quiz_lista') {

    $quiz_id = isset($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : 0;

    if ($quiz_id <= 0) {
        echo json_encode(["status" => "erro", "msg" => "ID do quiz inválido"]);
        exit;
    }

    // INSERT IGNORE evita erro se já estiver salvo
    $stmt = $conn->prepare("INSERT IGNORE INTO quizzes_salvos (usuario_id, questionario_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $usuario_id, $quiz_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "sucesso", "msg" => "Quiz salvo com sucesso!"]);
        } else {
            echo json_encode(["status" => "sucesso", "msg" => "Quiz já estava salvo."]);
        }
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao salvar: " . $conn->error]);
    }
}


// =========================
// CRIAR QUIZ (RASCUNHO)
// =========================
elseif ($acao == 'criar_quiz') {

    $comunidade_id = (int)($_POST['comunidade_id'] ?? 0);
    $titulo = $conn->real_escape_string($_POST['titulo'] ?? '');
    $descricao = $conn->real_escape_string($_POST['descricao'] ?? '');

    if (empty($titulo) || $comunidade_id <= 0) {
        echo json_encode(["status" => "erro", "msg" => "Dados incompletos"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO questionarios (comunidade_id, titulo, descricao, criador_id, publicado) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("issi", $comunidade_id, $titulo, $descricao, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso", "quiz_id" => $conn->insert_id]);
    } else {
        echo json_encode(["status" => "erro", "msg" => $conn->error]);
    }
}


// =========================
// EXCLUIR QUIZ
// =========================
elseif ($acao == 'excluir_quiz') {

    $quiz_id = (int)($_POST['quiz_id'] ?? 0);

    // Verificar se o usuário é o criador
    $stmt = $conn->prepare("SELECT criador_id FROM questionarios WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row || $row['criador_id'] != $usuario_id) {
        echo json_encode(["status" => "erro", "msg" => "Sem permissão"]);
        exit;
    }

    // Excluir (CASCADE vai remover perguntas e alternativas)
    $stmt = $conn->prepare("DELETE FROM questionarios WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();

    echo json_encode(["status" => "sucesso"]);
}


// =========================
// PUBLICAR QUIZ
// =========================
elseif ($acao == 'publicar_quiz') {

    $quiz_id = (int)($_POST['quiz_id'] ?? 0);

    $stmt = $conn->prepare("UPDATE questionarios SET publicado = 1 WHERE id = ? AND criador_id = ?");
    $stmt->bind_param("ii", $quiz_id, $usuario_id);
    $stmt->execute();

    echo json_encode(["status" => "sucesso"]);
}


// =========================
// LISTAR MEUS QUIZZES SALVOS
// =========================
elseif ($acao == 'listar_meus_quizzes') {

    $sql = "SELECT q.*, c.nome as comunidade_nome, c.cor as comunidade_cor, u.nome as autor_nome
            FROM quizzes_salvos qs
            JOIN questionarios q ON qs.questionario_id = q.id
            LEFT JOIN comunidades c ON q.comunidade_id = c.id
            LEFT JOIN usuarios u ON q.criador_id = u.id
            WHERE qs.usuario_id = ?
            ORDER BY qs.data_salvo DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $lista = [];
    while ($row = $result->fetch_assoc()) {
        $lista[] = $row;
    }

    echo json_encode($lista);
}


// =========================
// REMOVER QUIZ DA LISTA DE SALVOS
// =========================
elseif ($acao == 'remover_salvo') {

    $quiz_id = (int)($_POST['quiz_id'] ?? 0);

    $stmt = $conn->prepare("DELETE FROM quizzes_salvos WHERE usuario_id = ? AND questionario_id = ?");
    $stmt->bind_param("ii", $usuario_id, $quiz_id);
    $stmt->execute();

    echo json_encode(["status" => "sucesso"]);
}


// =========================
// CRIAR PERGUNTA
// =========================
elseif ($acao == 'criar_pergunta') {

    $quiz_id = (int)($_POST['quiz_id'] ?? 0);
    $enunciado = $conn->real_escape_string($_POST['enunciado'] ?? 'Nova pergunta');

    $stmt = $conn->prepare("INSERT INTO perguntas (questionario_id, enunciado) VALUES (?, ?)");
    $stmt->bind_param("is", $quiz_id, $enunciado);
    $stmt->execute();

    $pergunta_id = $conn->insert_id;

    // Criar 4 alternativas vazias
    for ($i = 0; $i < 4; $i++) {
        $correta = ($i == 0) ? 1 : 0;
        $conn->query("INSERT INTO alternativas (pergunta_id, texto, e_correta) VALUES ($pergunta_id, 'Alternativa " . ($i + 1) . "', $correta)");
    }

    echo json_encode(["status" => "sucesso", "pergunta_id" => $pergunta_id]);
}


// =========================
// EDITAR INFO DO QUIZ (título, descrição e permite_salvar_antes)
// =========================
elseif ($acao == 'editar_quiz_info') {

    $quiz_id = (int)($_POST['quiz_id'] ?? 0);
    $titulo = $conn->real_escape_string($_POST['titulo'] ?? '');
    $descricao = $conn->real_escape_string($_POST['descricao'] ?? '');
    $permite_salvar = isset($_POST['permite_salvar_antes']) ? (int)$_POST['permite_salvar_antes'] : 1;

    $stmt = $conn->prepare("UPDATE questionarios SET titulo = ?, descricao = ?, permite_salvar_antes = ? WHERE id = ? AND criador_id = ?");
    $stmt->bind_param("ssiii", $titulo, $descricao, $permite_salvar, $quiz_id, $usuario_id);
    $stmt->execute();

    echo json_encode(["status" => "sucesso"]);
}


// =========================
// LISTAR MEMBROS DA COMUNIDADE
// =========================
elseif ($acao == 'listar_membros') {

    $comunidade_id = isset($_GET['comunidade_id']) ? (int)$_GET['comunidade_id'] : 0;

    $stmt = $conn->prepare("
        SELECT u.id, u.nome, u.email, u.foto
        FROM usuarios u
        JOIN membros_comunidade m ON u.id = m.usuario_id
        WHERE m.comunidade_id = ?
        ORDER BY u.nome
    ");
    $stmt->bind_param("i", $comunidade_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $membros = [];
    while ($row = $result->fetch_assoc()) {
        $membros[] = $row;
    }

    echo json_encode($membros);
}


// =========================
// SALVAR PERGUNTA (criar nova ou atualizar existente)
// =========================
elseif ($acao == 'salvar_pergunta') {

    $quiz_id = (int)($_POST['quiz_id'] ?? 0);
    $pergunta_id = (int)($_POST['pergunta_id'] ?? 0);
    $enunciado = $conn->real_escape_string($_POST['enunciado'] ?? '');
    $opcoes = isset($_POST['opcoes']) ? $_POST['opcoes'] : [];
    $correta = (int)($_POST['correta'] ?? 0);

    // Se tem pergunta_id, é atualização; senão, cria nova
    if ($pergunta_id > 0) {
        // Atualizar enunciado existente
        $stmt = $conn->prepare("UPDATE perguntas SET enunciado = ? WHERE id = ?");
        $stmt->bind_param("si", $enunciado, $pergunta_id);
        $stmt->execute();
    } else {
        // Criar nova pergunta
        $stmt = $conn->prepare("INSERT INTO perguntas (questionario_id, enunciado) VALUES (?, ?)");
        $stmt->bind_param("is", $quiz_id, $enunciado);
        $stmt->execute();
        $pergunta_id = $conn->insert_id;

        // Criar alternativas
        foreach ($opcoes as $idx => $texto) {
            $texto = $conn->real_escape_string($texto);
            $e_correta = ($idx == $correta) ? 1 : 0;
            $conn->query("INSERT INTO alternativas (pergunta_id, texto, e_correta) VALUES ($pergunta_id, '$texto', $e_correta)");
        }
    }

    echo json_encode(["status" => "sucesso", "pergunta_id" => $pergunta_id]);
}


// =========================
// EXCLUIR PERGUNTA
// =========================
elseif ($acao == 'excluir_pergunta') {

    $pergunta_id = (int)($_POST['pergunta_id'] ?? 0);

    // CASCADE vai remover alternativas automaticamente
    $stmt = $conn->prepare("DELETE FROM perguntas WHERE id = ?");
    $stmt->bind_param("i", $pergunta_id);
    $stmt->execute();

    echo json_encode(["status" => "sucesso"]);
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