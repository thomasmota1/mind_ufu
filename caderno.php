<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="mindufu_logo_2.png" type="image/png">
    <title>Caderno - Mind UFU</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        .notebook-wrapper {
            display: flex;
            height: calc(100vh - 40px);
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .sidebar-tree {
            width: 300px;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            background: white;
        }

        .sidebar-content {
            padding: 15px;
        }

        .editor-area {
            flex-grow: 1;
            padding: 40px 60px;
            overflow-y: auto;
            background: #fff;
        }

        .tree-discipline {
            margin-bottom: 8px;
        }

        .tree-discipline-header {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        .tree-discipline-header:hover {
            background: #f0f9ff;
            border-color: #3b82f6;
        }

        .tree-discipline-header .icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 10px;
        }

        .tree-folder {
            margin-left: 20px;
            margin-top: 6px;
        }

        .tree-folder-header {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            color: #64748b;
            font-size: 14px;
            transition: all 0.2s;
        }

        .tree-folder-header:hover {
            background: #e0e7ff;
            color: #4338ca;
        }

        .tree-page {
            margin-left: 24px;
            margin-top: 4px;
        }

        .tree-page-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            color: #475569;
            font-size: 13px;
            transition: all 0.2s;
        }

        .tree-page-item:hover {
            background: #fef3c7;
        }

        .tree-page-item.active {
            background: #fef3c7;
            color: #92400e;
            font-weight: 600;
        }

        .btn-tree-action {
            width: 24px;
            height: 24px;
            padding: 0;
            border: none;
            background: transparent;
            color: #94a3b8;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.2s;
        }

        .tree-discipline-header:hover .btn-tree-action,
        .tree-folder-header:hover .btn-tree-action,
        .tree-page-item:hover .btn-tree-action {
            opacity: 1;
        }

        .btn-tree-action:hover {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-tree-action.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        #note-title {
            font-size: 36px;
            font-weight: 700;
            border: none;
            outline: none;
            width: 100%;
            margin-bottom: 8px;
            color: #1e293b;
            background: transparent;
        }

        #note-title::placeholder {
            color: #cbd5e1;
        }

        #note-body {
            border: none;
            outline: none;
            width: 100%;
            min-height: 60vh;
            font-size: 16px;
            line-height: 1.8;
            resize: none;
            color: #334155;
            background: transparent;
        }

        #note-body::placeholder {
            color: #cbd5e1;
        }

        .save-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #f0fdf4;
            color: #16a34a;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .save-status.saving {
            background: #fef3c7;
            color: #d97706;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .notebook-wrapper { flex-direction: column; }
            .sidebar-tree { width: 100%; max-height: 250px; }
            .editor-area { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content p-3">
        <div class="notebook-wrapper">

            <aside class="sidebar-tree">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">
                            <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Meus Cadernos
                        </h6>
                        <button class="btn btn-primary btn-sm rounded-pill" style="white-space: nowrap;" onclick="abrirModalCriar('disciplina')">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="sidebar-content" id="hierarquia-lista">
                    <div class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                </div>
            </aside>

            <div class="editor-area">
                <div id="editor-empty" class="empty-state">
                    <i class="bi bi-journal-text"></i>
                    <h5 class="fw-bold text-dark">Nenhuma pagina selecionada</h5>
                    <p>Selecione uma pagina no menu lateral ou crie uma nova.</p>
                </div>

                <div id="editor-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="save-status" id="save-status">
                            <i class="bi bi-cloud-check"></i> Salvo
                        </span>
                        <button class="btn btn-outline-danger btn-sm" onclick="excluirPaginaAtiva()">
                            <i class="bi bi-trash"></i> Excluir pagina
                        </button>
                    </div>
                    <input type="text" id="note-title" placeholder="Titulo da pagina" oninput="autoSave()">
                    <hr class="my-3">
                    <textarea id="note-body" placeholder="Comece a escrever suas anotacoes aqui..." oninput="autoSave()"></textarea>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Criar Item -->
<div class="modal fade" id="modalCriar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="modalCriarTitulo">Novo Item</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="criarTipo">
                <input type="hidden" id="criarPaiId">
                <div class="mb-3">
                    <label class="form-label small text-muted">Nome</label>
                    <input type="text" id="criarNome" class="form-control" placeholder="Digite o nome...">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary w-100" onclick="criarItem()">
                    <i class="bi bi-plus-lg me-2"></i>Criar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusao -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Exclusao
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="excluirTipo">
                <input type="hidden" id="excluirId">
                <p class="text-muted mb-0" id="excluirMsg">Tem certeza que deseja excluir?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarExcluir()">
                    <i class="bi bi-trash me-1"></i>Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<script>
    let paginaAtivaId = null;
    let saveTimer = null;

    function getUsuarioId() {
        const userData = JSON.parse(localStorage.getItem('usuario') || '{}');
        return userData.id || 0;
    }

    $(document).ready(() => {
        if(typeof loadSidebar === 'function') loadSidebar();

        // Verificar login
        if(!getUsuarioId()) {
            window.location.href = 'index.html';
            return;
        }

        carregarMenu();

        // Enter para criar
        $('#criarNome').keypress(function(e) {
            if(e.which === 13) criarItem();
        });
    });

    function carregarMenu() {
        $.get('php/buscar_hierarquia.php', { usuario_id: getUsuarioId() }, function(data) {
            try {
                const lista = typeof data === 'string' ? JSON.parse(data) : data;
                let html = '';

                if(lista.length === 0) {
                    html = `
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-journal-plus fs-1 d-block mb-2"></i>
                            <p class="small mb-2">Nenhum caderno ainda</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="abrirModalCriar('disciplina')">
                                Criar primeiro caderno
                            </button>
                        </div>`;
                } else {
                    lista.forEach(disc => {
                        html += `
                        <div class="tree-discipline">
                            <div class="tree-discipline-header">
                                <div class="icon"><i class="bi bi-book"></i></div>
                                <span class="flex-grow-1 fw-semibold">${disc.nome}</span>
                                <button class="btn-tree-action" onclick="event.stopPropagation(); abrirModalCriar('pasta', ${disc.id})" title="Nova pasta">
                                    <i class="bi bi-folder-plus"></i>
                                </button>
                                <button class="btn-tree-action delete" onclick="event.stopPropagation(); abrirModalExcluir('disciplina', ${disc.id}, '${disc.nome}')" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>`;

                        disc.pastas.forEach(pasta => {
                            html += `
                            <div class="tree-folder">
                                <div class="tree-folder-header">
                                    <i class="bi bi-folder-fill me-2"></i>
                                    <span class="flex-grow-1">${pasta.nome}</span>
                                    <button class="btn-tree-action" onclick="event.stopPropagation(); abrirModalCriar('pagina', ${pasta.id})" title="Nova pagina">
                                        <i class="bi bi-file-plus"></i>
                                    </button>
                                    <button class="btn-tree-action delete" onclick="event.stopPropagation(); abrirModalExcluir('pasta', ${pasta.id}, '${pasta.nome}')" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>`;

                            pasta.paginas.forEach(pag => {
                                const isActive = pag.id === paginaAtivaId ? 'active' : '';
                                html += `
                                <div class="tree-page">
                                    <div class="tree-page-item ${isActive}" onclick="abrirPagina(${pag.id})">
                                        <i class="bi bi-file-text me-2"></i>
                                        <span class="flex-grow-1 text-truncate">${pag.titulo || 'Sem titulo'}</span>
                                    </div>
                                </div>`;
                            });

                            html += `</div>`;
                        });

                        html += `</div>`;
                    });
                }

                $('#hierarquia-lista').html(html);
            } catch(e) {
                console.error("Erro ao carregar menu:", e);
                $('#hierarquia-lista').html('<div class="text-danger p-3">Erro ao carregar</div>');
            }
        });
    }

    function abrirPagina(id) {
        paginaAtivaId = id;
        $('#editor-empty').hide();
        $('#editor-content').fadeIn();

        // Marcar como ativo no menu
        $('.tree-page-item').removeClass('active');
        $(`.tree-page-item`).each(function() {
            if($(this).attr('onclick').includes(id)) {
                $(this).addClass('active');
            }
        });

        $.get('php/buscar_pagina.php', { id: id, usuario_id: getUsuarioId() }, function(data) {
            try {
                const p = typeof data === 'string' ? JSON.parse(data) : data;
                if(p.status === 'erro') {
                    console.error("Erro:", p.msg);
                    return;
                }
                $('#note-title').val(p.titulo || '');
                $('#note-body').val(p.conteudo || '');
                $('#save-status').html('<i class="bi bi-cloud-check"></i> Carregado').removeClass('saving');
            } catch(e) {
                console.error("Erro ao abrir pagina:", e);
            }
        });
    }

    function autoSave() {
        if (!paginaAtivaId) return;

        $('#save-status').html('<i class="bi bi-arrow-repeat"></i> Salvando...').addClass('saving');

        clearTimeout(saveTimer);
        saveTimer = setTimeout(function() {
            $.post('php/salvar_pagina.php', {
                id: paginaAtivaId,
                usuario_id: getUsuarioId(),
                titulo: $('#note-title').val(),
                conteudo: $('#note-body').val()
            }, function(res) {
                if(res.trim() === "Sucesso") {
                    $('#save-status').html('<i class="bi bi-cloud-check"></i> Salvo').removeClass('saving');
                    // Atualizar titulo no menu
                    carregarMenu();
                }
            });
        }, 800);
    }

    function abrirModalCriar(tipo, pai_id = 0) {
        const titulos = {
            'disciplina': 'Novo Caderno',
            'pasta': 'Nova Pasta',
            'pagina': 'Nova Pagina'
        };

        $('#modalCriarTitulo').text(titulos[tipo] || 'Novo Item');
        $('#criarTipo').val(tipo);
        $('#criarPaiId').val(pai_id);
        $('#criarNome').val('');

        const modal = new bootstrap.Modal(document.getElementById('modalCriar'));
        modal.show();

        setTimeout(() => $('#criarNome').focus(), 300);
    }

    function criarItem() {
        const tipo = $('#criarTipo').val();
        const nome = $('#criarNome').val().trim();
        const pai_id = $('#criarPaiId').val();

        if(!nome) {
            $('#criarNome').addClass('is-invalid');
            return;
        }

        $.post('php/criar_item.php', {
            tipo: tipo,
            nome: nome,
            pai_id: pai_id,
            usuario_id: getUsuarioId()
        }, function(res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;

            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalCriar'));
            if(modal) modal.hide();
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');

            if(data.status === 'sucesso') {
                carregarMenu();
                // Se criou uma pagina, abrir ela
                if(tipo === 'pagina' && data.id) {
                    setTimeout(() => abrirPagina(data.id), 300);
                }
            }
        });
    }

    function abrirModalExcluir(tipo, id, nome) {
        const msgs = {
            'disciplina': `Excluir o caderno "${nome}"? Todas as pastas e paginas dentro dele serao excluidas.`,
            'pasta': `Excluir a pasta "${nome}"? Todas as paginas dentro dela serao excluidas.`,
            'pagina': `Excluir a pagina "${nome}"?`
        };

        $('#excluirTipo').val(tipo);
        $('#excluirId').val(id);
        $('#excluirMsg').text(msgs[tipo] || 'Tem certeza?');

        const modal = new bootstrap.Modal(document.getElementById('modalExcluir'));
        modal.show();
    }

    function confirmarExcluir() {
        const tipo = $('#excluirTipo').val();
        const id = $('#excluirId').val();

        $.post('php/excluir_item.php', {
            tipo: tipo,
            id: id,
            usuario_id: getUsuarioId()
        }, function(res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;

            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluir'));
            if(modal) modal.hide();
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');

            if(data.status === 'sucesso') {
                // Se excluiu a pagina ativa, limpar editor
                if(tipo === 'pagina' && parseInt(id) === paginaAtivaId) {
                    paginaAtivaId = null;
                    $('#editor-content').hide();
                    $('#editor-empty').show();
                }
                carregarMenu();
            }
        });
    }

    function excluirPaginaAtiva() {
        if(!paginaAtivaId) return;
        const titulo = $('#note-title').val() || 'esta pagina';
        abrirModalExcluir('pagina', paginaAtivaId, titulo);
    }
</script>
</body>
</html>
