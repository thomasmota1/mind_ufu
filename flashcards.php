<?php
require_once "php/db.php";
$decks = $conn->query("SELECT * FROM decks");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Flashcards - Mind UFU</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style-global.css">

<style>
.deck-card:hover {
    transform: translateY(-6px);
}
.deck-card {
    border: none;
    border-radius: 22px;
    padding: 30px;
    color: white;
    position: relative;
    overflow: hidden;
    transition: all 0.25s ease;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.deck-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

/* Gradientes diferentes */
.deck-1 { background: linear-gradient(135deg, #4f46e5, #3b82f6); }
.deck-2 { background: linear-gradient(135deg, #9333ea, #6366f1); }
.deck-3 { background: linear-gradient(135deg, #0ea5e9, #06b6d4); }
.deck-4 { background: linear-gradient(135deg, #10b981, #059669); }

.deck-title {
    font-size: 20px;
    font-weight: 600;
}

.deck-count {
    font-size: 14px;
    opacity: 0.85;
}
</style>
</head>

<body>

<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content">
        <div class="container-fluid p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Seus Baralhos</h2>

                <a href="criar_flashcard.php" class="btn btn-primary">
                    + Criar Baralho
                </a>
            </div>

            <div class="row g-4">

                <?php 
$cor = 1;
while($deck = $decks->fetch_assoc()): 

    // Contar flashcards
    $count = $conn->query("SELECT COUNT(*) as total FROM flashcards WHERE deck_id = {$deck['id']}");
    $total = $count->fetch_assoc()['total'];

    $classeCor = "deck-" . $cor;
    $cor++;
    if($cor > 4) $cor = 1;
?>

<div class="col-md-4">
    <div class="deck-card <?= $classeCor ?>">

        <form method="POST" action="php/excluir_deck.php" class="position-absolute top-0 end-0 m-3">
            <input type="hidden" name="id" value="<?= $deck['id'] ?>">
            <button class="btn btn-sm btn-light">✕</button>
        </form>

        <div>
            <div class="deck-title"><?= $deck['nome'] ?></div>
            <div class="deck-count"><?= $total ?> flashcards</div>
        </div>

        <a href="revisar_flashcards.php?deck=<?= $deck['id'] ?>" 
           class="btn btn-light mt-3">
            Estudar
        </a>

    </div>
</div>

<?php endwhile; ?>

            </div>

        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/sidebar.js"></script>
<script>
$(document).ready(() => {
    if(typeof loadSidebar === 'function') loadSidebar();
});
</script>

</body>
</html>