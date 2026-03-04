<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="mindufu_logo_2.png" type="image/png">
    <title>Meu Perfil - Mind UFU</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style-global.css">

    <style>
        .perfil-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            max-width: 700px;
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
            margin: 0 auto 10px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            position: relative;
            overflow: hidden;
            cursor: pointer;
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
        .perfil-avatar:hover .avatar-overlay {
            opacity: 1;
        }
        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .avatar-overlay i {
            font-size: 24px;
            color: white;
        }
        .form-label {
            font-weight: 600;
            color: #374151;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .btn-salvar {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-salvar:hover {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f3f4f6;
        }
        .alert {
            border-radius: 10px;
        }
        .btn-logout {
            color: #ef4444;
            border-color: #ef4444;
        }
        .btn-logout:hover {
            background: #ef4444;
            color: white;
        }
        .social-input {
            position: relative;
        }
        .social-input .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #6b7280;
        }
        .social-input input {
            padding-left: 40px;
        }
        .social-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        @media (max-width: 576px) {
            .social-grid {
                grid-template-columns: 1fr;
            }
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
                    <h2 class="fw-bold text-dark m-0">Meu Perfil</h2>
                    <p class="text-muted small mb-0">Gerencie suas informacoes pessoais</p>
                </div>
            </div>

            <div class="perfil-card">

                <div class="perfil-avatar" onclick="document.getElementById('inputFoto').click()" title="Clique para alterar a foto">
                    <div id="avatar-content">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="avatar-overlay">
                        <i class="bi bi-camera"></i>
                    </div>
                </div>
                <input type="file" id="inputFoto" accept="image/*" style="display: none;">
                <p class="text-center text-muted small mb-3">Clique na foto para alterar</p>

                <h4 class="text-center fw-bold mb-1" id="display-nome">Carregando...</h4>
                <p class="text-center text-muted mb-4" id="display-email">...</p>

                <div id="alertBox"></div>

                <form id="formPerfil">

                    <div class="section-title">
                        <i class="bi bi-person me-2"></i>Informacoes Basicas
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" required>
                        <div class="form-text" id="emailFeedback"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" rows="3" placeholder="Conte um pouco sobre voce..."></textarea>
                    </div>

                    <div class="section-title">
                        <i class="bi bi-link-45deg me-2"></i>Redes Sociais
                    </div>

                    <div class="social-grid mb-4">
                        <div class="social-input">
                            <i class="bi bi-linkedin input-icon" style="color: #0077b5;"></i>
                            <input type="url" class="form-control" id="link_linkedin" placeholder="LinkedIn">
                        </div>
                        <div class="social-input">
                            <i class="bi bi-instagram input-icon" style="color: #e4405f;"></i>
                            <input type="url" class="form-control" id="link_instagram" placeholder="Instagram">
                        </div>
                        <div class="social-input">
                            <i class="bi bi-youtube input-icon" style="color: #ff0000;"></i>
                            <input type="url" class="form-control" id="link_youtube" placeholder="YouTube">
                        </div>
                        <div class="social-input">
                            <i class="bi bi-twitter-x input-icon"></i>
                            <input type="url" class="form-control" id="link_twitter" placeholder="X (Twitter)">
                        </div>
                        <div class="social-input">
                            <i class="bi bi-facebook input-icon" style="color: #1877f2;"></i>
                            <input type="url" class="form-control" id="link_facebook" placeholder="Facebook">
                        </div>
                        <div class="social-input">
                            <i class="bi bi-github input-icon"></i>
                            <input type="url" class="form-control" id="link_github" placeholder="GitHub">
                        </div>
                    </div>

                    <div class="section-title">
                        <i class="bi bi-lock me-2"></i>Alterar Senha (opcional)
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Senha Atual</label>
                        <input type="password" class="form-control" id="senha_atual" placeholder="Deixe em branco para manter">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" id="nova_senha" placeholder="Minimo 6 caracteres">
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-danger btn-logout" onclick="logout()">
                            <i class="bi bi-box-arrow-left me-2"></i>Sair
                        </button>
                        <button type="submit" class="btn btn-primary btn-salvar">
                            <i class="bi bi-check-lg me-2"></i>Salvar Alteracoes
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="#" class="text-muted small" data-bs-toggle="modal" data-bs-target="#modalExcluirConta">
                        Excluir minha conta
                    </a>
                </div>

            </div>

        </div>
    </main>
</div>

<!-- Modal Excluir Conta -->
<div class="modal fade" id="modalExcluirConta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Excluir conta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Tem certeza? Esta acao e irreversivel e todos os seus dados serao apagados.</p>
                <div class="mb-3">
                    <label class="form-label">Confirme sua senha</label>
                    <input type="password" class="form-control" id="senhaConfirmacao" placeholder="Sua senha">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="excluirConta()">Excluir</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js?v=2"></script>

<script>
const API_URL = 'php/api_usuario.php';
let usuarioId = null;
let emailOriginal = '';

$(document).ready(() => {
    if (typeof loadSidebar === 'function') loadSidebar();

    // Obter ID do usuario do localStorage
    const userData = localStorage.getItem('usuario');
    if (!userData) {
        window.location.href = 'index.html';
        return;
    }

    const usuario = JSON.parse(userData);
    usuarioId = usuario.id;

    carregarPerfil();

    // Upload de foto
    $('#inputFoto').change(function() {
        const file = this.files[0];
        if (file) {
            uploadFoto(file);
        }
    });
});

function carregarPerfil() {
    $.get(API_URL, { acao: 'get_perfil', id: usuarioId }, function(res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;

        if (data.status === 'sucesso') {
            const u = data.usuario;
            $('#nome').val(u.nome);
            $('#email').val(u.email);
            $('#bio').val(u.bio || '');
            $('#link_linkedin').val(u.link_linkedin || '');
            $('#link_instagram').val(u.link_instagram || '');
            $('#link_youtube').val(u.link_youtube || '');
            $('#link_twitter').val(u.link_twitter || '');
            $('#link_facebook').val(u.link_facebook || '');
            $('#link_github').val(u.link_github || '');
            $('#display-nome').text(u.nome);
            $('#display-email').text(u.email);
            emailOriginal = u.email;

            // Mostrar foto se existir
            if (u.foto) {
                $('#avatar-content').html(`<img src="uploads/perfil/${u.foto}" alt="Foto">`);
            }
        } else {
            showAlert('danger', data.msg);
        }
    });
}

function uploadFoto(file) {
    const formData = new FormData();
    formData.append('acao', 'upload_foto');
    formData.append('id', usuarioId);
    formData.append('foto', file);

    // Preview imediato
    const reader = new FileReader();
    reader.onload = function(e) {
        $('#avatar-content').html(`<img src="${e.target.result}" alt="Foto">`);
    };
    reader.readAsDataURL(file);

    $.ajax({
        url: API_URL,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            if (data.status === 'sucesso') {
                showAlert('success', 'Foto atualizada!');
                // Recarregar sidebar para atualizar a foto
                if (typeof loadSidebar === 'function') loadSidebar();
            } else {
                showAlert('danger', data.msg);
            }
        }
    });
}

