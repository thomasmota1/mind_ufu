<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $senha = $_POST["senha"] ?? '';
    $confirmar = $_POST["confirmar_senha"] ?? '';

    // Validações básicas
    if (empty($nome) || empty($email) || empty($senha)) {
        die("Preencha todos os campos.");
    }

    if ($senha !== $confirmar) {
        die("As senhas não coincidem.");
    }

    if (strlen($senha) < 6) {
        die("A senha deve ter pelo menos 6 caracteres.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("E-mail inválido.");
    }

    // Verificar se email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Este e-mail já está cadastrado. <a href='index.html'>Fazer login</a>");
    }

    // Criptografar senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir usuário
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro na preparação da query: " . $conn->error);
    }

    $stmt->bind_param("sss", $nome, $email, $senhaHash);

    if ($stmt->execute()) {
        // Cadastro OK - redireciona para login
        header("Location: ../index.html?cadastro=sucesso");
        exit;
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
