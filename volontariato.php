<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
include_once 'includes/header.php';

$is_loggato = isset($_SESSION['username']);
?>

<h2>🗓️ Organizzazione Turni Volontariato</h2>
<p>La struttura accoglie un massimo di 2 volontari in contemporanea per ciascuna fascia oraria per garantire il corretto coordinamento delle attività.</p>

<div id="errore-js" class="alert hidden-alert"></div>

<?php if ($is_loggato): ?>
    <form id="form-volontariato" class="pannello-form-stretto">
        <div class="campo-form">
            <label class="etichetta-lista">Seleziona un turno disponibile:</label>
            
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

        <button type="submit" id="btn-prenota-turno" class="bottone-volontariato">
            Prenota Turno
        </button>
    </form>
<?php else: ?>
    <div class="alert alert-warning pannello-avviso-stretto">
        ⚠️ <strong>Avviso:</strong> Per poter offrire il tuo tempo come volontario e selezionare le fasce orarie, devi prima <a href="login.php">effettuare l'accesso</a> o <a href="registrazione.php">registrarti</a>.
    </div>
<?php endif; ?>

<script src="js/volontariato.js"></script>

<?php include_once 'includes/footer.php'; ?>