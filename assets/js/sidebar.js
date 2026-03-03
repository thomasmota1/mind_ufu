function loadSidebar() {
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';

    // Obter dados do usuário logado
    const userData = localStorage.getItem('usuario');
    let nomeUsuario = 'Estudante';
    let usuarioId = 0;

    if (userData) {
        try {
            const usuario = JSON.parse(userData);
            nomeUsuario = usuario.nome || 'Estudante';
            usuarioId = usuario.id || 0;
        } catch (e) {
            console.error('Erro ao parsear dados do usuário');
        }
    }

    // Pegar iniciais do nome para o avatar
    const iniciais = nomeUsuario.split(' ')
        .map(p => p[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();

    const sidebarHTML = `
        <div class="sidebar">

            <div class="sidebar-header">
                <div class="brand-container">
                    <i class="bi bi-mortarboard-fill brand-icon"></i>
                    <span class="brand-text">Mind UFU</span>
                </div>
                <button id="toggle-btn" class="btn btn-sm btn-light text-secondary border-0 rounded-circle" style="width: 32px; height: 32px;">
                    <i class="bi ${isCollapsed ? 'bi-chevron-right' : 'bi-chevron-left'}"></i>
                </button>
            </div>

            <nav class="nav flex-column nav-ufu">
                <a href="dashboard.php" class="nav-link" id="link-dashboard">
                    <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                </a>
                <a href="comunidades.php" class="nav-link" id="link-comunidades">
                    <i class="bi bi-people-fill"></i> <span>Comunidades</span>
                </a>
                <a href="quiz.php" class="nav-link" id="link-quiz">
                    <i class="bi bi-ui-checks"></i> <span>Quiz</span>
                </a>
                <a href="flashcards.php" class="nav-link" id="link-flashcards">
                    <i class="bi bi-card-heading"></i> <span>Flashcards</span>
                </a>
                <a href="caderno.php" class="nav-link" id="link-caderno">
                    <i class="bi bi-journal-bookmark-fill"></i> <span>Caderno</span>
                </a>
                <a href="calendario.html" class="nav-link" id="link-calendario">
                    <i class="bi bi-calendar-event"></i> <span>Calendário</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="perfil.php" class="d-flex align-items-center text-decoration-none text-dark sidebar-profile-link">
                    <div id="sidebar-avatar" class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center shadow-sm sidebar-avatar" style="width: 40px; height: 40px; flex-shrink:0; font-weight: 600; font-size: 14px; overflow: hidden;">
                        ${iniciais || '<i class="bi bi-person-fill"></i>'}
                    </div>
                    <div class="ms-2 user-text overflow-hidden">
                        <div class="fw-bold small text-dark">${nomeUsuario}</div>
                        <div class="text-muted" style="font-size: 11px;">Ver perfil</div>
                    </div>
                </a>
            </div>
        </div>`;

    $('#sidebar-container').html(sidebarHTML);

    // Buscar foto do usuário via API
    if (usuarioId > 0) {
        $.ajax({
            url: 'php/api_usuario.php',
            data: { acao: 'get_perfil', id: usuarioId },
            dataType: 'json',
            success: function(data) {
                if (data.status === 'sucesso' && data.usuario && data.usuario.foto) {
                    const fotoUrl = 'uploads/perfil/' + data.usuario.foto;
                    $('#sidebar-avatar').html('<img src="' + fotoUrl + '" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">');
                }
            },
            error: function(xhr, status, error) {
                console.log('Sidebar: erro ao carregar foto', status, error);
            }
        });
    }

    if (isCollapsed) $('#sidebar-container').addClass('collapsed');

    $('#toggle-btn').click(function() {
        $('#sidebar-container').toggleClass('collapsed');
        const collapsed = $('#sidebar-container').hasClass('collapsed');
        localStorage.setItem('sidebar-collapsed', collapsed);
        $(this).find('i').attr('class', collapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left');
    });

    // Highlight do link ativo
    const path = window.location.pathname.split("/").pop() || "dashboard.php";
    const links = {
        "dashboard.php": "link-dashboard",
        "comunidades.php": "link-comunidades",
        "quiz.php": "link-quiz",
        "flashcards.php": "link-flashcards",
        "caderno.php": "link-caderno",
        "calendario.html": "link-calendario"
    };
    if (links[path]) $(`#${links[path]}`).addClass('active');

    // Highlight para perfil
    if (path === 'perfil.php') {
        $('.sidebar-profile-link').css({
            'background': '#f0f9ff',
            'borderRadius': '8px',
            'padding': '8px'
        });
    }
}

// Função para verificar se usuário está logado
function checkAuth() {
    const userData = localStorage.getItem('usuario');
    if (!userData) {
        window.location.href = 'index.html';
        return false;
    }
    return JSON.parse(userData);
}
