<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcards - Mind UFU</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        .deck-card {
            border: none;
            border-radius: 20px;
            padding: 24px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        .deck-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .deck-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .deck-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .deck-desc {
            font-size: 13px;
            opacity: 0.85;
            flex-grow: 1;
        }
        .deck-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            font-size: 13px;
        }
        .deck-badge {
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .deck-actions {
            position: absolute;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 6px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .deck-card:hover .deck-actions {
            opacity: 1;
        }
        .deck-actions button {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.9);
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .deck-actions button:hover {
            transform: scale(1.1);
        }
        .deck-actions button.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }
        .tab-custom {
            border: none;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 6px;
        }
        .tab-custom .nav-link {
            border: none;
            border-radius: 8px;
            color: #64748b;
            font-weight: 500;
            padding: 10px 20px;
        }
        .tab-custom .nav-link.active {
            background: white;
            color: #1e40af;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
        }
        .search-box {
            position: relative;
        }
        .search-box input {
            padding-left: 45px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
        }
        .search-box input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .public-deck-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .public-deck-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .author-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            overflow: hidden;
        }
        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>
<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content">
        <div class="container-fluid p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Flashcards</h2>
                    <p class="text-muted small mb-0">Estude com cartoes de memoria</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4" onclick="abrirModalCriar()">
                    <i class="bi bi-plus-lg me-2"></i>Novo Deck
                </button>
            </div>

            <!-- Tabs -->
            <ul class="nav tab-custom mb-4" id="flashcardTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#meus-decks">
                        <i class="bi bi-collection me-2"></i>Meus Decks
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#explorar">
                        <i class="bi bi-compass me-2"></i>Explorar Publicos
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Meus Decks -->
                <div class="tab-pane fade show active" id="meus-decks">
                    <div id="loading-meus" class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                    </div>
                    <div id="lista-meus-decks" class="row g-4"></div>
                </div>

                <!-- Explorar -->
                <div class="tab-pane fade" id="explorar">
                    <div class="search-box mb-4" style="max-width: 400px;">
                        <i class="bi bi-search"></i>
                        <input type="text" id="busca-publica" class="form-control" placeholder="Buscar por tema ou autor..." onkeyup="buscarPublicos()">
                    </div>
                    <div id="loading-publicos" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary"></div>
                    </div>
                    <div id="lista-publicos" class="row g-4"></div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Modal Criar/Editar Deck -->
<div class="modal fade" id="modalDeck" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modalDeckTitulo">Novo Deck</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deckId">
                <div class="mb-3">
                    <label class="form-label">Nome do Deck</label>
                    <input type="text" id="deckNome" class="form-control" placeholder="Ex: Vocabulario de Ingles">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descricao (opcional)</label>
                    <textarea id="deckDescricao" class="form-control" rows="2" placeholder="Sobre o que e este deck?"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cor</label>
                    <div class="d-flex gap-2" id="coresDeck">
                        <button type="button" class="btn-cor active" data-cor="#4f46e5" style="background: linear-gradient(135deg, #4f46e5, #3b82f6);"></button>
                        <button type="button" class="btn-cor" data-cor="#9333ea" style="background: linear-gradient(135deg, #9333ea, #6366f1);"></button>
                        <button type="button" class="btn-cor" data-cor="#0ea5e9" style="background: linear-gradient(135deg, #0ea5e9, #06b6d4);"></button>
                        <button type="button" class="btn-cor" data-cor="#10b981" style="background: linear-gradient(135deg, #10b981, #059669);"></button>
                        <button type="button" class="btn-cor" data-cor="#f59e0b" style="background: linear-gradient(135deg, #f59e0b, #d97706);"></button>
                        <button type="button" class="btn-cor" data-cor="#ef4444" style="background: linear-gradient(135deg, #ef4444, #dc2626);"></button>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="deckPublico">
                    <label class="form-check-label" for="deckPublico">
                        <i class="bi bi-globe me-1"></i>Deck publico (outros podem ver e estudar)
                    </label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary w-100" onclick="salvarDeck()">
                    <i class="bi bi-check-lg me-2"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusao -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Excluir Deck
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="excluirDeckId">
                <p class="text-muted mb-0">Tem certeza? Todos os flashcards deste deck serao excluidos.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarExcluir()">Excluir</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<style>
    .btn-cor {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 3px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-cor:hover, .btn-cor.active {
        border-color: #1e40af;
        transform: scale(1.1);
    }
</style>

<script>
const API_URL = 'php/api_flashcards.php';
let corSelecionada = '#4f46e5';
let buscaTimer;

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

    carregarMeusDecks();

    // Quando trocar para aba Explorar
    $('a[href="#explorar"]').on('shown.bs.tab', function() {
        buscarPublicos();
    });

    // Selecionar cor
    $(document).on('click', '.btn-cor', function() {
        $('.btn-cor').removeClass('active');
        $(this).addClass('active');
        corSelecionada = $(this).data('cor');
    });

    // Enter para salvar
    $('#deckNome').keypress(function(e) {
        if(e.which === 13) salvarDeck();
    });
});

