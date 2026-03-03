<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include('db.php');

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// =========================
// LOGIN
// =========================
if ($acao == 'login') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(["status" => "erro", "msg" => "Preencha todos os campos"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "E-mail não cadastrado"]);
        exit;
    }

    $usuario = $result->fetch_assoc();

    if (!password_verify($senha, $usuario['senha'])) {
        echo json_encode(["status" => "erro", "msg" => "Senha incorreta"]);
        exit;
    }

    // Login OK - retorna dados (sem a senha)
    unset($usuario['senha']);
    echo json_encode([
        "status" => "sucesso",
        "msg" => "Login realizado com sucesso",
        "usuario" => $usuario
    ]);
}

// =========================
// VERIFICAR SE EMAIL EXISTE
// =========================
elseif ($acao == 'verificar_email') {

    $email = trim($_POST['email'] ?? $_GET['email'] ?? '');

    if (empty($email)) {
        echo json_encode(["status" => "erro", "msg" => "E-mail não informado"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "existe", "msg" => "E-mail já cadastrado"]);
    } else {
        echo json_encode(["status" => "disponivel", "msg" => "E-mail disponível"]);
    }
}

// =========================
// OBTER PERFIL DO USUÁRIO
// =========================
elseif ($acao == 'get_perfil') {

    $usuario_id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

    if ($usuario_id <= 0) {
        echo json_encode(["status" => "erro", "msg" => "ID inválido"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, nome, email, foto, bio, link_linkedin, link_instagram, link_youtube, link_twitter, link_facebook, link_github FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Usuário não encontrado"]);
        exit;
    }

    $usuario = $result->fetch_assoc();
    echo json_encode(["status" => "sucesso", "usuario" => $usuario]);
}

// =========================
// OBTER PERFIL PÚBLICO (para visualizar outros usuários)
// =========================
elseif ($acao == 'get_perfil_publico') {

    $usuario_id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

    if ($usuario_id <= 0) {
        echo json_encode(["status" => "erro", "msg" => "ID inválido"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, nome, email, foto, bio, link_linkedin, link_instagram, link_youtube, link_twitter, link_facebook, link_github, created_at FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "erro", "msg" => "Usuário não encontrado"]);
        exit;
    }

    $usuario = $result->fetch_assoc();
    echo json_encode(["status" => "sucesso", "usuario" => $usuario]);
}

// =========================
// ATUALIZAR PERFIL
// =========================
elseif ($acao == 'atualizar_perfil') {

    $usuario_id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $link_linkedin = trim($_POST['link_linkedin'] ?? '');
    $link_instagram = trim($_POST['link_instagram'] ?? '');
    $link_youtube = trim($_POST['link_youtube'] ?? '');
    $link_twitter = trim($_POST['link_twitter'] ?? '');
    $link_facebook = trim($_POST['link_facebook'] ?? '');
    $link_github = trim($_POST['link_github'] ?? '');
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';

    if ($usuario_id <= 0 || empty($nome) || empty($email)) {
        echo json_encode(["status" => "erro", "msg" => "Dados incompletos"]);
        exit;
    }

    // Verificar se email já pertence a outro usuário
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $usuario_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(["status" => "erro", "msg" => "Este e-mail já está em uso"]);
        exit;
    }

    // Se quer mudar a senha, verificar senha atual
    if (!empty($nova_senha)) {
        if (empty($senha_atual)) {
            echo json_encode(["status" => "erro", "msg" => "Informe a senha atual"]);
            exit;
        }

        $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!password_verify($senha_atual, $row['senha'])) {
            echo json_encode(["status" => "erro", "msg" => "Senha atual incorreta"]);
            exit;
        }

        // Atualizar com nova senha
        $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, bio = ?, link_linkedin = ?, link_instagram = ?, link_youtube = ?, link_twitter = ?, link_facebook = ?, link_github = ?, senha = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssi", $nome, $email, $bio, $link_linkedin, $link_instagram, $link_youtube, $link_twitter, $link_facebook, $link_github, $senhaHash, $usuario_id);
    } else {
        // Atualizar sem mudar senha
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, bio = ?, link_linkedin = ?, link_instagram = ?, link_youtube = ?, link_twitter = ?, link_facebook = ?, link_github = ? WHERE id = ?");
        $stmt->bind_param("sssssssssi", $nome, $email, $bio, $link_linkedin, $link_instagram, $link_youtube, $link_twitter, $link_facebook, $link_github, $usuario_id);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso", "msg" => "Perfil atualizado com sucesso"]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao atualizar: " . $conn->error]);
    }
}


// =========================
// UPLOAD DE FOTO
// =========================
elseif ($acao == 'upload_foto') {

    $usuario_id = (int)($_POST['id'] ?? 0);

    if ($usuario_id <= 0) {
        echo json_encode(["status" => "erro", "msg" => "ID inválido"]);
        exit;
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "erro", "msg" => "Nenhuma foto enviada"]);
        exit;
    }

    $file = $_FILES['foto'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(["status" => "erro", "msg" => "Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP."]);
        exit;
    }

    if ($file['size'] > 2 * 1024 * 1024) { // 2MB max
        echo json_encode(["status" => "erro", "msg" => "Arquivo muito grande. Máximo 2MB."]);
        exit;
    }

    // Criar diretório de uploads se não existir
    $uploadDir = '../uploads/perfil/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Gerar nome único
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $novoNome = 'user_' . $usuario_id . '_' . time() . '.' . $ext;
    $destino = $uploadDir . $novoNome;

    // Remover foto antiga
    $stmt = $conn->prepare("SELECT foto FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row && $row['foto'] && file_exists($uploadDir . $row['foto'])) {
        unlink($uploadDir . $row['foto']);
    }

    if (move_uploaded_file($file['tmp_name'], $destino)) {
        $stmt = $conn->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
        $stmt->bind_param("si", $novoNome, $usuario_id);
        $stmt->execute();

        echo json_encode(["status" => "sucesso", "msg" => "Foto atualizada!", "foto" => $novoNome]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao salvar arquivo"]);
    }
}

// =========================
// AÇÃO INVÁLIDA
// =========================
else {
    echo json_encode(["status" => "erro", "msg" => "Ação inválida: " . $acao]);
}

$conn->close();
?>
