<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"] ?? '';
    $email = $_POST["email"] ?? '';
    $senha = $_POST["senha"] ?? '';
    $confirmar = $_POST["confirmar_senha"] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        die("Preencha todos os campos.");
    }

    if ($senha !== $confirmar) {
        die("As senhas não coincidem.");
    }

    // Criptografar senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro na preparação da query: " . $conn->error);
    }

    $stmt->bind_param("sss", $nome, $email, $senhaHash);

    if ($stmt->execute()) {
        header("Location: ../index.html");
        exit;
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>