<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Svuotiamo l'array delle sessioni
$_SESSION = array();

// Distruggiamo la sessione sul server
session_destroy();

// Rimandiamo l'utente alla home page
header("Location: home.php");
exit();
?>