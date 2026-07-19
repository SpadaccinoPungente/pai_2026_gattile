<?php
// api/get_turni.php
header("Content-Type: application/json; charset=UTF-8");
require_once '../includes/db.php';

$conn = connetti_lettore();

// Conta quanti volontari ci sono per ogni fascia oraria
$query = "SELECT fascia_oraria, COUNT(*) as totale FROM turni_volontariato GROUP BY fascia_oraria";
$risultato = $conn->query($query);

$slot_occupati = array();

if ($risultato) {
    while ($riga = $risultato->fetch_assoc()) {
        // Riempiamo l'array usando la data/ora come chiave e il conteggio come valore
        $slot_occupati[$riga['fascia_oraria']] = intval($riga['totale']);
    }
}

echo json_encode($slot_occupati);
$conn->close();
?>