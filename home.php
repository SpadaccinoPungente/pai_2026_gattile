<?php
require_once 'includes/db.php'; // Carica le funzioni del DB
include_once 'includes/header.php'; // Include l'intestazione comune

// Recuperiamo gli ultimi 2 gatti arrivati usando il modulo lettore[cite: 1]
$conn = connetti_lettore();
$query = "SELECT nome, descrizione, data_arrivo FROM gatti ORDER BY data_arrivo DESC LIMIT 2";
$risultato = $conn->query($query);
?>

<h2>Benvenuti al Gattile!</h2>
<p>Ogni anno centinaia di felini cercano una famiglia o cure adeguate. Il nostro portale ti permette di adottare un gatto o di offrire il tuo tempo prezioso come volontario presso la nostra struttura.</p>

<hr class="separatore-sezione">

<h3>🐾 Nuovi Arrivi (Ultimi inseriti)</h3>

<div class="griglia-gatti">
    <?php if ($risultato && $risultato->num_rows > 0): ?>
        <?php while($gatto = $risultato->fetch_assoc()): ?>
            <div class="card-gatto">
                <!-- Placeholder standard richiesto in attesa delle foto reali[cite: 1] -->
                <div class="placeholder-gatto">
                    <span>🐱</span>
                </div>
                <h4><?php echo htmlspecialchars($gatto['nome']); ?></h4>
                <p><?php echo htmlspecialchars($gatto['descrizione']); ?></p>
                <small class="testo-secondario">Arrivato il: <?php echo date("d/m/Y", strtotime($gatto['data_arrivo'])); ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nessun gatto presente al momento in struttura.</p>
    <?php endif; ?>
</div>

<?php 
$conn->close();
include_once 'includes/footer.php'; // Include il piè di pagina comune
?>