function carregarMeusDecks() {
    $('#loading-meus').show();
    $('#lista-meus-decks').html('');

    $.get(API_URL, { acao: 'listar_meus_decks', usuario_id: getUsuarioId() }, function(res) {
        const decks = typeof res === 'string' ? JSON.parse(res) : res;
        let html = '';

        if(decks.length === 0) {
            html = `
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-collection"></i>
                        <h5 class="mt-3 fw-bold text-dark">Nenhum deck ainda</h5>
                        <p class="text-muted">Crie seu primeiro deck de flashcards!</p>
                        <button class="btn btn-primary" onclick="abrirModalCriar()">
                            <i class="bi bi-plus-lg me-2"></i>Criar Deck
                        </button>
                    </div>
                </div>`;
        } else {
            decks.forEach(deck => {
                const gradientes = {
                    '#4f46e5': 'linear-gradient(135deg, #4f46e5, #3b82f6)',
                    '#9333ea': 'linear-gradient(135deg, #9333ea, #6366f1)',
                    '#0ea5e9': 'linear-gradient(135deg, #0ea5e9, #06b6d4)',
                    '#10b981': 'linear-gradient(135deg, #10b981, #059669)',
                    '#f59e0b': 'linear-gradient(135deg, #f59e0b, #d97706)',
                    '#ef4444': 'linear-gradient(135deg, #ef4444, #dc2626)'
                };
                const bg = gradientes[deck.cor] || gradientes['#4f46e5'];

                html += `
                <div class="col-md-6 col-lg-4">
                    <div class="deck-card" style="background: ${bg};" onclick="abrirDeck(${deck.id})">
                        <div class="deck-actions">
                            <button onclick="event.stopPropagation(); abrirModalEditar(${deck.id}, '${deck.nome.replace(/'/g, "\\'")}', '${(deck.descricao || '').replace(/'/g, "\\'")}', ${deck.publico}, '${deck.cor}')" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="delete" onclick="event.stopPropagation(); abrirModalExcluir(${deck.id})" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="flex-grow-1">
                            <div class="deck-title">${deck.nome}</div>
                            <div class="deck-desc">${deck.descricao || ''}</div>
                        </div>
                        <div class="deck-meta">
                            <span><i class="bi bi-card-text me-1"></i>${deck.total_cards} cards</span>
                            <span class="deck-badge">${deck.publico == 1 ? '<i class="bi bi-globe"></i> Publico' : '<i class="bi bi-lock"></i> Privado'}</span>
                        </div>
                    </div>
                </div>`;
            });
        }

        $('#lista-meus-decks').html(html);
        $('#loading-meus').hide();
    });
}

