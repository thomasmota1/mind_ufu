function loadSidebar() {
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    
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
                <a href="calendario.php" class="nav-link" id="link-calendario">
                    <i class="bi bi-calendar-event"></i> <span>Calendário</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="#" class="d-flex align-items-center text-decoration-none text-dark">
                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center shadow-sm" style="width: 40px; height: 40px; flex-shrink:0;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="ms-2 user-text overflow-hidden">
                        <div class="fw-bold small text-dark">Estudante</div>
                        <div class="text-muted" style="font-size: 11px;">Configurações</div>
                    </div>
                </a>
            </div>
        </div>`;

    $('#sidebar-container').html(sidebarHTML);
    
    if (isCollapsed) $('#sidebar-container').addClass('collapsed');

    $('#toggle-btn').click(function() {
        $('#sidebar-container').toggleClass('collapsed');
        const collapsed = $('#sidebar-container').hasClass('collapsed');
        localStorage.setItem('sidebar-collapsed', collapsed);
        $(this).find('i').attr('class', collapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left');
    });

    const path = window.location.pathname.split("/").pop() || "dashboard.php";
    const links = {
    "dashboard.php": "link-dashboard",
    "comunidades.php": "link-comunidades",
    "quiz.php": "link-quiz",
    "flashcards.php": "link-flashcards",
    "caderno.php": "link-caderno",
    "calendario.php": "link-calendario"
    };
    if (links[path]) $(`#${links[path]}`).addClass('active');
}