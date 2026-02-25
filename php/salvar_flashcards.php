<?php
require_once "db.php";

$deck_nome = $_POST['deck_nome'];
$perguntas = $_POST['pergunta'];
$respostas = $_POST['resposta'];

/* Criar deck */
$conn->query("INSERT INTO decks (nome) VALUES ('$deck_nome')");
$deck_id = $conn->insert_id;

/* Inserir todos flashcards */
for($i = 0; $i < count($perguntas); $i++) {

    $pergunta = $perguntas[$i];
    $resposta = $respostas[$i];

    $conn->query("
        INSERT INTO flashcards (deck_id, pergunta, resposta)
        VALUES ($deck_id, '$pergunta', '$resposta')
    ");
}

header("Location: ../flashcards.php");
exit;