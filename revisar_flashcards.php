<?php
require_once "php/db.php";

$deck_id = $_GET['deck'];

$result = $conn->query("SELECT pergunta, resposta FROM flashcards WHERE deck_id = $deck_id");

$cards = [];

while($row = $result->fetch_assoc()) {
    $cards[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Revisão</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
}
.close-btn {
    position: absolute;
    top: 30px;
    right: 40px;
    font-size: 28px;
    cursor: pointer;
}
.card-scene {
    width: 100%;
    max-width: 750px;
    height: 450px;
    perspective: 1200px;
    cursor: pointer;
    margin: auto;
}
.card-flip {
    width: 100%;
    height: 100%;
    transition: transform 0.6s ease;
    transform-style: preserve-3d;
    position: relative;
    border-radius: 30px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}
.card-flip.is-flipped {
    transform: rotateY(180deg);
}
.card-face {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 50px;
    text-align: center;
}
.card-front { background: white; }
.card-back {
    background: #3b82f6;
    color: white;
    transform: rotateY(180deg);
}
.card-text {
    font-size: 28px;
    font-weight: 500;
}
</style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100 position-relative">

<div class="close-btn" onclick="voltar()">✕</div>

<div class="text-center">

    <div class="card-scene" onclick="flipCard()">
        <div class="card-flip" id="card">
            <div class="card-face card-front">
                <div>
                    <p class="text-muted mb-3">Pergunta</p>
                    <div class="card-text" id="pergunta"></div>
                    <small class="text-muted mt-4 d-block">Clique para virar</small>
                </div>
            </div>

            <div class="card-face card-back">
                <div>
                    <p class="opacity-75 mb-3">Resposta</p>
                    <div class="card-text fw-bold" id="resposta"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center gap-3">
        <button class="btn btn-outline-secondary px-4" onclick="anterior()">← Anterior</button>
        <button class="btn btn-outline-primary px-4" onclick="proximo()">Próximo →</button>
    </div>

</div>

<script>
const cards = <?php echo json_encode($cards); ?>;

let index = 0;

function mostrarCard() {
    if(cards.length === 0) {
        document.getElementById("pergunta").innerText = "Nenhum flashcard encontrado.";
        document.getElementById("resposta").innerText = "";
        return;
    }

    document.getElementById("pergunta").innerText = cards[index].pergunta;
    document.getElementById("resposta").innerText = cards[index].resposta;
    document.getElementById("card").classList.remove("is-flipped");
}

function proximo() {
    if(index < cards.length - 1) index++;
    mostrarCard();
}

function anterior() {
    if(index > 0) index--;
    mostrarCard();
}

function flipCard() {
    document.getElementById("card").classList.toggle("is-flipped");
}

function voltar() {
    window.location.href = "flashcards.php";
}

mostrarCard();
</script>

</body>
</html>