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
                    <h2 class="fw-bold text-dark m-0">Olá, Estudante! </h2>
                    <p class="text-muted small">Aqui está o resumo do seu dia.</p>
                </div>
                <span class="badge bg-light text-dark border p-2">Semestre 2024/2</span>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                                <i class="bi bi-calendar-event fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted small">Próxima Prova</h6>
                                <h5 class="fw-bold mb-0">Cálculo I</h5>
                                <small class="text-danger">Em 2 dias</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                                <i class="bi bi-check-circle fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted small">Quizzes Feitos</h6>
                                <h5 class="fw-bold mb-0">12</h5>
                                <small class="text-success">+2 essa semana</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning me-3">
                                <i class="bi bi-card-heading fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted small">Flashcards</h6>
                                <h5 class="fw-bold mb-0">85</h5>
                                <small class="text-muted">Cartões criados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Acesso Rápido</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="caderno.html" class="text-decoration-none">
                        <div class="card border-0 shadow-sm p-4 hover-scale text-center">
                            <i class="bi bi-journal-plus fs-1 text-primary mb-2"></i>
                            <h6 class="fw-bold text-dark">Nova Anotação</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="comunidades.html" class="text-decoration-none">
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
<script src="assets/js/sidebar.js"></script>
<script>
    $(document).ready(() => {
        if(typeof loadSidebar === 'function') loadSidebar();
    });
</script>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-5px); }
</style>
</body>
</html>