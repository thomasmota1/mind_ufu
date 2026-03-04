<?php
header("Content-Type: application/json; charset=utf-8");
include('db.php');

$usuario_id = (int)($_GET['usuario_id'] ?? 0);

if ($usuario_id <= 0) {
    echo json_encode([]);
    exit;
}

$hierarquia = [];

$stmt = $conn->prepare("SELECT * FROM disciplinas WHERE usuario_id = ? ORDER BY nome ASC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res_disc = $stmt->get_result();

while($disc = $res_disc->fetch_assoc()) {
    $disc['pastas'] = [];

    $stmt2 = $conn->prepare("SELECT * FROM pastas WHERE disciplina_id = ?");
    $stmt2->bind_param("i", $disc['id']);
    $stmt2->execute();
    $res_pastas = $stmt2->get_result();

    while($pasta = $res_pastas->fetch_assoc()) {
        $pasta['paginas'] = [];

        $stmt3 = $conn->prepare("SELECT id, titulo FROM paginas WHERE pasta_id = ?");
        $stmt3->bind_param("i", $pasta['id']);
        $stmt3->execute();
        $res_pags = $stmt3->get_result();

        while($pag = $res_pags->fetch_assoc()) {
            $pasta['paginas'][] = $pag;
        }
        $disc['pastas'][] = $pasta;
    }
    $hierarquia[] = $disc;
}

echo json_encode($hierarquia);
?>
