<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Caderno - Mind UFU</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        .notebook-wrapper { display: flex; height: calc(100vh - 40px); background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        .notion-tree { 
            width: 280px; background: #f9f9f9; border-right: 1px solid #eee; 
            overflow-y: auto; padding: 15px; flex-shrink: 0; 
        }
        
        .editor-area { flex-grow: 1; padding: 40px; overflow-y: auto; }

        .tree-item { padding: 6px 10px; cursor: pointer; border-radius: 4px; font-size: 14px; color: #444; margin-bottom: 2px; }
        .tree-item:hover { background: #e9e9e9; }
        
        #note-title { font-size: 32px; font-weight: 700; border: none; outline: none; width: 100%; margin-bottom: 10px; color: #2c2c2c; }
        #note-body { border: none; outline: none; width: 100%; min-height: 60vh; font-size: 16px; line-height: 1.6; resize: none; color: #37352f; }
        
        @media (max-width: 768px) {
            .notebook-wrapper { flex-direction: column; }
            .notion-tree { width: 100%; height: 200px; border-bottom: 1px solid #eee; }
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content">
        <div class="notebook-wrapper">
            
            <aside class="notion-tree">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-muted" style="font-size: 12px; letter-spacing: 1px;">SEUS CADERNOS</span>
                    <button class="btn btn-sm btn-light border" onclick="criarItem('disciplina')"><i class="bi bi-plus"></i></button>
                </div>
                <div id="hierarquia-lista">
                    </div>
            </aside>

            <div class="editor-area">
                <input type="text" id="note-title" placeholder="Sem título" oninput="autoSave()">
                <div class="d-flex justify-content-between text-muted small border-bottom mb-4 pb-2">
                    <span id="save-status"><i class="bi bi-cloud-check"></i> Sincronizado</span>
                    <span>Última edição: Hoje 14:00</span>
                </div>
                <textarea id="note-body" placeholder="Comece a escrever suas anotações..." oninput="autoSave()"></textarea>
            </div>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<script>
    let paginaAtivaId = null;

    $(document).ready(() => {
        if(typeof loadSidebar === 'function') loadSidebar();
        carregarMenu();
    });

    // MANTIVE SUAS FUNÇÕES ORIGINAIS
    function carregarMenu() {
        $.get('php/buscar_hierarquia.php', function(data) {
            try {
                const lista = JSON.parse(data);
                let html = '';
                lista.forEach(disc => {
                    html += `<div class="mt-2">
                        <div class="tree-item fw-bold d-flex justify-content-between align-items-center">
                            <span>📚 ${disc.nome}</span>
                            <button class="btn btn-sm py-0 px-1 text-muted" onclick="criarItem('pasta', ${disc.id})">+</button>
                        </div>`;
                    disc.pastas.forEach(pasta => {
                        html += `<div class="ps-3">
                            <div class="tree-item text-secondary d-flex justify-content-between align-items-center">
                                <span>📁 ${pasta.nome}</span>
                                <button class="btn btn-sm py-0 px-1 text-muted" onclick="criarItem('pagina', ${pasta.id})">+</button>
                            </div>`;
                        pasta.paginas.forEach(pag => {
                            html += `<div class="ps-3 tree-item" onclick="abrirPagina(${pag.id})">📄 ${pag.titulo}</div>`;
                        });
                        html += `</div>`;
                    });
                    html += `</div>`;
                });
                $('#hierarquia-lista').html(html);
            } catch(e) { console.log("Erro JSON menu"); }
        });
    }

    function abrirPagina(id) {
        paginaAtivaId = id;
        $.get('php/buscar_pagina.php', { id: id }, function(data) {
            try {
                const p = JSON.parse(data);
                $('#note-title').val(p.titulo);
                $('#note-body').val(p.conteudo);
                $('#save-status').html('<i class="bi bi-check-circle"></i> Carregado');
            } catch(e) {}
        });
    }

    function autoSave() {
        if (!paginaAtivaId) return;
        $('#save-status').text("Sincronizando...");
        $.post('php/salvar_pagina.php', {
            id: paginaAtivaId,
            titulo: $('#note-title').val(),
            conteudo: $('#note-body').val()
        }, function(res) {
            if(res.trim() === "Sucesso") $('#save-status').html('<i class="bi bi-cloud-check"></i> Salvo');
        });
    }

    function criarItem(tipo, pai_id = 0) {
        const nome = prompt(`Nome da ${tipo}:`);
        if(nome) $.post('php/criar_item.php', { tipo, nome, pai_id }, () => carregarMenu());
    }
</script>
</body>
</html>