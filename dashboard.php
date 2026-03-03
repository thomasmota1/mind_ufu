<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mind UFU</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style-global.css">
</head>
<body>

<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark m-0" id="saudacao">Olá!</h2>
                    <p class="text-muted small">Aqui está o resumo do seu dia.</p>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <h6 class="text-muted small mb-3"><i class="bi bi-calendar-event me-2"></i>Próximos Eventos</h6>
                        <div id="lista-eventos">
                            <div class="text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Acesso Rápido</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="caderno.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm p-4 hover-scale text-center">
                            <i class="bi bi-journal-plus fs-1 text-primary mb-2"></i>
                            <h6 class="fw-bold text-dark">Nova Anotação</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="comunidades.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm p-4 hover-scale text-center">
                            <i class="bi bi-people fs-1 text-info mb-2"></i>
                            <h6 class="fw-bold text-dark">Ver Grupos</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="calendario.html" class="text-decoration-none">
                        <div class="card border-0 shadow-sm p-4 hover-scale text-center">
                            <i class="bi bi-calendar-plus fs-1 text-danger mb-2"></i>
                            <h6 class="fw-bold text-dark">Adicionar Evento</h6>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>
<script>
    // Obter dados do usuário logado
    function getUsuario() {
        return JSON.parse(localStorage.getItem('usuario') || '{}');
    }

    function getUsuarioId() {
        return getUsuario().id || 0;
    }

    $(document).ready(() => {
        if(typeof loadSidebar === 'function') loadSidebar();

        // Verificar se está logado
        if(!getUsuarioId()) {
            window.location.href = 'index.html';
            return;
        }

        // Saudação dinâmica com nome do usuário
        const userData = getUsuario();
        const nome = userData.nome || 'Estudante';
        const primeiroNome = nome.split(' ')[0];
        $('#saudacao').text(`Olá, ${primeiroNome}!`);

        // Carregar próximos eventos
        carregarEventos();
    });

    function carregarEventos() {
        const userId = getUsuarioId();

        $.get('php/api_eventos.php', { acao: 'listar', usuario_id: userId })
            .done(function(res) {
                let eventos = (typeof res === 'string') ? JSON.parse(res) : res;

                // Verificar se é um array válido
                if(!Array.isArray(eventos)) {
                    $('#lista-eventos').html('<p class="text-muted small text-center mb-0">Nenhum evento futuro.</p>');
                    return;
                }

                // Filtrar apenas eventos futuros
                const agora = new Date();
                eventos = eventos.filter(e => new Date(e.data_inicio) >= agora);

                // Ordenar por data e pegar os 3 primeiros
                eventos.sort((a, b) => new Date(a.data_inicio) - new Date(b.data_inicio));
                eventos = eventos.slice(0, 3);

                let html = '';
                if(eventos.length > 0) {
                    eventos.forEach(e => {
                        const data = new Date(e.data_inicio);
                        const dataFormatada = data.toLocaleDateString('pt-BR', {
                            day: '2-digit', month: 'short'
                        });
                        html += `
                        <div class="d-flex align-items-center mb-2 p-2 rounded" style="background: ${e.cor}15;">
                            <div class="rounded-circle p-2 me-3" style="background: ${e.cor}30;">
                                <i class="bi bi-calendar-check" style="color: ${e.cor};"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">${e.titulo}</div>
                                <small class="text-muted">${dataFormatada}</small>
                            </div>
                        </div>`;
                    });
                } else {
                    html = '<p class="text-muted small text-center mb-0">Nenhum evento futuro.</p>';
                }

                $('#lista-eventos').html(html);
            })
            .fail(function() {
                $('#lista-eventos').html('<p class="text-muted small text-center mb-0">Erro ao carregar eventos.</p>');
            });
    }
</script>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-5px); }
</style>
</body>
</html>