<!-- header.php - Intestazione HTML e barra di navigazione -->

<?php
// Avvia la sessione (deve essere la prima cosa, header HTTP inviati prima di HTML)
// PHP_SESSION_DISABLED | PHP_SESSION_NONE | PHP_SESSION_ACTIVE
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gattile - Portale Adozioni e Volontariato</title>
    <link rel="stylesheet" href="css/stile.css">
    <link rel="icon" type="image/svg+xml" href="asset/favicon.svg">
</head>
<body>

<header class="barra-navigazione">
    <div class="logo">
        <h1>🐾 Il Gattile</h1>
    </div>
    
    <nav class="menu-principale">
        <a href="home.php">Home</a>
        <a href="gatti.php">Adozioni</a>
        <a href="volontariato.php">Volontariato</a>
        
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <!-- Gestione gatti solo per admin -->
            <a href="inserisci_gatto.php" class="link-gestione">Gestione Gatti (Admin)</a>
        <?php endif; ?>
    </nav>

    <div class="user-status">
        <?php if (isset($_SESSION['username'])): ?>
            <!-- htmlspecialchars() converte caratteri speciali HTML in entità (& = &amp, " = &quot, < = &lt, > = &gt) -->
            <!-- Previene attacchi XSS (cross-site scripting) -->
            <span>Connesso come: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="logout.php" class="link-logout">Logout</a>
        <?php else: ?>
            <span>Stato: <strong>non loggato</strong></span>
            <a href="login.php">Accedi</a>
            <a href="registrazione.php">Registrati</a>
        <?php endif; ?>
    </div>
</header>

<main>