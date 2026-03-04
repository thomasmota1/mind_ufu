<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="mindufu_logo_2.png" type="image/png">
    <title>Estudar Deck - Mind UFU</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        .deck-header {
            background: linear-gradient(135deg, var(--deck-color, #4f46e5), var(--deck-color-light, #3b82f6));
            border-radius: 20px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
        }
        .btn-voltar {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 10px;
        }
        .btn-voltar:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }

        /* Card de Estudo */
        .study-container {
            max-width: 700px;
            margin: 0 auto;
        }
        .card-scene {
            width: 100%;
            height: 350px;
            perspective: 1200px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .card-flip {
            width: 100%;
            height: 100%;
            transition: transform 0.6s ease;
            transform-style: preserve-3d;
            position: relative;
            border-radius: 24px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }
        .card-flip.is-flipped {
            transform: rotateY(180deg);
        }
        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
        }
        .card-front {
            background: white;
            border: 2px solid #e2e8f0;
        }
        .card-back {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            transform: rotateY(180deg);
        }
        .card-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            margin-bottom: 16px;
        }
        .card-text {
            font-size: 24px;
            font-weight: 500;
            line-height: 1.4;
        }
        .card-hint {
            position: absolute;
            bottom: 20px;
            font-size: 13px;
            opacity: 0.5;
        }
        .progress-study {
            height: 6px;
            border-radius: 3px;
            background: #e2e8f0;
            margin-bottom: 10px;
        }
        .progress-study .progress-bar {
            border-radius: 3px;
        }

        /* Lista de Cards */
        .cards-list {
            max-width: 800px;
            margin: 0 auto;
        }
        .card-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .card-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .card-item-actions {
            opacity: 0;
            transition: opacity 0.2s;
        }
        .card-item:hover .card-item-actions {
            opacity: 1;
        }

        /* Tabs */
        .mode-tabs {
            display: inline-flex;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
        }
        .mode-tabs .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            color: #64748b;
            font-weight: 500;
        }
        .mode-tabs .btn.active {
            background: white;
            color: #1e40af;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .empty-cards {
            text-align: center;
            padding: 60px 20px;
            background: #f8fafc;
            border-radius: 16px;
            border: 2px dashed #e2e8f0;
        }
    </style>
</head>

<body>
<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content p-4">

        <div id="loading" class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted">Carregando deck...</p>
        </div>

        <div id="deck-content" style="display: none;">

            <!-- Header do Deck -->
            <div class="deck-header" id="deck-header">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <a href="flashcards.php" class="btn-voltar">
                        <i class="bi bi-arrow-left me-2"></i>Voltar
                    </a>
                    <div id="owner-actions" style="display: none;">
                        <button class="btn-voltar" onclick="abrirModalAddCard()">
                            <i class="bi bi-plus-lg me-1"></i>Add Card
                        </button>
                    </div>
                </div>
                <h2 class="fw-bold mb-2" id="deck-titulo">...</h2>
                <p class="opacity-75 mb-0" id="deck-descricao"></p>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-25" id="deck-count">0 cards</span>
                    <span class="badge bg-white bg-opacity-25 ms-2" id="deck-autor"></span>
                </div>
            </div>

            <!-- Tabs de Modo -->
            <div class="text-center mb-4">
                <div class="mode-tabs">
                    <button class="btn active" onclick="setMode('estudar')" id="btn-estudar">
                        <i class="bi bi-play-circle me-2"></i>Estudar
                    </button>
                    <button class="btn" onclick="setMode('lista')" id="btn-lista">
                        <i class="bi bi-list-ul me-2"></i>Ver Lista
                    </button>
                </div>
            </div>

            <!-- Modo Estudar -->
            <div id="mode-estudar" class="study-container">
                <div id="study-empty" class="empty-cards" style="display: none;">
                    <i class="bi bi-card-text fs-1 text-muted"></i>
                    <h5 class="mt-3 fw-bold">Nenhum card neste deck</h5>
                    <p class="text-muted">Adicione flashcards para comecar a estudar</p>
                    <button class="btn btn-primary" onclick="abrirModalAddCard()" id="btn-add-empty" style="display: none;">
                        <i class="bi bi-plus-lg me-2"></i>Adicionar Card
                    </button>
                </div>

                <div id="study-area" style="display: none;">
                    <div class="progress-study">
                        <div class="progress-bar bg-primary" id="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="text-center text-muted small mb-3">
                        <span id="card-atual">1</span> de <span id="card-total">0</span>
                    </div>

                    <div class="card-scene" onclick="flipCard()">
                        <div class="card-flip" id="card">
                            <div class="card-face card-front">
                                <div>
                                    <div class="card-label">Pergunta</div>
                                    <div class="card-text" id="pergunta"></div>
                                    <div class="card-hint">Clique para ver a resposta</div>
                                </div>
                            </div>
                            <div class="card-face card-back">
                                <div>
                                    <div class="card-label">Resposta</div>
                                    <div class="card-text" id="resposta"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-outline-secondary px-4" onclick="anterior()" id="btn-anterior">
                            <i class="bi bi-chevron-left me-2"></i>Anterior
                        </button>
                        <button class="btn btn-primary px-4" onclick="proximo()" id="btn-proximo">
                            Proximo<i class="bi bi-chevron-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modo Lista -->
            <div id="mode-lista" class="cards-list" style="display: none;">
                <div id="lista-cards"></div>
            </div>

        </div>

    </main>
