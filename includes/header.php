<?php
// Avvia il tracciamento della sessione (deve essere la prima cosa in assoluto nella pagina)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gattile - Portale Adozioni e Volontariato</title>
    <!-- Includeremo qui il CSS comune più avanti -->
    <link rel="stylesheet" href="css/stile.css">
</head>
<body>

<header style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: #f4f4f4; border-bottom: 1px solid #ddd;">
    <div class="logo">
        <h1>🐾 Il Gattile</h1>
    </div>
    
    <nav>
        <a href="home.php" style="margin-right: 15px;">Home</a>
        <a href="gatti.php" style="margin-right: 15px;">Adozioni</a>
        <a href="volontariato.php" style="margin-right: 15px;">Volontariato</a>
        
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <!-- Link visibile solo agli amministratori -->
            <a href="inserisci_gatto.php" style="margin-right: 15px; color: red; font-weight: bold;">Gestione Gatti</a>
        <?php endif; ?>
    </nav>

    <div class="user-status">
        <?php if (isset($_SESSION['username'])): ?>
            <span>Connesso come: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="logout.php" style="margin-left: 10px; color: #cc0000;">Logout</a>
        <?php else: ?>
            <span>Stato: <strong>non loggato</strong></span>
            <a href="login.php" style="margin-left: 10px;">Accedi</a>
            <a href="registrazione.php" style="margin-left: 10px;">Registrati</a>
        <?php endif; ?>
    </div>
</header>

<main style="padding: 20px;">