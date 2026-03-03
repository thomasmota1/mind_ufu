<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidades - Mind UFU</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        /* Estilo local para os cards */
        .card-topo { 
            height: 100px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 40px; 
            border-radius: 10px 10px 0 0; 
            color: white; 
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    
    <div id="sidebar-container"></div>

    <main class="main-content">
        <div class="container-fluid p-0">
            
            <div class="d-flex justify-content-between align-items-center mb-5 pt-3">
                <h2 class="fw-bold text-dark">Minhas Comunidades</h2>
                <div>
                    <button class="btn btn-outline-secondary shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#modalEntrar">
                        <i class="bi bi-unlock"></i> Entrar
                    </button>
                    <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCriar">
                        <i class="bi bi-plus-lg"></i> Criar Nova
                    </button>
                </div>
            </div>

            <div id="grid-comunidades" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <div class="text-center w-100 mt-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Carregando comunidades...</p>
                </div>
            </div>

        </div>
    </main>
</div>

<div class="modal fade" id="modalCriar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Criar Nova Comunidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nome da Matéria/Grupo</label>
                    <input type="text" id="novoNome" class="form-control" placeholder="Ex: Cálculo Numérico">
                </div>
                <div class="mb-3">
                    <label class="form-label">Cor do Card</label>
                    <input type="color" id="novoCor" class="form-control form-control-color" value="#0d6efd">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" onclick="criarComunidade()">Gerar Código e Criar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEntrar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Entrar em Comunidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-qr-code fs-1 text-primary"></i>
                </div>
                <label class="form-label">Insira o Código da Turma</label>
                <input type="text" id="codigoEntrada" class="form-control text-center fs-4 fw-bold text-uppercase" placeholder="EX: A1B2C3">
                <div id="msgErro" class="text-danger mt-2 small text-center"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success w-100" onclick="entrarComunidade()">Entrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<script>
    
    const API_URL = 'php/api_comunidades.php';

    // Obter dados do usuário logado
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

        listarComunidades();
    });

    function listarComunidades() {
        $.get(API_URL, { acao: 'listar', usuario_id: getUsuarioId() })
            .done(function(data) {
                try {
                    const lista = (typeof data === 'string') ? JSON.parse(data) : data;
                    let html = '';
                    
                    if(lista.length === 0) {
                        $('#grid-comunidades').html('<div class="text-muted text-center w-100 p-5">Nenhuma comunidade encontrada. Crie a primeira!</div>');
                        return;
                    }

                    lista.forEach(c => {
                        html += `
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0 card-ufu">
                                <div class="card-topo" style="background-color: ${c.cor || '#0d6efd'};">
                                    <i class="${c.icone || 'bi-people-fill'}"></i>
                                </div>
                                <div class="card-body text-center p-4">
                                    <h5 class="fw-bold mb-1">${c.nome}</h5>
                                    <div class="badge bg-light text-dark mb-3 border">Cód: ${c.codigo}</div>
                                    <a href="ver_comunidade.html?id=${c.id}" class="btn btn-outline-primary w-100 fw-bold py-2">
                                        <i class="bi bi-eye"></i> Acessar
                                    </a>
                                </div>
                            </div>
                        </div>`;
                    });
                    $('#grid-comunidades').html(html);
                } catch(e) {
                    console.error("Erro JSON:", e, data);
                    $('#grid-comunidades').html('<div class="text-danger">Erro ao ler dados. Veja o console (F12).</div>');
                }
            })
            .fail(function(xhr, status, error) {
                console.error("Erro Ajax:", status, error);
                $('#grid-comunidades').html('<div class="text-danger p-4">Erro de conexão (404/500).<br>Certifique-se que <b>api_comunidades.php</b> está na mesma pasta que este arquivo.</div>');
            });
    }

    function criarComunidade() {
        const nome = $('#novoNome').val();
        const cor = $('#novoCor').val();
        
        if(!nome) return alert("Digite um nome!");

        $.post(API_URL, { acao: 'criar', nome: nome, cor: cor, usuario_id: getUsuarioId() }, function(res) {
            let dados = res;
            if(typeof res === 'string') { try { dados = JSON.parse(res); } catch(e){} }

            if(dados.status === 'sucesso') {
                alert(`Comunidade criada! Código: ${dados.codigo}`);
                bootstrap.Modal.getInstance(document.getElementById('modalCriar')).hide();
                listarComunidades();
            } else {
                alert("Erro: " + (dados.msg || "Desconhecido"));
            }
        });
    }

    function entrarComunidade() {
        const codigo = $('#codigoEntrada').val().toUpperCase();
        
        $.post(API_URL, { acao: 'entrar', codigo: codigo, usuario_id: getUsuarioId() }, function(res) {
            let dados = res;
            if(typeof res === 'string') { try { dados = JSON.parse(res); } catch(e){} }

            if(dados.status === 'sucesso') {
                bootstrap.Modal.getInstance(document.getElementById('modalEntrar')).hide();
                listarComunidades();
            } else {
                $('#msgErro').text(dados.msg || "Erro ao entrar");
            }
        });
    }
</script>

</body>
</html>