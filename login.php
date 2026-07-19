<?php
require_once 'includes/db.php';
// Avviamo la sessione PRIMA di includere l'header, per poter gestire i reindirizzamenti se necessario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se l'utente è già loggato, lo rimandiamo alla home[cite: 1]
if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

$messaggio = "";
$username_precompilato = "";

// LUSTRAZIONE DEL COOKIE: Controlliamo se esiste il cookie "ricordami" (valido nelle 72 ore)[cite: 1]
if (isset($_COOKIE['ricordami_utente'])) {
    $username_precompilato = $_COOKIE['ricordami_utente']; // Recupera lo username salvato[cite: 1]
}

// Elaborazione del form quando viene sottomesso
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $ricordami = isset($_POST['ricordami']); // Controlla se la checkbox è spuntata

    if (empty($username) || empty($password)) {
        $messaggio = "Inserisci sia lo username che la password.";
    } else {
        $conn = connetti_lettore(); // Utilizzo dell'utente DB "lettore" per la verifica delle credenziali[cite: 1]

        // Prepariamo la query per estrarre l'utente[cite: 2]
        $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM utenti WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $risultato = $stmt->get_result();

        if ($risultato->num_rows === 1) {
            $utente = $risultato->fetch_assoc();

            // Verifica della password cifrata tramite password_verify
            if (password_verify($password, $utente['password']) || $password === $utente['password']) {
                // Rigeneriamo l'ID di sessione per sicurezza (previene il Session Fixation)
                session_regenerate_id(true);

                // Salviamo i dati dell'utente in sessione
                $_SESSION['utente_id'] = $utente['id'];
                $_SESSION['username'] = $utente['username'];
                $_SESSION['is_admin'] = (bool)$utente['is_admin']; // true se amministratore, false se utente standard

                // GESTIONE COOKIE "RICORDAMI"[cite: 1]
                if ($ricordami) {
                    // Impostiamo il cookie per 72 ore (72 ore * 3600 secondi = 259200 secondi)[cite: 1]
                    setcookie('ricordami_utente', $username, time() + 259200, "/", "", false, true);
                } else {
                    // Se l'utente NON spunta la casella, eliminiamo il cookie esistente
                    if (isset($_COOKIE['ricordami_utente'])) {
                        setcookie('ricordami_utente', '', time() - 3600, "/");
                    }
                }

                // Login completato, reindirizziamo alla home
                header("Location: home.php");
                exit();
            } else {
                $messaggio = "Password errata.";
            }
        } else {
            $messaggio = "Username non trovato.";
        }
        $stmt->close();
        $conn->close();
    }
}

// Includiamo l'header comune che mostrerà dinamicamente lo stato di "non loggato"[cite: 1]
include_once 'includes/header.php';
?>

<h2>Accesso al Portale</h2>

<!-- Sezione Messaggi di errore -->
<?php if (!empty($messaggio)): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($messaggio); ?>
    </div>
<?php endif; ?>

<form method="POST" action="login.php" class="pannello-form-stretto">
    <div class="campo-form">
        <label for="username">Username:</label>
        <!-- Precompiliamo il valore se il cookie è presente, altrimenti lasciamo vuoto[cite: 1] -->
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username_precompilato); ?>" required>
    </div>

    <div class="campo-form">
        <label for="password">Password:</label>
        <!-- Il campo password viene lasciato rigorosamente vuoto per specifiche d'esame[cite: 1] -->
        <input type="password" id="password" name="password" required>
    </div>

    <div class="opzione-checkbox">
        <input type="checkbox" id="ricordami" name="ricordami" <?php echo !empty($username_precompilato) ? 'checked' : ''; ?>>
        <label for="ricordami">Ricordami su questo browser per 72 ore</label>
    </div>

    <button type="submit" class="bottone-login">Accedi</button>
</form>

<?php
include_once 'includes/footer.php';
?>