function buscarPublicos() {
    const busca = $('#busca-publica').val().trim();

    clearTimeout(buscaTimer);
    buscaTimer = setTimeout(function() {
        $('#loading-publicos').removeClass('d-none');
        $('#lista-publicos').html('');

        $.get(API_URL, { acao: 'listar_publicos', busca: busca, usuario_id: getUsuarioId() }, function(res) {
            const decks = typeof res === 'string' ? JSON.parse(res) : res;
            let html = '';

            if(decks.length === 0) {
                html = `
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="bi bi-search"></i>
                            <h5 class="mt-3 fw-bold text-dark">Nenhum deck encontrado</h5>
                            <p class="text-muted">Tente buscar por outro termo</p>
                        </div>
                    </div>`;
            } else {
                decks.forEach(deck => {
                    const inicial = deck.autor_nome ? deck.autor_nome.charAt(0).toUpperCase() : '?';
                    const avatarHtml = deck.autor_foto
                        ? `<img src="uploads/perfil/${deck.autor_foto}" alt="Foto">`
                        : inicial;

                    html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="public-deck-card" onclick="abrirDeck(${deck.id})" style="cursor: pointer;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="author-avatar me-2">${avatarHtml}</div>
                                <div>
                                    <div class="fw-semibold text-dark small">${deck.autor_nome || 'Anonimo'}</div>
                                </div>
                            </div>
                            <h6 class="fw-bold text-dark mb-2">${deck.nome}</h6>
                            <p class="text-muted small mb-3">${deck.descricao || 'Sem descricao'}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="bi bi-card-text me-1"></i>${deck.total_cards} cards</span>
                                <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); abrirDeck(${deck.id})">
                                    <i class="bi bi-play-fill"></i> Estudar
                                </button>
                            </div>
                        </div>
                    </div>`;
                });
            }

            $('#lista-publicos').html(html);
            $('#loading-publicos').addClass('d-none');
        });
    }, 300);
}

function abrirModalCriar() {
    $('#modalDeckTitulo').text('Novo Deck');
    $('#deckId').val('');
    $('#deckNome').val('');
    $('#deckDescricao').val('');
    $('#deckPublico').prop('checked', false);
    $('.btn-cor').removeClass('active').first().addClass('active');
    corSelecionada = '#4f46e5';

    const modal = new bootstrap.Modal(document.getElementById('modalDeck'));
    modal.show();
    setTimeout(() => $('#deckNome').focus(), 300);
}

function abrirModalEditar(id, nome, descricao, publico, cor) {
    $('#modalDeckTitulo').text('Editar Deck');
    $('#deckId').val(id);
    $('#deckNome').val(nome);
    $('#deckDescricao').val(descricao);
    $('#deckPublico').prop('checked', publico == 1);

    $('.btn-cor').removeClass('active');
    $(`.btn-cor[data-cor="${cor}"]`).addClass('active');
    corSelecionada = cor || '#4f46e5';

    const modal = new bootstrap.Modal(document.getElementById('modalDeck'));
    modal.show();
}

function salvarDeck() {
    const id = $('#deckId').val();
    const nome = $('#deckNome').val().trim();
    const descricao = $('#deckDescricao').val().trim();
    const publico = $('#deckPublico').is(':checked') ? 1 : 0;

    if(!nome) {
        $('#deckNome').addClass('is-invalid');
        return;
    }

    const acao = id ? 'atualizar_deck' : 'criar_deck';
    const dados = {
        acao: acao,
        usuario_id: getUsuarioId(),
        nome: nome,
        descricao: descricao,
        publico: publico,
        cor: corSelecionada
    };

    if(id) dados.deck_id = id;

    $.post(API_URL, dados, function(res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalDeck'));
        if(modal) modal.hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');

        if(data.status === 'sucesso') {
            carregarMeusDecks();
            // Se criou novo deck, abrir para adicionar cards
            if(!id && data.id) {
                setTimeout(() => abrirDeck(data.id), 300);
            }
        }
    });
}

function abrirModalExcluir(id) {
    $('#excluirDeckId').val(id);
    const modal = new bootstrap.Modal(document.getElementById('modalExcluir'));
    modal.show();
}

function confirmarExcluir() {
    const id = $('#excluirDeckId').val();

    $.post(API_URL, { acao: 'excluir_deck', deck_id: id, usuario_id: getUsuarioId() }, function(res) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluir'));
        if(modal) modal.hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');

        carregarMeusDecks();
    });
}

function abrirDeck(id) {
    window.location.href = 'estudar_deck.php?id=' + id;
}
</script>

</body>
</html>