</div>

<!-- Modal Adicionar/Editar Card -->
<div class="modal fade" id="modalCard" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modalCardTitulo">Novo Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cardId">
                <div class="mb-3">
                    <label class="form-label">Pergunta (Frente)</label>
                    <textarea id="cardPergunta" class="form-control" rows="3" placeholder="Digite a pergunta..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Resposta (Verso)</label>
                    <textarea id="cardResposta" class="form-control" rows="3" placeholder="Digite a resposta..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary w-100" onclick="salvarCard()">
                    <i class="bi bi-check-lg me-2"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusao Card -->
<div class="modal fade" id="modalExcluirCard" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold text-danger">Excluir Card</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="excluirCardId">
                <p class="text-muted mb-0">Tem certeza que deseja excluir este card?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarExcluirCard()">Excluir</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastNotif" class="toast align-items-center border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMsg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<script>
const API_URL = 'php/api_flashcards.php';

function mostrarToast(msg, tipo = "success") {
    const toast = document.getElementById('toastNotif');
    toast.className = 'toast align-items-center border-0 text-bg-' + tipo;
    document.getElementById('toastMsg').textContent = msg;
    new bootstrap.Toast(toast).show();
}
let deckId = null;
let deckData = null;
let cards = [];
let index = 0;
let isOwner = false;

function getUsuarioId() {
    const userData = JSON.parse(localStorage.getItem('usuario') || '{}');
    return userData.id || 0;
}

$(document).ready(() => {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('overflow', '');

    if(typeof loadSidebar === 'function') loadSidebar();

    if(!getUsuarioId()) {
        window.location.href = 'index.html';
        return;
    }

    // Obter ID do deck da URL
    const params = new URLSearchParams(window.location.search);
    deckId = params.get('id');

    if(!deckId) {
        window.location.href = 'flashcards.php';
        return;
    }

    carregarDeck();
});

function carregarDeck() {
    $.get(API_URL, { acao: 'get_deck', deck_id: deckId, usuario_id: getUsuarioId() }, function(res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;

        if(data.status !== 'sucesso') {
            mostrarToast(data.msg || 'Erro ao carregar deck', 'danger');
            setTimeout(() => window.location.href = 'flashcards.php', 1500);
            return;
        }

        deckData = data.deck;
        cards = deckData.cards || [];
        isOwner = deckData.is_owner;

        // Definir cores
        const cores = {
            '#4f46e5': ['#4f46e5', '#3b82f6'],
            '#9333ea': ['#9333ea', '#6366f1'],
            '#0ea5e9': ['#0ea5e9', '#06b6d4'],
            '#10b981': ['#10b981', '#059669'],
            '#f59e0b': ['#f59e0b', '#d97706'],
            '#ef4444': ['#ef4444', '#dc2626']
        };
        const cor = cores[deckData.cor] || cores['#4f46e5'];
        document.documentElement.style.setProperty('--deck-color', cor[0]);
        document.documentElement.style.setProperty('--deck-color-light', cor[1]);
        $('#deck-header').css('background', `linear-gradient(135deg, ${cor[0]}, ${cor[1]})`);

        // Preencher informacoes
        $('#deck-titulo').text(deckData.nome);
        $('#deck-descricao').text(deckData.descricao || '');
        $('#deck-count').html(`<i class="bi bi-card-text me-1"></i>${cards.length} cards`);
        $('#deck-autor').html(`<i class="bi bi-person me-1"></i>${deckData.autor_nome || 'Anonimo'}`);

        // Mostrar acoes de dono
        if(isOwner) {
            $('#owner-actions').show();
            $('#btn-add-empty').show();
        }

        $('#loading').hide();
        $('#deck-content').show();

        atualizarModo();
    }).fail(function() {
        mostrarToast('Erro ao carregar deck', 'danger');
        setTimeout(() => window.location.href = 'flashcards.php', 1500);
    });
}

function setMode(mode) {
    if(mode === 'estudar') {
        $('#btn-estudar').addClass('active');
        $('#btn-lista').removeClass('active');
        $('#mode-estudar').show();
        $('#mode-lista').hide();
    } else {
        $('#btn-estudar').removeClass('active');
        $('#btn-lista').addClass('active');
        $('#mode-estudar').hide();
        $('#mode-lista').show();
        renderizarLista();
    }
}

function atualizarModo() {
    if(cards.length === 0) {
        $('#study-empty').show();
        $('#study-area').hide();
    } else {
        $('#study-empty').hide();
        $('#study-area').show();
        index = 0;
        mostrarCard();
    }
    // Atualizar lista se estiver visivel
    if($('#mode-lista').is(':visible')) {
        renderizarLista();
    }
}

