<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Quizzes - Mind UFU</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        .card-quiz {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
        }
        .card-quiz:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .badge-comunidade {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content p-4">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-0">
                        <i class="bi bi-bookmark-star-fill text-warning me-2"></i>Meus Quizzes Salvos
                    </h2>
                    <p class="text-muted small mt-1">Lista de atividades que você salvou para fazer depois.</p>
                </div>
            </div>

            <div id="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Buscando seus quizzes...</p>
            </div>

            <div id="lista-salvos" class="row g-4">
                </div>

            <div id="empty-state" class="text-center py-5 d-none">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Vazio" style="width: 100px; opacity: 0.5;">
                <h5 class="mt-3 text-muted">Nenhum quiz salvo ainda.</h5>
                <p class="text-muted small">Vá até uma comunidade e clique em "Salvar" nas atividades.</p>
                <a href="comunidades.php" class="btn btn-outline-primary mt-2">Ir para Comunidades</a>
            </div>

        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<script>
    const API_URL = 'php/api_comunidades.php';

    // Obter ID do usuário logado
    function getUsuarioId() {
        const userData = JSON.parse(localStorage.getItem('usuario') || '{}');
        return userData.id || 0;
    }

    $(document).ready(() => {
        if(typeof loadSidebar === 'function') loadSidebar();

        // Verificar se está logado
        if(!getUsuarioId()) {
            window.location.href = 'index.html';
            return;
        }

        carregarSalvos();
    });

    function carregarSalvos() {
        $.get(API_URL, { acao: 'listar_meus_quizzes', usuario_id: getUsuarioId() })
            .done(function(res) {
                // Converte para JSON se necessário
                let data = (typeof res === 'string') ? JSON.parse(res) : res;
                let html = '';

                // Se não for array ou estiver vazia
                if(!Array.isArray(data) || data.length === 0) {
                    $('#loading').hide();
                    $('#empty-state').removeClass('d-none');
                    $('#lista-salvos').html('');
                    return;
                }

                data.forEach(q => {
                    const corComunidade = q.comunidade_cor || '#6c757d';
                    const nomeComunidade = q.comunidade_nome || 'Geral';

                    html += `
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 shadow-sm card-quiz position-relative">
                            
                            <span class="badge rounded-pill badge-comunidade shadow-sm" style="background-color: ${corComunidade}">
                                ${nomeComunidade}
                            </span>

                            <div class="card-body d-flex flex-column pt-5">
                                <h5 class="card-title fw-bold text-dark mb-2">${q.titulo}</h5>
                                
                                <p class="card-text small text-muted flex-grow-1">
                                    ${q.descricao ? q.descricao.substring(0, 80) + '...' : 'Sem descrição.'}
                                </p>
                                
                                <small class="text-muted mb-3 d-block">
                                    <i class="bi bi-person-circle me-1"></i> ${q.autor_nome || 'Professor'}
                                </small>

                                <div class="d-grid gap-2">
                                    <a href="realizar_quiz.html?id=${q.id}" class="btn btn-primary fw-bold">
                                        <i class="bi bi-play-fill"></i> Resolver Agora
                                    </a>
                                    
                                    <button onclick="removerSalvo(${q.id})" class="btn btn-outline-danger btn-sm border-0">
                                        <i class="bi bi-trash"></i> Remover da lista
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });

                $('#lista-salvos').html(html);
                $('#loading').hide();
                $('#empty-state').addClass('d-none');
            })
            .fail(function() {
                $('#loading').html('<div class="alert alert-danger">Erro ao carregar lista. Verifique o servidor.</div>');
            });
    }

    function removerSalvo(id) {
        if(confirm("Tem certeza que deseja remover este quiz dos seus salvos?")) {
            $.post(API_URL, { acao: 'remover_salvo', quiz_id: id, usuario_id: getUsuarioId() }, function(res) {
                carregarSalvos();
            });
        }
    }
</script>

</body>
</html>