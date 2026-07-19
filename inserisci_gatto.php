<?php
require_once 'includes/db.php';

// Avviamo la sessione per controllare chi sta provando ad accedere
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CONTROLLO DI ACCESSO AMMINISTRATORE: Se non è loggato o non è admin, viene respinto alla home
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: home.php");
    exit();
}

$messaggio = "";
$tipo_messaggio = "";

// Elaborazione del form al submit via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recupero e sanificazione dei dati inviati dal form
    $nome = trim($_POST['nome']);
    $descrizione = trim($_POST['descrizione']);
    $peso = floatval($_POST['peso']);
    $colore_mantello = trim($_POST['colore_mantello']);
    $lunghezza_pelo = trim($_POST['lunghezza_pelo']);
    $razza = trim($_POST['razza']);
    $colore_occhi = trim($_POST['colore_occhi']);
    $eta = intval($_POST['eta']);
    $sesso = $_POST['sesso'];
    $data_arrivo = $_POST['data_arrivo'];

    // Controllo di integrità lato server
    if (empty($nome) || empty($descrizione) || $peso <= 0 || $eta < 0 || empty($data_arrivo)) {
        $messaggio = "Compila tutti i campi obbligatori con valori validi.";
        $tipo_messaggio = "errore";
    } else {
        // Connessione con l'utente database "modificatore" (ha i permessi di INSERT)
        $conn = connetti_modificatore();

        // Prepariamo la query di inserimento per la tabella 'gatti'
        $stmt = $conn->prepare("INSERT INTO gatti (nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssssiss", $nome, $descrizione, $peso, $colore_mantello, $lunghezza_pelo, $razza, $colore_occhi, $eta, $sesso, $data_arrivo);

        if ($stmt->execute()) {
            $messaggio = "Scheda del gatto creata con successo! È stato assegnato il placeholder standard.";
            $tipo_messaggio = "successo";
        } else {
            $messaggio = "Errore durante il salvataggio nel database: " . $conn->error;
            $tipo_messaggio = "errore";
        }

        $stmt->close();
        $conn->close();
    }
}

// Includiamo l'header comune (mostrerà il link di amministrazione e lo username)
include_once 'includes/header.php';
?>

<h2>🐾 Inserimento Nuovo Felino</h2>
<p>Compila la scheda per registrare un nuovo gatto all'interno della struttura ospitante.</p>

<!-- Sezione Messaggi di inserimento -->
<?php if (!empty($messaggio)): ?>
    <div class="alert <?php echo $tipo_messaggio === 'successo' ? 'alert-success' : 'alert-danger'; ?>">
        <?php echo htmlspecialchars($messaggio); ?>
    </div>
<?php endif; ?>

<form id="form-gatto" class="pannello-form-esteso" method="POST" action="inserisci_gatto.php">
    <div class="campo-form">
        <label for="nome">Nome del Gatto *</label>
        <input type="text" id="nome" name="nome" required>
    </div>

    <div class="campo-form">
        <label for="descrizione">Carattere e Storia *</label>
        <textarea id="descrizione" name="descrizione" rows="4" required></textarea>
    </div>

    <div class="riga-form">
        <div class="colonna-form">
            <label for="peso">Peso (in kg) *</label>
            <input type="number" id="peso" name="peso" step="0.01" required>
        </div>
        <div class="colonna-form">
            <label for="eta">Età (in mesi) *</label>
            <input type="number" id="eta" name="eta" min="0" required>
        </div>
    </div>

    <div class="riga-form">
        <div class="colonna-form">
            <label for="colore_mantello">Colore del Mantello *</label>
            <input type="text" id="colore_mantello" name="colore_mantello" required>
        </div>
        <div class="colonna-form">
            <label for="lunghezza_pelo">Lunghezza del Pelo *</label>
            <input type="text" id="lunghezza_pelo" name="lunghezza_pelo" required>
        </div>
    </div>

    <div class="riga-form">
        <div class="colonna-form">
            <label for="razza">Razza *</label>
            <input type="text" id="razza" name="razza" required>
        </div>
        <div class="colonna-form">
            <label for="colore_occhi">Colore degli Occhi *</label>
            <input type="text" id="colore_occhi" name="colore_occhi" required>
        </div>
    </div>

    <div class="riga-form">
        <div class="colonna-form">
            <label for="sesso">Sesso *</label>
            <select id="sesso" name="sesso" required>
                <option value="M">Maschio (M)</option>
                <option value="F">Femmina (F)</option>
            </select>
        </div>
        <div class="colonna-form">
            <label for="data_arrivo">Data di Arrivo *</label>
            <input type="date" id="data_arrivo" name="data_arrivo" required>
        </div>
    </div>

    <button type="submit" id="btn-inserisci-gatto" class="bottone-admin">Crea Scheda Felino</button>
</form>

<!-- Inclusione dello script Vanilla JS dedicato alla validazione della scheda -->
<script src="js/validazione_gatto.js"></script>

<?php
include_once 'includes/footer.php';
?>