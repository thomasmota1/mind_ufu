<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "mysql", "mind_ufu");

if ($conn->connect_error) {
    die(json_encode(["erro" => "Erro conexão"]));
}

// Ler JSON do body primeiro
$input = json_decode(file_get_contents("php://input"), true);

// Obter usuario_id do request ou do JSON body
$usuario_id = (int)($_REQUEST['usuario_id'] ?? $input['usuario_id'] ?? 0);

// Se não tiver usuario_id válido, retornar erro
if ($usuario_id <= 0) {
    echo json_encode(["erro" => "Usuário não identificado"]);
    exit;
}

$acao = $_REQUEST['acao'] ?? $input['acao'] ?? '';

switch ($acao) {

case "criar":

    $titulo = $input['titulo'];
    $descricao = $input['descricao'];
    $data = $input['data_inicio'];
    $cor = $input['cor'];

    $stmt = $conn->prepare("
        INSERT INTO eventos (usuario_id,titulo,descricao,data_inicio,cor)
        VALUES (?,?,?,?,?)
    ");

    $stmt->bind_param("issss",$usuario_id,$titulo,$descricao,$data,$cor);
    $stmt->execute();

    echo json_encode(["status"=>"ok"]);
break;

case "listar":

    $ano = $_GET['ano'] ?? null;
    $mes = $_GET['mes'] ?? null;

    // Se ano e mes forem fornecidos, filtra por mês
    if($ano && $mes) {
        $stmt = $conn->prepare("
            SELECT * FROM eventos
            WHERE usuario_id=?
            AND YEAR(data_inicio)=?
            AND MONTH(data_inicio)=?
            ORDER BY data_inicio
        ");
        $stmt->bind_param("iii",$usuario_id,$ano,$mes);
    } else {
        // Se não, lista todos os eventos futuros
        $stmt = $conn->prepare("
            SELECT * FROM eventos
            WHERE usuario_id=?
            AND data_inicio >= NOW()
            ORDER BY data_inicio
        ");
        $stmt->bind_param("i",$usuario_id);
    }

    $stmt->execute();

    $res = $stmt->get_result();
    $dados = [];

    while($row = $res->fetch_assoc()){
        $dados[] = $row;
    }

    echo json_encode($dados);
break;

case "editar":

    $id = $input['id'];
    $titulo = $input['titulo'];
    $descricao = $input['descricao'];
    $data = $input['data_inicio'];
    $cor = $input['cor'];

    $stmt = $conn->prepare("
        UPDATE eventos
        SET titulo=?, descricao=?, data_inicio=?, cor=?
        WHERE id=? AND usuario_id=?
    ");

    $stmt->bind_param("ssssii",
        $titulo,
        $descricao,
        $data,
        $cor,
        $id,
        $usuario_id
    );

    $stmt->execute();

    echo json_encode(["status"=>"editado"]);
break;

case "excluir":

    $id = $_GET['id'];

    $stmt = $conn->prepare("
        DELETE FROM eventos
        WHERE id=? AND usuario_id=?
    ");

    $stmt->bind_param("ii",$id,$usuario_id);
    $stmt->execute();

    echo json_encode(["status"=>"excluido"]);
break;
}