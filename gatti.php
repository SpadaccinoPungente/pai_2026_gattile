<?php
// Avviamo la sessione per capire lo stato dell'utente
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
include_once 'includes/header.php';

// Controlliamo se chi naviga è un utente registrato/autenticato
$is_loggato = isset($_SESSION['username']) ? 'true' : 'false';

// Gestione del salvataggio del form via POST (quando l'utente prenota la visita)
$messaggio_prenotazione = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['azione_prenota'])) {
    if ($is_loggato === 'false') {
        $messaggio_prenotazione = "Errore: Devi effettuare il login per prenotare una visita.";
    } else {
        $utente_id = $_SESSION['utente_id'];
        $data_ora = $_POST['data_ora'];
        // I gatti selezionati arriveranno come stringa separata da virgole da un input hidden popolato da Vanilla JS
        $gatti_selezionati = isset($_POST['gatti_selezionati_input']) ? $_POST['gatti_selezionati_input'] : '';

        if (empty($data_ora) || empty($gatti_selezionati)) {
            $messaggio_prenotazione = "Seleziona almeno un gatto e una data/ora valida.";
        } else {
            $conn = connetti_modificatore(); // Privilegio di scrittura per la prenotazione[cite: 2]
            
            // 1. Inseriamo la prenotazione principale[cite: 2]
            $stmt = $conn->prepare("INSERT INTO prenotazioni_visite (utente_id, data_ora) VALUES (?, ?)");
            $stmt->bind_param("is", $utente_id, $data_ora);
            
            if ($stmt->execute()) {
                $prenotazione_id = $conn->insert_id; // Recuperiamo l'ID appena generato
                
                // 2. Associazioniamo i gatti scelti nella tabella di raccordo visita_gatti[cite: 2]
                $id_gatti_array = explode(',', $gatti_selezionati);
                $stmt_raccordo = $conn->prepare("INSERT INTO visita_gatti (prenotazione_id, gatto_id) VALUES (?, ?)");
                
                foreach ($id_gatti_array as $gatto_id) {
                    $gatto_id_int = intval($gatto_id);
                    $stmt_raccordo->bind_param("ii", $prenotazione_id, $gatto_id_int);
                    $stmt_raccordo->execute();
                }
                
                $messaggio_prenotazione = "Prenotazione registrata con successo per i gatti selezionati!";
                $stmt_raccordo->close();
            } else {
                $messaggio_prenotazione = "Errore durante la prenotazione: " . $conn->error;
            }
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<h2>🐾 Portale Adozioni Felini</h2>

<!-- Sezione Messaggi di prenotazione -->
<?php if (!empty($messaggio_prenotazione)): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($messaggio_prenotazione); ?>
    </div>
<?php endif; ?>

<!-- ========================================== -->
<!-- 1. CONTENITORE DOVE SI MONTERÀ REACT      -->
<!-- ========================================== -->
<div id="react-adozioni-root"></div>

<hr class="separatore-sezione">

<!-- ========================================== -->
<!-- 2. FORM DI PRENOTAZIONE (VANILLA JAVASCRIPT)-->
<!-- ========================================== -->
<div id="sezione-prenotazione" class="pannello-form">
    <h3>📅 Prenota una Visita Conoscitiva</h3>
    
    <?php if ($is_loggato === 'true'): ?>
        <form id="form-prenotazione-visita" method="POST" action="gatti.php">
            <input type="hidden" name="azione_prenota" value="1">
            
            <!-- Questo input nascosto verrà riempito dallo script Vanilla JS leggendo il CustomEvent di React[cite: 1] -->
            <input type="hidden" id="gatti_selezionati_input" name="gatti_selezionati_input" value="">

            <div class="campo-form">
                <label>Gatti attualmente selezionati:</label>
                <div id="elenco-gatti-selezionati-visivo" class="testo-selezionati">
                    Nessun gatto selezionato dalla lista sopra.
                </div>
            </div>

            <div class="campo-form">
                <label class="etichetta-obbligatoria" for="data_ora">Seleziona Data e Ora *</label>
                <input type="datetime-local" id="data_ora" name="data_ora" required>
            </div>

            <button type="submit" id="btn-invia-prenotazione" class="bottone-disabilitato" disabled>
                Conferma Prenotazione Visita
            </button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">
            ⚠️ <strong>Avviso:</strong> Per selezionare i gatti e prenotare una visita in struttura, devi prima <a href="login.php">effettuare l'accesso</a> o <a href="registrazione.php">registrarti.</a>
        </div>
    <?php endif; ?>
</div>

<!-- ========================================== -->
<!-- 3. INCLUSIONE LIBRERIE VIA CDN            -->
<!-- ========================================== -->
<!-- React Core e React DOM (versioni di produzione) -->
<script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>

<!-- Babel per compilare la sintassi JSX in tempo reale direttamente nel browser -->
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<!-- Passiamo lo stato di sessione PHP a JavaScript in modo sicuro -->
<script>
    window.APP_CONFIG = {
        isLoggato: <?php echo $is_loggato; ?>
    };
</script>

<!-- ========================================== -->
<!-- 4. I NOSTRI SCRIPT DI LOGICA               -->
<!-- ========================================== -->
<!-- Carichiamo prima il componente React (scritto in JSX, interpretato da Babel)[cite: 1] -->
<script type="text/babel" src="js/react-app.js"></script>

<!-- Carichiamo lo script Vanilla JS che gestisce l'ascolto degli eventi DOM e il form di prenotazione[cite: 1] -->
<script src="js/prenotazioni_visita.js"></script>

<?php
include_once 'includes/footer.php';
?>