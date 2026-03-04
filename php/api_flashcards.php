<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include('db.php');

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// =========================
// LISTAR MEUS DECKS
// =========================
if ($acao == 'listar_meus_decks') {
    $usuario_id = (int)($_GET['usuario_id'] ?? 0);

    if ($usuario_id <= 0) {
        echo json_encode(["status" => "erro", "msg" => "Usuário não identificado"]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT d.*,
               (SELECT COUNT(*) FROM flashcards WHERE deck_id = d.id) as total_cards,
               u.nome as autor_nome
        FROM decks d
        LEFT JOIN usuarios u ON d.usuario_id = u.id
        WHERE d.usuario_id = ?
        ORDER BY d.created_at DESC
    ");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $decks = [];
    while ($row = $result->fetch_assoc()) {
        $decks[] = $row;
    }

    echo json_encode($decks);
}

// =========================
// LISTAR DECKS PÚBLICOS
// =========================
elseif ($acao == 'listar_publicos') {
    $busca = trim($_GET['busca'] ?? '');
    $usuario_id = (int)($_GET['usuario_id'] ?? 0);

    $sql = "
        SELECT d.*,
               (SELECT COUNT(*) FROM flashcards WHERE deck_id = d.id) as total_cards,
               u.nome as autor_nome,
               u.foto as autor_foto
        FROM decks d
        LEFT JOIN usuarios u ON d.usuario_id = u.id
        WHERE d.publico = 1
    ";

    if (!empty($busca)) {
        $busca = $conn->real_escape_string($busca);
        $sql .= " AND (d.nome LIKE '%$busca%' OR d.descricao LIKE '%$busca%' OR u.nome LIKE '%$busca%')";
    }

    // Excluir os próprios decks se usuario_id fornecido
    if ($usuario_id > 0) {
        $sql .= " AND d.usuario_id != $usuario_id";
    }

    $sql .= " ORDER BY d.created_at DESC LIMIT 50";

    $result = $conn->query($sql);

    $decks = [];
    while ($row = $result->fetch_assoc()) {
        $decks[] = $row;
    }

    echo json_encode($decks);
}

// =========================
// LISTAR DECKS DE UM USUÁRIO (públicos)
// =========================
elseif ($acao == 'listar_decks_usuario') {
    $usuario_id = (int)($_GET['usuario_id'] ?? 0);

    if ($usuario_id <= 0) {
        echo json_encode([]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT d.*,
               (SELECT COUNT(*) FROM flashcards WHERE deck_id = d.id) as total_cards
        FROM decks d
        WHERE d.usuario_id = ? AND d.publico = 1
        ORDER BY d.created_at DESC
    ");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $decks = [];
    while ($row = $result->fetch_assoc()) {
        $decks[] = $row;
    }

    echo json_encode($decks);
}

// =========================
// CRIAR DECK
// =========================
elseif ($acao == 'criar_deck') {
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $publico = (int)($_POST['publico'] ?? 0);
    $cor = $_POST['cor'] ?? '#4f46e5';

    if ($usuario_id <= 0) {
        echo json_encode(["status" => "erro", "msg" => "Usuário não identificado"]);
        exit;
    }

    if (empty($nome)) {
        echo json_encode(["status" => "erro", "msg" => "Nome obrigatório"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO decks (nome, descricao, usuario_id, publico, cor) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $nome, $descricao, $usuario_id, $publico, $cor);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao criar deck"]);
    }
}

// =========================
// ATUALIZAR DECK
// =========================
elseif ($acao == 'atualizar_deck') {
    $deck_id = (int)($_POST['deck_id'] ?? 0);
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $publico = (int)($_POST['publico'] ?? 0);
    $cor = $_POST['cor'] ?? '#4f46e5';

    // Verificar propriedade
    $stmt = $conn->prepare("SELECT id FROM decks WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $deck_id, $usuario_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Deck não encontrado"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE decks SET nome = ?, descricao = ?, publico = ?, cor = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $nome, $descricao, $publico, $cor, $deck_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso"]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao atualizar"]);
    }
}

// =========================
// EXCLUIR DECK
// =========================
elseif ($acao == 'excluir_deck') {
    $deck_id = (int)($_POST['deck_id'] ?? 0);
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);

    // Verificar propriedade
    $stmt = $conn->prepare("SELECT id FROM decks WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $deck_id, $usuario_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Deck não encontrado"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM decks WHERE id = ?");
    $stmt->bind_param("i", $deck_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso"]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao excluir"]);
    }
}

// =========================
// OBTER DECK COM CARDS
// =========================
elseif ($acao == 'get_deck') {
    $deck_id = (int)($_GET['deck_id'] ?? 0);
    $usuario_id = (int)($_GET['usuario_id'] ?? 0);

    // Buscar deck
    $stmt = $conn->prepare("
        SELECT d.*, u.nome as autor_nome
        FROM decks d
        LEFT JOIN usuarios u ON d.usuario_id = u.id
        WHERE d.id = ?
    ");
    $stmt->bind_param("i", $deck_id);
    $stmt->execute();
    $deck = $stmt->get_result()->fetch_assoc();

    if (!$deck) {
        echo json_encode(["status" => "erro", "msg" => "Deck não encontrado"]);
        exit;
    }

    // Verificar permissão (dono ou público)
    if ($deck['usuario_id'] != $usuario_id && $deck['publico'] != 1) {
        echo json_encode(["status" => "erro", "msg" => "Sem permissão"]);
        exit;
    }

    // Buscar flashcards
    $stmt = $conn->prepare("SELECT id, pergunta, resposta FROM flashcards WHERE deck_id = ? ORDER BY id");
    $stmt->bind_param("i", $deck_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cards = [];
    while ($row = $result->fetch_assoc()) {
        $cards[] = $row;
    }

    $deck['cards'] = $cards;
    $deck['is_owner'] = ($deck['usuario_id'] == $usuario_id);

    echo json_encode(["status" => "sucesso", "deck" => $deck]);
}

// =========================
// ADICIONAR FLASHCARD
// =========================
elseif ($acao == 'adicionar_card') {
    $deck_id = (int)($_POST['deck_id'] ?? 0);
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);
    $pergunta = trim($_POST['pergunta'] ?? '');
    $resposta = trim($_POST['resposta'] ?? '');

    // Verificar propriedade do deck
    $stmt = $conn->prepare("SELECT id FROM decks WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $deck_id, $usuario_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Sem permissão"]);
        exit;
    }

    if (empty($pergunta) || empty($resposta)) {
        echo json_encode(["status" => "erro", "msg" => "Preencha pergunta e resposta"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO flashcards (deck_id, pergunta, resposta) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $deck_id, $pergunta, $resposta);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao adicionar"]);
    }
}

// =========================
// ATUALIZAR FLASHCARD
// =========================
elseif ($acao == 'atualizar_card') {
    $card_id = (int)($_POST['card_id'] ?? 0);
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);
    $pergunta = trim($_POST['pergunta'] ?? '');
    $resposta = trim($_POST['resposta'] ?? '');

    // Verificar propriedade via deck
    $stmt = $conn->prepare("
        SELECT f.id FROM flashcards f
        JOIN decks d ON f.deck_id = d.id
        WHERE f.id = ? AND d.usuario_id = ?
    ");
    $stmt->bind_param("ii", $card_id, $usuario_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Sem permissão"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE flashcards SET pergunta = ?, resposta = ? WHERE id = ?");
    $stmt->bind_param("ssi", $pergunta, $resposta, $card_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso"]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao atualizar"]);
    }
}

// =========================
// EXCLUIR FLASHCARD
// =========================
elseif ($acao == 'excluir_card') {
    $card_id = (int)($_POST['card_id'] ?? 0);
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);

    // Verificar propriedade via deck
    $stmt = $conn->prepare("
        SELECT f.id FROM flashcards f
        JOIN decks d ON f.deck_id = d.id
        WHERE f.id = ? AND d.usuario_id = ?
    ");
    $stmt->bind_param("ii", $card_id, $usuario_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Sem permissão"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM flashcards WHERE id = ?");
    $stmt->bind_param("i", $card_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso"]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao excluir"]);
    }
}

// =========================
// AÇÃO INVÁLIDA
// =========================
else {
    echo json_encode(["status" => "erro", "msg" => "Ação inválida"]);
}

$conn->close();
?>
