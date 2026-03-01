<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "mysql", "mind_ufu");

if ($conn->connect_error) {
    die(json_encode(["erro" => "Erro conexão"]));
}

$usuario_id = 1;


$input = json_decode(file_get_contents("php://input"), true);

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

    $ano = $_GET['ano'];
    $mes = $_GET['mes'];

    $stmt = $conn->prepare("
        SELECT * FROM eventos
        WHERE usuario_id=?
        AND YEAR(data_inicio)=?
        AND MONTH(data_inicio)=?
        ORDER BY data_inicio
    ");

    $stmt->bind_param("iii",$usuario_id,$ano,$mes);
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

    $stmt = $conn->prepare("
        UPDATE eventos
        SET titulo=?
        WHERE id=? AND usuario_id=?
    ");

    $stmt->bind_param("sii",$titulo,$id,$usuario_id);
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