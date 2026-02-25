<?php
<<<<<<< HEAD
=======
// ===== CORS =====
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
// se quiser liberar tudo em ambiente local, pode usar:
// header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Resposta para preflight (OBRIGATÓRIO)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// =================

// Mostrar erros (desenvolvimento)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('db.php');
//--
>>>>>>> 3a38f99 (Adiciona arquivos iniciais do projeto mind_ufu + cadastro)
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
include('db.php');

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$usuario_id = 1; // Usuário logado (Prof. Silva)


if ($acao == 'listar') {
    $sql = "SELECT c.* FROM comunidades c 
            JOIN membros_comunidade m ON c.id = m.comunidade_id 
            WHERE m.usuario_id = $usuario_id";
    $result = $conn->query($sql);
    
    $lista = [];
    if ($result) {
        while($row = $result->fetch_assoc()) { $lista[] = $row; }
    }
    echo json_encode($lista);
}

elseif ($acao == 'detalhes') {
    $id = $_GET['id'];
    
    $comunidade = $conn->query("SELECT * FROM comunidades WHERE id = $id")->fetch_assoc();
    
    if($comunidade) {
        // Busca quizzes (Rascunhos só para o dono)
        $sql = "SELECT q.*, u.nome as autor_nome 
                FROM questionarios q
                LEFT JOIN usuarios u ON q.criador_id = u.id
                WHERE q.comunidade_id = $id 
                AND (q.publicado = 1 OR q.criador_id = $usuario_id)
                ORDER BY q.id DESC";
        
        $res_quest = $conn->query($sql);
        $questionarios = [];
        if($res_quest) {
            while($q = $res_quest->fetch_assoc()) { $questionarios[] = $q; }
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

elseif ($acao == 'criar') {
    $nome = $conn->real_escape_string($_POST['nome']);
    $cor = $_POST['cor'] ?? '#0d6efd';
    $codigo = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    $sql = "INSERT INTO comunidades (nome, codigo, cor) VALUES ('$nome', '$codigo', '$cor')";
    if ($conn->query($sql)) {
        $id = $conn->insert_id;
        $conn->query("INSERT INTO membros_comunidade (comunidade_id, usuario_id) VALUES ($id, $usuario_id)");
        echo json_encode(["status" => "sucesso", "codigo" => $codigo]);
    } else {
        echo json_encode(["status" => "erro", "msg" => $conn->error]);
    }
}

elseif ($acao == 'entrar') {
    $codigo = $conn->real_escape_string($_POST['codigo']);
    $res = $conn->query("SELECT id FROM comunidades WHERE codigo = '$codigo'");
    
    if ($res && $res->num_rows > 0) {
        $id_com = $res->fetch_assoc()['id'];
        $conn->query("INSERT IGNORE INTO membros_comunidade (comunidade_id, usuario_id) VALUES ($id_com, $usuario_id)");
        echo json_encode(["status" => "sucesso"]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Código inválido"]);
    }
}

elseif ($acao == 'criar_quiz') {
    $com_id = $_POST['comunidade_id'];
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    
    $sql = "INSERT INTO questionarios (comunidade_id, titulo, descricao, criador_id, publicado) 
            VALUES ($com_id, '$titulo', '$descricao', $usuario_id, 0)";
    
    if ($conn->query($sql)) echo json_encode(["status" => "sucesso"]);
    else echo json_encode(["status" => "erro", "msg" => $conn->error]);
}

elseif ($acao == 'editar_quiz_info') {
    $id = $_POST['quiz_id'];
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    
    $conn->query("UPDATE questionarios SET titulo='$titulo', descricao='$descricao' WHERE id=$id");
    echo json_encode(["status" => "sucesso"]);
}

elseif ($acao == 'publicar_quiz') {
    $id = $_POST['quiz_id'];
    $conn->query("UPDATE questionarios SET publicado = 1 WHERE id=$id");
    echo json_encode(["status" => "sucesso"]);
}

elseif ($acao == 'excluir_quiz') {
    $id = $_POST['quiz_id'];
    $conn->query("DELETE FROM alternativas WHERE pergunta_id IN (SELECT id FROM perguntas WHERE questionario_id = $id)");
    $conn->query("DELETE FROM perguntas WHERE questionario_id = $id");
    $conn->query("DELETE FROM questionarios WHERE id = $id");
    echo json_encode(["status" => "sucesso"]);
}

elseif ($acao == 'excluir_pergunta') {
    $p_id = $_POST['pergunta_id'];
    $conn->query("DELETE FROM alternativas WHERE pergunta_id = $p_id");
    $conn->query("DELETE FROM perguntas WHERE id = $p_id");
    echo json_encode(["status" => "sucesso"]);
}

elseif ($acao == 'get_quiz_full') {
    $quiz_id = $_GET['quiz_id'];
    $quiz = $conn->query("SELECT * FROM questionarios WHERE id = $quiz_id")->fetch_assoc();
    
    $perguntas = [];
    if($quiz) {
        $res_p = $conn->query("SELECT * FROM perguntas WHERE questionario_id = $quiz_id");
        while($p = $res_p->fetch_assoc()) {
            $p_id = $p['id'];
            $alts = [];
            $res_a = $conn->query("SELECT id, texto, e_correta FROM alternativas WHERE pergunta_id = $p_id");
            while($a = $res_a->fetch_assoc()) { $alts[] = $a; }
            $p['alternativas'] = $alts;
            $perguntas[] = $p;
        }
        echo json_encode(["status" => "sucesso", "quiz" => $quiz, "perguntas" => $perguntas]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Quiz não encontrado"]);
    }
}

elseif ($acao == 'salvar_pergunta') {
    $quiz_id = $_POST['quiz_id'];
    $enunciado = $conn->real_escape_string($_POST['enunciado']);
    $opcoes = $_POST['opcoes']; 
    $correta_idx = $_POST['correta'];

    $conn->query("INSERT INTO perguntas (questionario_id, enunciado) VALUES ($quiz_id, '$enunciado')");
    $pergunta_id = $conn->insert_id;

    foreach ($opcoes as $index => $texto) {
        $texto = $conn->real_escape_string($texto);
        $e_correta = ($index == $correta_idx) ? 1 : 0;
        $conn->query("INSERT INTO alternativas (pergunta_id, texto, e_correta) VALUES ($pergunta_id, '$texto', $e_correta)");
    }
    echo json_encode(["status" => "sucesso"]);
}

elseif ($acao == 'salvar_quiz_lista') {
    $quiz_id = $_POST['quiz_id'];
    $sql = "INSERT IGNORE INTO quizzes_salvos (usuario_id, questionario_id) VALUES ($usuario_id, $quiz_id)";
    
    if ($conn->query($sql)) {
        if ($conn->affected_rows > 0) echo json_encode(["status" => "sucesso", "msg" => "Quiz salvo na sua lista!"]);
        else echo json_encode(["status" => "sucesso", "msg" => "Este quiz já estava na sua lista."]);
    } else {
        echo json_encode(["status" => "erro", "msg" => $conn->error]);
    }
}

elseif ($acao == 'listar_meus_quizzes') {
    $sql = "SELECT q.*, u.nome as autor_nome, c.nome as comunidade_nome, c.cor as comunidade_cor
            FROM quizzes_salvos s
            JOIN questionarios q ON s.questionario_id = q.id
            JOIN comunidades c ON q.comunidade_id = c.id
            LEFT JOIN usuarios u ON q.criador_id = u.id
            WHERE s.usuario_id = $usuario_id
            ORDER BY s.data_salvo DESC";
            
    $result = $conn->query($sql);
    $lista = [];
    if($result) {
        while($row = $result->fetch_assoc()) { $lista[] = $row; }
    }
    echo json_encode($lista);
}

elseif ($acao == 'remover_salvo') {
    $quiz_id = $_POST['quiz_id'];
    $conn->query("DELETE FROM quizzes_salvos WHERE usuario_id = $usuario_id AND questionario_id = $quiz_id");
    echo json_encode(["status" => "sucesso"]);
}

else {
    echo json_encode([
        "status" => "erro", 
        "msg" => "Ação inválida ou não enviada. Ação recebida: " . $acao
    ]);
}
?>