function mostrarCard() {
    if(cards.length === 0) return;

    $('#pergunta').text(cards[index].pergunta);
    $('#resposta').text(cards[index].resposta);
    $('#card').removeClass('is-flipped');

    $('#card-atual').text(index + 1);
    $('#card-total').text(cards.length);

    const progresso = ((index + 1) / cards.length) * 100;
    $('#progress-bar').css('width', progresso + '%');

    // Desabilitar botoes
    $('#btn-anterior').prop('disabled', index === 0);
    $('#btn-proximo').text(index === cards.length - 1 ? 'Reiniciar' : 'Proximo');
    if(index === cards.length - 1) {
        $('#btn-proximo').html('Reiniciar <i class="bi bi-arrow-clockwise ms-2"></i>');
    } else {
        $('#btn-proximo').html('Proximo <i class="bi bi-chevron-right ms-2"></i>');
    }
}

function flipCard() {
    $('#card').toggleClass('is-flipped');
}

function proximo() {
    if(index < cards.length - 1) {
        index++;
    } else {
        index = 0; // Reiniciar
    }
    mostrarCard();
}

function anterior() {
    if(index > 0) {
        index--;
        mostrarCard();
    }
}

function renderizarLista() {
    let html = '';

    if(cards.length === 0) {
        html = `
            <div class="empty-cards">
                <i class="bi bi-card-text fs-1 text-muted"></i>
                <h5 class="mt-3 fw-bold">Nenhum card</h5>
                ${isOwner ? '<button class="btn btn-primary mt-2" onclick="abrirModalAddCard()"><i class="bi bi-plus-lg me-2"></i>Adicionar</button>' : ''}
            </div>`;
    } else {
        cards.forEach((card, i) => {
            html += `
            <div class="card-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark mb-2">
                            <span class="badge bg-primary me-2">${i + 1}</span>
                            ${card.pergunta}
                        </div>
                        <div class="text-muted">${card.resposta}</div>
                    </div>
                    ${isOwner ? `
                    <div class="card-item-actions ms-3">
                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="abrirModalEditCard(${card.id}, '${card.pergunta.replace(/'/g, "\\'")}', '${card.resposta.replace(/'/g, "\\'")}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="abrirModalExcluirCard(${card.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>` : ''}
                </div>
            </div>`;
        });
    }

    $('#lista-cards').html(html);
}

function abrirModalAddCard() {
    if(!isOwner) return;
    $('#modalCardTitulo').text('Novo Card');
    $('#cardId').val('');
    $('#cardPergunta').val('');
    $('#cardResposta').val('');

    const modal = new bootstrap.Modal(document.getElementById('modalCard'));
    modal.show();
    setTimeout(() => $('#cardPergunta').focus(), 300);
}

function abrirModalEditCard(id, pergunta, resposta) {
    if(!isOwner) return;
    $('#modalCardTitulo').text('Editar Card');
    $('#cardId').val(id);
    $('#cardPergunta').val(pergunta);
    $('#cardResposta').val(resposta);

    const modal = new bootstrap.Modal(document.getElementById('modalCard'));
    modal.show();
}

function salvarCard() {
    const id = $('#cardId').val();
    const pergunta = $('#cardPergunta').val().trim();
    const resposta = $('#cardResposta').val().trim();

    if(!pergunta || !resposta) {
        mostrarToast('Preencha pergunta e resposta', 'warning');
        return;
    }

    const dados = {
        usuario_id: getUsuarioId(),
        pergunta: pergunta,
        resposta: resposta
    };

    if(id) {
        dados.acao = 'atualizar_card';
        dados.card_id = id;
    } else {
        dados.acao = 'adicionar_card';
        dados.deck_id = deckId;
    }

    $.post(API_URL, dados, function(res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCard'));
        if(modal) modal.hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');

        if(data.status === 'sucesso') {
            carregarDeck(); // Recarregar deck
            mostrarToast('Card salvo com sucesso!');
        } else {
            mostrarToast(data.msg || 'Erro ao salvar', 'danger');
        }
    });
}

function abrirModalExcluirCard(id) {
    $('#excluirCardId').val(id);
    const modal = new bootstrap.Modal(document.getElementById('modalExcluirCard'));
    modal.show();
}

function confirmarExcluirCard() {
    const id = $('#excluirCardId').val();

    $.post(API_URL, { acao: 'excluir_card', card_id: id, usuario_id: getUsuarioId() }, function(res) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluirCard'));
        if(modal) modal.hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');

        carregarDeck();
        mostrarToast('Card excluido com sucesso!');
    });
}

// Atalhos de teclado
$(document).keydown(function(e) {
    if($('.modal.show').length > 0) return; // Ignorar se modal aberto

    if(e.key === ' ' || e.key === 'Enter') {
        e.preventDefault();
        flipCard();
    } else if(e.key === 'ArrowRight') {
        proximo();
    } else if(e.key === 'ArrowLeft') {
        anterior();
    }
});
</script>

</body>
</html>
