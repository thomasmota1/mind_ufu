<?php
require_once "db.php";

$id = $_POST["id"];

$stmt = $conn->prepare("DELETE FROM decks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: ../flashcards.php");
exit;
?>