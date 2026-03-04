<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="mindufu_logo_2.png" type="image/png">
    <title>Perfil - Mind UFU</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        .perfil-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }
        .perfil-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            overflow: hidden;
        }
        .perfil-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .perfil-avatar i {
            font-size: 50px;
            color: white;
        }
        .bio-text {
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 10px;
            color: #4b5563;
            font-style: italic;
        }
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
            margin: 0 5px;
        }
        .social-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .social-link.linkedin:hover { background: #0077b5; color: white; }
        .social-link.instagram:hover { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: white; }
        .social-link.youtube:hover { background: #ff0000; color: white; }
        .social-link.twitter:hover { background: #000; color: white; }
        .social-link.facebook:hover { background: #1877f2; color: white; }
        .social-link.github:hover { background: #333; color: white; }
        .member-since {
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <div id="sidebar-container"></div>

    <main class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark m-0">Perfil do Usuario</h2>
                    <p class="text-muted small mb-0">Informacoes publicas</p>
                </div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Voltar
                </a>
            </div>

            <div id="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Carregando perfil...</p>
            </div>

            <div id="perfil-content" class="d-none">
                <div class="perfil-card text-center">

                    <div class="perfil-avatar" id="avatar-container">
                        <i class="bi bi-person-fill"></i>
                    </div>

                    <h3 class="fw-bold mb-1" id="nome-usuario">Nome do Usuario</h3>
                    <p class="text-muted mb-3" id="email-usuario">email@exemplo.com</p>

                    <div id="bio-container" class="mb-4 d-none">
                        <p class="bio-text mb-0" id="bio-text"></p>
                    </div>

                    <div id="social-links" class="mb-4">
                    </div>

                    <p class="member-since" id="member-since"></p>

                </div>

                <!-- Decks Publicos -->
                <div id="decks-section" class="mt-4 d-none">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-collection text-primary me-2"></i>Decks Publicos
                    </h5>
                    <div id="lista-decks" class="row g-3"></div>
                </div>
            </div>

            <div id="error-state" class="text-center py-5 d-none">
                <i class="bi bi-person-x text-muted" style="font-size: 60px;"></i>
                <h5 class="mt-3 text-muted">Usuario nao encontrado</h5>
                <a href="javascript:history.back()" class="btn btn-outline-primary mt-3">Voltar</a>
            </div>

        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<script>
const API_URL = 'php/api_usuario.php';

$(document).ready(() => {
    // Limpar qualquer backdrop de modal que possa ter ficado
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('overflow', '');

    if (typeof loadSidebar === 'function') loadSidebar();

    // Verificar se está logado
    const userData = localStorage.getItem('usuario');
    if (!userData) {
        window.location.href = 'index.html';
        return;
    }

    // Obter ID do usuário a visualizar
    const params = new URLSearchParams(window.location.search);
    const userId = params.get('id');

    if (!userId) {
        mostrarErro();
        return;
    }

    // Verificar se é o próprio perfil
    const usuarioLogado = JSON.parse(userData);
    if (parseInt(userId) === parseInt(usuarioLogado.id)) {
        window.location.href = 'perfil.php';
        return;
    }

    carregarPerfil(userId);
});

function carregarPerfil(userId) {
    $.get(API_URL, { acao: 'get_perfil_publico', id: userId }, function(res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;

        if (data.status === 'sucesso') {
            const u = data.usuario;

            // Nome e email
            $('#nome-usuario').text(u.nome);
            $('#email-usuario').text(u.email);

            // Foto
            if (u.foto) {
                $('#avatar-container').html(`<img src="uploads/perfil/${u.foto}" alt="Foto">`);
            }

            // Bio
            if (u.bio) {
                $('#bio-text').text(u.bio);
                $('#bio-container').removeClass('d-none');
            }

            // Redes sociais
            let socialHtml = '';
            if (u.link_linkedin) {
                socialHtml += `<a href="${u.link_linkedin}" target="_blank" class="social-link linkedin" title="LinkedIn"><i class="bi bi-linkedin"></i></a>`;
            }
            if (u.link_instagram) {
                socialHtml += `<a href="${u.link_instagram}" target="_blank" class="social-link instagram" title="Instagram"><i class="bi bi-instagram"></i></a>`;
            }
            if (u.link_youtube) {
                socialHtml += `<a href="${u.link_youtube}" target="_blank" class="social-link youtube" title="YouTube"><i class="bi bi-youtube"></i></a>`;
            }
            if (u.link_twitter) {
                socialHtml += `<a href="${u.link_twitter}" target="_blank" class="social-link twitter" title="X (Twitter)"><i class="bi bi-twitter-x"></i></a>`;
            }
            if (u.link_facebook) {
                socialHtml += `<a href="${u.link_facebook}" target="_blank" class="social-link facebook" title="Facebook"><i class="bi bi-facebook"></i></a>`;
            }
            if (u.link_github) {
                socialHtml += `<a href="${u.link_github}" target="_blank" class="social-link github" title="GitHub"><i class="bi bi-github"></i></a>`;
            }

            if (socialHtml) {
                $('#social-links').html(socialHtml);
            } else {
                $('#social-links').html('<p class="text-muted small">Nenhuma rede social cadastrada</p>');
            }

            // Data de entrada
            if (u.created_at) {
                const date = new Date(u.created_at);
                const options = { year: 'numeric', month: 'long' };
                $('#member-since').html(`<i class="bi bi-calendar3 me-1"></i> Membro desde ${date.toLocaleDateString('pt-BR', options)}`);
            }

            $('#loading').addClass('d-none');
            $('#perfil-content').removeClass('d-none');

            // Carregar decks publicos
            carregarDecksPublicos(userId);
        } else {
            mostrarErro();
        }
    }).fail(function() {
        mostrarErro();
    });
}

function carregarDecksPublicos(userId) {
    $.get('php/api_flashcards.php', { acao: 'listar_decks_usuario', usuario_id: userId }, function(res) {
        const decks = typeof res === 'string' ? JSON.parse(res) : res;

        if (decks.length > 0) {
            let html = '';
            decks.forEach(deck => {
                html += `
                <div class="col-md-6">
                    <a href="estudar_deck.php?id=${deck.id}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-body">
                                <h6 class="fw-bold text-dark mb-1">${deck.nome}</h6>
                                <p class="text-muted small mb-2">${deck.descricao || 'Sem descricao'}</p>
                                <span class="badge bg-primary">${deck.total_cards} cards</span>
                            </div>
                        </div>
                    </a>
                </div>`;
            });
            $('#lista-decks').html(html);
            $('#decks-section').removeClass('d-none');
        }
    });
}

function mostrarErro() {
    $('#loading').addClass('d-none');
    $('#error-state').removeClass('d-none');
}
</script>

</body>
</html>
