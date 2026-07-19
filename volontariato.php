<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
include_once 'includes/header.php';

// Protezione della pagina: se l'utente non è loggato, mostriamo solo l'avviso
$is_loggato = isset($_SESSION['username']);

$messaggio = "";
$tipo_messaggio = "";

// Elaborazione dell'inserimento del turno via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && $is_loggato) {
    $utente_id = $_SESSION['utente_id'];
    $fascia_oraria = $_POST['fascia_oraria'];

    if (empty($fascia_oraria)) {
        $messaggio = "Seleziona una fascia oraria valida.";
        $tipo_messaggio = "errore";
    } else {
        $conn = connetti_modificatore();

        // CONTROLLO DI INTEGRITÀ LATO SERVER: Verifichiamo se lo slot è effettivamente libero (max 2)
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM turni_volontariato WHERE fascia_oraria = ?");
        $stmt_check->bind_param("s", $fascia_oraria);
        $stmt_check->execute();
        $stmt_check->bind_result($totale_iscritti);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($totale_iscritti >= 2) {
            // Risposta strutturata in caso di violazione (richiesta esplicitamente dal testo)
            $messaggio = "Errore: La fascia oraria selezionata ha già raggiunto il limite massimo di 2 volontari.";
            $tipo_messaggio = "errore";
        } else {
            // Procediamo con l'inserimento del turno
            $stmt_insert = $conn->prepare("INSERT INTO turni_volontariato (utente_id, fascia_oraria) VALUES (?, ?)");
            $stmt_insert->bind_param("is", $utente_id, $fascia_oraria);

            if ($stmt_insert->execute()) {
                $messaggio = "Turno di volontariato prenotato con successo!";
                $tipo_messaggio = "successo";
            } else {
                // Gestione del vincolo UNIQUE (se l'utente prova a iscriversi due volte allo stesso turno)
                $messaggio = "Hai già prenotato un turno in questa esatta fascia oraria.";
                $tipo_messaggio = "errore";
            }
            $stmt_insert->close();
        }
        $conn->close();
    }
}
?>

<h2>🗓️ Organizzazione Turni Volontariato</h2>
<p>La struttura accoglie un maximum di 2 volontari in contemporanea per ciascuna fascia oraria per garantire il corretto coordinamento delle attività.</p>

<!-- Box dei messaggi di errore/successo del server -->
<?php if (!empty($messaggio)): ?>
    <div id="messaggio-server" class="alert <?php echo $tipo_messaggio === 'successo' ? 'alert-success' : 'alert-danger'; ?>">
        <?php echo htmlspecialchars($messaggio); ?>
    </div>
<?php endif; ?>

<!-- Box per gli errori intercettati via JavaScript asincrono -->
<div id="errore-js" class="alert alert-danger hidden-alert"></div>

<?php if ($is_loggato): ?>
    <form id="form-volontariato" class="pannello-form-stretto" method="POST" action="volontariato.php">
        <div class="campo-form">
            <label class="etichetta-lista">Seleziona un turno disponibile:</label>
            
            <!-- Generiamo una lista radio di turni fissi per il test (es: fasce di data presenti nel file SQL) -->
            <div class="lista-opzioni-verticale">
                <label class="opzione-turno">
                    <input type="radio" name="fascia_oraria" value="2026-06-05 09:00:00" required> 05 Giugno 2026 - Ore 09:00
                    <span class="stato-slot"></span>
                </label>
                <label class="opzione-turno">
                    <input type="radio" name="fascia_oraria" value="2026-06-05 11:00:00"> 05 Giugno 2026 - Ore 11:00
                    <span class="stato-slot"></span>
                </label>
                <label class="opzione-turno">
                    <input type="radio" name="fascia_oraria" value="2026-06-05 15:00:00"> 05 Giugno 2026 - Ore 15:00
                    <span class="stato-slot"></span>
                </label>
            </div>
        </div>

        <button type="submit" class="bottone-volontariato">
            Prenota Turno
        </button>
    </form>
<?php else: ?>
    <div class="alert alert-warning pannello-avviso-stretto">
        ⚠️ <strong>Avviso:</strong> Per poter offrire il tuo tempo come volontario e selezionare le fasce orarie, devi prima <a href="login.php">effettuare l'accesso</a> o <a href="registrazione.php">registrarti</a>.
    </div>
<?php endif; ?>

<!-- Script Vanilla JS specifico per la gestione dinamica dei turni -->
<script src="js/volontariato.js"></script>

<?php
include_once 'includes/footer.php';
?>