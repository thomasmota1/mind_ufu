<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Criar Flashcards</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
function adicionarFlashcard() {
    const container = document.getElementById("flashcards-container");

    const bloco = document.createElement("div");
    bloco.classList.add("card", "p-3", "mb-3");

    bloco.innerHTML = `
        <div class="mb-3">
            <label>Pergunta</label>
            <textarea name="pergunta[]" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>Resposta</label>
            <textarea name="resposta[]" class="form-control" required></textarea>
        </div>

        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">
            Remover
        </button>
    `;

    container.appendChild(bloco);
}
</script>

</head>
<body class="bg-light p-5">

<div class="container">
    <h2 class="mb-4">Criar Novo Baralho</h2>

    <form method="POST" action="php/salvar_flashcards.php">
        
        <div class="mb-3">
            <label class="form-label">Tema</label>
            <input type="text" name="deck_nome" class="form-control" required>
        </div>

        <hr>

        <h5>Flashcards</h5>

        <div id="flashcards-container">

            <div class="card p-3 mb-3">
                <div class="mb-3">
                    <label>Pergunta</label>
                    <textarea name="pergunta[]" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Resposta</label>
                    <textarea name="resposta[]" class="form-control" required></textarea>
                </div>
            </div>

        </div>

        <button type="button" class="btn btn-secondary mb-3" onclick="adicionarFlashcard()">
            + Adicionar Pergunta
        </button>

        <br>

        <button class="btn btn-primary">Salvar Baralho</button>
    </form>
</div>

</body>
</html>