<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário - Mind UFU</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        /* CSS Específico do Calendário (Sem sidebar) */
        .calendar-container { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 10px; }
        .month-title { font-size: 24px; font-weight: bold; color: #1f2937; margin: 0; }
        
        .days-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px; }
        .weekday { text-align: center; font-weight: 600; color: #9ca3af; padding-bottom: 12px; font-size: 14px; }
        
        .day { 
            aspect-ratio: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; 
            position: relative; transition: all 0.2s; cursor: pointer; background: white;
        }
        .day:hover { border-color: var(--ufu-indigo); background: #f9fafb; }
        .day-number { font-weight: 600; color: #374151; }
        .day.other-month { background: #f9fafb; color: #d1d5db; }
        .day.today { background: #eff6ff; border-color: var(--ufu-indigo); }
        .day.today .day-number { color: var(--ufu-indigo); }

        .event-dot {
            width: 8px; height: 8px; border-radius: 50%; margin-top: 6px; display: inline-block; margin-right: 4px;
        }
        .dot-red { background: #ef4444; }
        .dot-green { background: #10b981; }
        .dot-purple { background: #a855f7; }

        .event-list { margin-top: 32px; background: white; padding: 20px; border-radius: 12px; }
        .event-row { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
        .event-date { width: 60px; font-weight: bold; color: #374151; }
        .event-desc { flex: 1; }
    </style>
</head>
<body>

<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content">
        <div class="container-fluid">
            <div class="calendar-container">
                <div class="calendar-header">
                    <h2 class="month-title">Dezembro 2024</h2>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-chevron-left"></i></button>
                        <button class="btn btn-outline-secondary btn-sm me-2">Hoje</button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>

                <div class="days-grid">
                    <div class="weekday">DOM</div><div class="weekday">SEG</div><div class="weekday">TER</div><div class="weekday">QUA</div><div class="weekday">QUI</div><div class="weekday">SEX</div><div class="weekday">SÁB</div>

                    <div class="day other-month"><span class="day-number">29</span></div>
                    <div class="day other-month"><span class="day-number">30</span></div>
                    <div class="day"><span class="day-number">1</span></div>
                    <div class="day"><span class="day-number">2</span></div>
                    <div class="day"><span class="day-number">3</span></div>
                    <div class="day"><span class="day-number">4</span></div>
                    <div class="day"><span class="day-number">5</span></div>
                    
                    <div class="day"><span class="day-number">6</span></div>
                    <div class="day"><span class="day-number">7</span></div>
                    <div class="day"><span class="day-number">8</span></div>
                    <div class="day"><span class="day-number">9</span>
                        <div><span class="event-dot dot-purple"></span></div>
                    </div>
                    <div class="day"><span class="day-number">10</span></div>
                    <div class="day"><span class="day-number">11</span></div>
                    <div class="day"><span class="day-number">12</span></div>

                    <div class="day today"><span class="day-number">13</span></div>
                    <div class="day"><span class="day-number">14</span></div>
                    <div class="day"><span class="day-number">15</span>
                        <div><span class="event-dot dot-red"></span></div>
                    </div>
                    <div class="day"><span class="day-number">16</span></div>
                    <div class="day"><span class="day-number">17</span></div>
                    <div class="day"><span class="day-number">18</span>
                        <div><span class="event-dot dot-green"></span></div>
                    </div>
                    <div class="day"><span class="day-number">19</span></div>
                </div>
            </div>

            <div class="event-list shadow-sm">
                <h3 class="fw-bold mb-3 fs-5">Próximos Eventos</h3>
                <div class="event-row">
                    <div class="event-date">15 Dez</div>
                    <div class="event-desc">Prova de Cálculo I - Sala 3B</div>
                    <span class="event-dot dot-red"></span>
                </div>
                <div class="event-row border-0">
                    <div class="event-date">18 Dez</div>
                    <div class="event-desc">Entrega Trabalho Web - Online</div>
                    <span class="event-dot dot-green"></span>
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
</body>
</html>