<?php
require_once 'includes/db.php';
include_once 'includes/header.php';

$messaggio = "";
$tipo_messaggio = ""; // "successo" o "errore"

// Verifichiamo se il form è stato inviato via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanifichiamo gli input di testo semplici
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $indirizzo = trim($_POST['indirizzo']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Controllo di sicurezza lato server (fondamentale anche se c'è JS)
    if (empty($nome) || empty($cognome) || empty($indirizzo) || empty($username) || empty($password)) {
        $messaggio = "Tutti i campi sono obbligatori.";
        $tipo_messaggio = "errore";
    } else {
        // FASE 1: Controllo di esistenza dello username
        // Usiamo l'utente "lettore" che dispone dei privilegi di SELECT
        $conn_lettura = connetti_lettore();
        
        $stmt_check = $conn_lettura->prepare("SELECT id FROM utenti WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        // Memorizziamo l'esito del controllo prima di chiudere la connessione
        $username_esiste = $stmt_check->num_rows > 0;
        
        $stmt_check->close();
        $conn_lettura->close();

        if ($username_esiste) {
            $messaggio = "Lo username inserito è già utilizzato da un altro utente.";
            $tipo_messaggio = "errore";
        } else {
            // FASE 2: Inserimento del nuovo profilo utente
            // Apriamo una nuova connessione con l'utente "registratore" (privilegio esclusivo di INSERT)
            $conn_scrittura = connetti_registratore();

            // Cifratura della password prima del salvataggio nel database
            $password_criptata = password_hash($password, PASSWORD_DEFAULT);

            // Eseguiamo l'inserimento sicuro
            $stmt_insert = $conn_scrittura->prepare("INSERT INTO utenti (nome, cognome, indirizzo, username, password) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssss", $nome, $cognome, $indirizzo, $username, $password_criptata);

            if ($stmt_insert->execute()) {
                $messaggio = "Registrazione completata con successo! Ora puoi effettuare il login.";
                $tipo_messaggio = "successo";
            } else {
                $messaggio = "Si è verificato un errore durante la registrazione. Riprova più tardi.";
                $tipo_messaggio = "errore";
            }
            
            $stmt_insert->close();
            $conn_scrittura->close();
        }
    }
}
?>

<h2>Registrazione Nuovo Utente</h2>

<!-- Se ci sono messaggi di errore o successo dall'elaborazione PHP, li mostriamo a schermo -->
<?php if (!empty($messaggio)): ?>
    <div class="alert <?php echo $tipo_messaggio === 'successo' ? 'alert-success' : 'alert-danger'; ?>">
        <?php echo htmlspecialchars($messaggio); ?>
    </div>
<?php endif; ?>

<form id="form-registrazione" class="pannello-form-stretto" method="POST" action="registrazione.php">
    <div class="campo-form">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
    </div>
    
    <div class="campo-form">
        <label for="cognome">Cognome:</label>
        <input type="text" id="cognome" name="cognome" required>
    </div>

    <div class="campo-form">
        <label for="indirizzo">Indirizzo:</label>
        <input type="text" id="indirizzo" name="indirizzo" required>
    </div>

    <div class="campo-form">
        <label for="username">Username (deve iniziare con una lettera):</label>
        <input type="text" id="username" name="username" required>
    </div>

    <div class="campo-form">
        <label for="password">Password (8-16 caratteri, 1 maiuscola, 1 minuscola, 1 numero, 1 spec.):</label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="campo-form">
        <label for="conferma_password">Conferma Password:</label>
        <input type="password" id="conferma_password" name="conferma_password" required>
    </div>

    <button type="submit" id="btn-registrati" class="bottone-registrazione">Registrati</button>
</form>

<!-- Includiamo lo script JavaScript di validazione specifico per questa pagina -->
<script src="js/validazioni.js"></script>

<?php
include_once 'includes/footer.php';
?>