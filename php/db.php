<?php
$host = 'localhost';
$user = 'root';
$pass = 'mysql'; 
$db   = 'mind_ufu';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // Isso força o erro aparecer na tela em vez de ficar branco
    die(json_encode(["status" => "erro", "msg" => "Conexão falhou: " . $conn->connect_error]));
}
?>