// Verificar email em tempo real
let emailTimer;
$('#email').on('input', function() {
    const email = $(this).val().trim();
    clearTimeout(emailTimer);

    if (email === emailOriginal) {
        $('#emailFeedback').html('');
        return;
    }

    emailTimer = setTimeout(() => {
        $.get(API_URL, { acao: 'verificar_email', email: email }, function(res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            if (data.status === 'existe') {
                $('#emailFeedback').html('<span class="text-danger"><i class="bi bi-x-circle"></i> E-mail ja cadastrado</span>');
            } else {
                $('#emailFeedback').html('<span class="text-success"><i class="bi bi-check-circle"></i> E-mail disponivel</span>');
            }
        });
    }, 500);
});

$('#formPerfil').submit(function(e) {
    e.preventDefault();

    const novaSenha = $('#nova_senha').val();
    if (novaSenha && novaSenha.length < 6) {
        showAlert('warning', 'A nova senha deve ter pelo menos 6 caracteres');
        return;
    }

    $.post(API_URL, {
        acao: 'atualizar_perfil',
        id: usuarioId,
        nome: $('#nome').val(),
        email: $('#email').val(),
        bio: $('#bio').val(),
        link_linkedin: $('#link_linkedin').val(),
        link_instagram: $('#link_instagram').val(),
        link_youtube: $('#link_youtube').val(),
        link_twitter: $('#link_twitter').val(),
        link_facebook: $('#link_facebook').val(),
        link_github: $('#link_github').val(),
        senha_atual: $('#senha_atual').val(),
        nova_senha: novaSenha
    }, function(res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;

        if (data.status === 'sucesso') {
            showAlert('success', data.msg);

            // Atualizar localStorage
            const userData = JSON.parse(localStorage.getItem('usuario'));
            userData.nome = $('#nome').val();
            userData.email = $('#email').val();
            localStorage.setItem('usuario', JSON.stringify(userData));

            // Atualizar display
            $('#display-nome').text($('#nome').val());
            $('#display-email').text($('#email').val());
            emailOriginal = $('#email').val();

            // Limpar campos de senha
            $('#senha_atual').val('');
            $('#nova_senha').val('');
            $('#emailFeedback').html('');

            // Recarregar sidebar para atualizar nome
            if (typeof loadSidebar === 'function') loadSidebar();
        } else {
            showAlert('danger', data.msg);
        }
    });
});

function showAlert(type, msg) {
    $('#alertBox').html(`
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
}

function logout() {
    if (confirm('Deseja realmente sair?')) {
        localStorage.removeItem('usuario');
        window.location.href = 'index.html';
    }
}

function excluirConta() {
    const senha = $('#senhaConfirmacao').val();

    if (!senha) {
        alert('Digite sua senha para confirmar');
        return;
    }

    $.post(API_URL, {
        acao: 'excluir_conta',
        id: usuarioId,
        senha: senha
    }, function(res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;

        if (data.status === 'sucesso') {
            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluirConta'));
            if (modal) modal.hide();
            $('.modal-backdrop').remove();

            alert('Sua conta foi excluida. Ate mais!');
            localStorage.removeItem('usuario');
            window.location.href = 'index.html';
        } else {
            alert(data.msg || 'Erro ao excluir conta');
        }
    });
}
</script>

</body>
</html>
